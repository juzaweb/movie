<?php

namespace Juzaweb\Modules\Movie\Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Juzaweb\Modules\Movie\Tests\TestCase;
use Juzaweb\Modules\Movie\Services\ImportService;
use Juzaweb\Modules\Movie\Services\Tmdb;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Core\Enums\PostStatus;
use Mockery;

class ImportServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_import_movie_success()
    {
        $tmdbId = '12345';
        $movieData = [
            'id' => 12345,
            'title' => 'Test Movie',
            'original_title' => 'Test Original Title',
            'release_date' => '2023-01-01',
            'runtime' => 120,
            'vote_average' => 8.5,
            'overview' => 'This is a test movie description.',
            'poster_path' => '/poster.jpg',
            'backdrop_path' => '/backdrop.jpg',
            'genres' => [
                ['id' => 1, 'name' => 'Action'],
                ['id' => 2, 'name' => 'Drama'],
            ],
            'production_countries' => [
                ['iso_3166_1' => 'US', 'name' => 'United States of America'],
            ],
            'credits' => [
                'cast' => [
                    ['name' => 'Actor One'],
                    ['name' => 'Actor Two'],
                ],
                'crew' => [
                    ['name' => 'Director One', 'job' => 'Director'],
                ],
            ],
            'videos' => [
                'results' => [
                    ['key' => 'trailer_key'],
                ],
            ],
        ];

        $tmdbMock = Mockery::mock(Tmdb::class);
        $tmdbMock->shouldReceive('getMovie')
            ->with($tmdbId)
            ->once()
            ->andReturn($movieData);
        $tmdbMock->shouldReceive('getImageURL')->andReturn('http://image.tmdb.org/t/p/');

        $service = new TestImportService($tmdbMock);
        $movie = $service->import($tmdbId, 'movie');

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals('Test Movie', $movie->name);
        $this->assertEquals('Test Original Title', $movie->origin_name);
        $this->assertEquals('This is a test movie description.', $movie->content);
        $this->assertEquals('2023-01-01 00:00:00', $movie->release);
        $this->assertEquals(120, $movie->runtime);
        $this->assertEquals(4.25, $movie->tmdb_rating); // 8.5 / 2
        $this->assertEquals('https://www.youtube.com/watch?v=trailer_key', $movie->trailer_link);
        $this->assertEquals(0, $movie->is_tv_series);
        $this->assertEquals(PostStatus::PUBLISHED, $movie->status);

        $this->assertTrue($service->imagesProcessed);

        $this->assertDatabaseHas('movie_genre_translations', [
            'name' => 'Action',
        ]);
    }

    public function test_import_tv_show_success()
    {
        $tmdbId = '67890';
        $tvData = [
            'id' => 67890,
            'name' => 'Test TV Show',
            'original_name' => 'Test Original TV Show',
            'first_air_date' => '2022-05-01',
            'episode_run_time' => [45],
            'vote_average' => 7.0,
            'overview' => 'This is a test TV show.',
            'poster_path' => '/tv_poster.jpg',
            'backdrop_path' => '/tv_backdrop.jpg',
            'genres' => [
                ['id' => 3, 'name' => 'Comedy'],
            ],
            'production_countries' => [],
            'credits' => [
                'cast' => [],
                'crew' => [],
            ],
            'created_by' => [
                 ['name' => 'Creator Name'],
            ],
            'videos' => ['results' => []],
        ];

        $tmdbMock = Mockery::mock(Tmdb::class);
        $tmdbMock->shouldReceive('getTVShow')
            ->with($tmdbId)
            ->once()
            ->andReturn($tvData);
        $tmdbMock->shouldReceive('getImageURL')->andReturn('http://image.tmdb.org/t/p/');

        $service = new TestImportService($tmdbMock);
        $movie = $service->import($tmdbId, 'tv');

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals('Test TV Show', $movie->name);
        $this->assertEquals(1, $movie->is_tv_series);
        $this->assertEquals(3.5, $movie->tmdb_rating);
        $this->assertEquals(45, $movie->runtime);
    }

    public function test_import_updates_existing_movie()
    {
        // Create existing movie
        $existingMovie = new Movie();
        $existingMovie->fill([
            'is_tv_series' => 0,
            'status' => PostStatus::PUBLISHED,
        ]);
        $existingMovie->setDefaultLocale('en');
        $existingMovie->name = 'Existing Movie';
        $existingMovie->save();

        $tmdbId = '11111';
        $movieData = [
            'id' => 11111,
            'title' => 'Existing Movie', // Matches existing name
            'original_title' => 'Updated Original Title',
            'release_date' => '2023-01-01',
            'runtime' => 100,
            'vote_average' => 6.0,
            'overview' => 'Updated description.',
            'videos' => ['results' => []],
        ];

        $tmdbMock = Mockery::mock(Tmdb::class);
        $tmdbMock->shouldReceive('getMovie')->andReturn($movieData);
        $tmdbMock->shouldReceive('getImageURL')->andReturn('url');

        $service = new TestImportService($tmdbMock);
        $updatedMovie = $service->import($tmdbId, 'movie');

        $this->assertEquals($existingMovie->id, $updatedMovie->id);
        $this->assertEquals('Updated Original Title', $updatedMovie->origin_name);
        $this->assertEquals('Updated description.', $updatedMovie->content);
    }
}

class TestImportService extends ImportService
{
    protected $mockTmdb;
    public $imagesProcessed = false;

    public function __construct($mockTmdb)
    {
        $this->mockTmdb = $mockTmdb;
    }

    protected function tmdb()
    {
        return $this->mockTmdb;
    }

    protected function processImages(Movie $movie, array $data): void
    {
        $this->imagesProcessed = true;
    }
}
