<?php

namespace Juzaweb\Modules\Movie\Services;

use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Enums\PostStatus;
use Juzaweb\Modules\Core\FileManager\MediaUploader;
use Juzaweb\Modules\Movie\Models\Country;
use Juzaweb\Modules\Movie\Models\Genre;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Models\Year;

class ImportService
{
    protected Tmdb $tmdb;

    public function import(string $tmdbId, string $type = 'movie', PostStatus $status = PostStatus::PUBLISHED): Movie
    {
        if ($type === 'tv') {
            $data = $this->tmdb()->getTVShow($tmdbId);
        } else {
            $data = $this->tmdb()->getMovie($tmdbId);
        }

        return DB::transaction(fn () => $this->save($data, $type, $status));
    }

    protected function save(array $data, string $type, PostStatus $status): Movie
    {
        $isTvSeries = $type === 'tv';
        $locale = default_language();

        // Map fields based on type
        $title = $isTvSeries ? ($data['name'] ?? '') : ($data['title'] ?? '');
        $originalTitle = $isTvSeries ? ($data['original_name'] ?? '') : ($data['original_title'] ?? '');
        $releaseDate = $isTvSeries ? ($data['first_air_date'] ?? null) : ($data['release_date'] ?? null);
        $runtime = $isTvSeries ? ($data['episode_run_time'][0] ?? null) : ($data['runtime'] ?? null);

        // Check for existing movie/tv show by name AND is_tv_series to prevent duplicates/confusion
        $movie = Movie::whereTranslation('name', $title)
            ->where('is_tv_series', $isTvSeries)
            ->first();

        if (!$movie) {
             $movie = new Movie();
        }

        $movieData = [
            'origin_name' => $originalTitle,
            'tmdb_rating' => $data['vote_average'] ? round($data['vote_average'] / 2, 2) : null,
            'release' => $releaseDate,
            'year' => $releaseDate ? date('Y', strtotime($releaseDate)) : null,
            'runtime' => $runtime,
            'trailer_link' => isset($data['videos']['results'][0]) ? 'https://www.youtube.com/watch?v=' . $data['videos']['results'][0]['key'] : null,
            'is_tv_series' => $isTvSeries,
            'status' => $status,
            'video_quality' => 'HD',
        ];

        $movie->fill($movieData);
        $movie->setDefaultLocale($locale);
        $movie->name = $title;
        $movie->content = $data['overview'] ?? '';
        $movie->save();

        $this->processImages($movie, $data);
        $this->processTaxonomies($movie, $data, $locale);
        $this->processCredits($movie, $data);

        return $movie;
    }

    protected function processImages(Movie $movie, array $data): void
    {
        if (isset($data['poster_path']) && $data['poster_path']) {
            $posterUrl = $this->tmdb()->getImageURL('w500') . $data['poster_path'];
            try {
                $media = MediaUploader::make($posterUrl)->upload();
                $movie->setThumbnail($media);
            } catch (\Exception $e) {
                // report($e);
            }
        }

        if (isset($data['backdrop_path']) && $data['backdrop_path']) {
            $backdropUrl = $this->tmdb()->getImageURL('w780') . $data['backdrop_path'];
            try {
                $media = MediaUploader::make($backdropUrl)->upload();
                $movie->attachOrUpdateMedia($media, 'poster');
            } catch (\Exception $e) {
                // report($e);
            }
        }
    }

    protected function processTaxonomies(Movie $movie, array $data, string $locale): void
    {
        // Genres
        $genreIds = [];
        foreach ($data['genres'] ?? [] as $genreData) {
            $genre = Genre::whereHas('translations', function ($query) use ($genreData, $locale) {
                $query->where('name', $genreData['name'])->where('locale', $locale);
            })->first();

            if (!$genre) {
                $genre = new Genre();
                $genre->setDefaultLocale($locale);
                $genre->name = $genreData['name'];
                $genre->save();
            }
            $genreIds[] = $genre->id;
        }
        $movie->genres()->sync($genreIds);

        // Countries
        $countryIds = [];
        // TMDB returns 'production_countries' for movies.
        // For TV shows, it's often 'origin_country' (codes) or 'production_countries' (details).
        // Let's rely on 'production_countries' if available, otherwise check 'origin_country' if it was a list of codes we'd need to map, but `getTVShow` typically returns full details.
        // If 'production_countries' is missing/empty for TV, we might need to handle 'origin_country' codes, but for now assuming details are there or empty is fine for MVP.
        foreach ($data['production_countries'] ?? [] as $countryData) {
            $country = Country::whereHas('translations', function ($query) use ($countryData, $locale) {
                $query->where('name', $countryData['name'])->where('locale', $locale);
            })->first();

            if (!$country) {
                $country = new Country();
                $country->setDefaultLocale($locale);
                $country->name = $countryData['name'];
                $country->save();
            }
            $countryIds[] = $country->id;
        }
        $movie->countries()->sync($countryIds);

        // Year
        if ($movie->year) {
            Year::firstOrCreate(['name' => $movie->year]);
        }
    }

    protected function processCredits(Movie $movie, array $data): void
    {
        // Actors
        $castNames = collect(array_slice($data['credits']['cast'] ?? [], 0, 10))
            ->pluck('name')
            ->toArray();
        $movie->syncActors($castNames);

        // Directors
        $directorNames = collect($data['credits']['crew'] ?? [])
            ->filter(fn ($crew) => ($crew['job'] ?? '') === 'Director')
            ->pluck('name')
            ->toArray();

        if (empty($directorNames) && !empty($data['created_by'])) {
            $directorNames = collect($data['created_by'])->pluck('name')->toArray();
        }

        $movie->syncDirectors($directorNames);
    }

    protected function tmdb()
    {
        if (!isset($this->tmdb)) {
            $this->tmdb = new Tmdb();
        }

        return $this->tmdb;
    }
}
