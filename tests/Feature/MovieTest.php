<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Tests\TestCase;

class MovieTest extends TestCase
{
    public function test_can_create_movie()
    {
        $movie = Movie::create([
            'name' => 'Test Movie',
            'slug' => 'test-movie',
            'description' => 'Test Description',
            'content' => 'Test Content',
        ]);

        $this->assertDatabaseHas('movies', [
            'id' => $movie->id,
        ]);

        $this->assertDatabaseHas('movie_translations', [
            'name' => 'Test Movie',
            'movie_id' => $movie->id,
        ]);
    }
}
