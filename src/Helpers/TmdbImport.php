<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/juzacms
 * @author     Juzaweb Team <admin@juzaweb.com>
 * @link       https://juzaweb.com
 * @license    MIT
 */

namespace Juzaweb\Movie\Helpers;

use Illuminate\Support\Arr;
use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Models\User;

class TmdbImport
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function import(string $tmdbId, int $type): bool|Post
    {
        if (empty(get_config('tmdb_api_key'))) {
            throw new \Exception(trans('mymo::app.tmdb_api_key_not_found'));
        }

        $data = $this->getMovieById($tmdbId, $type);

        if (empty($data)) {
            throw new \Exception(trans('mymo::app.movie_not_found'));
        }

        $data['tmdb_id'] = $tmdbId;

        $import = new ImportMovie($data);

        if (!$import->validate()) {
            throw new \Exception($import->errors[0]);
        }

        $user = User::whereIsAdmin(1)->first();

        return $import->save($user);
    }

    protected function getMovieById($tmdb_id, $type): bool|array
    {
        if ($type == 2) {
            return $this->getTVShow($tmdb_id);
        }

        return $this->getMovie($tmdb_id);
    }

    protected function getMovie($tmdb_id): bool|array
    {
        $api = new TmdbApi();
        $api->setAPIKey(get_config('tmdb_api_key'));
        $data = $api->getMovie($tmdb_id);

        if (empty($data)) {
            return false;
        }

        $actors = $data['credits']['cast'];
        $directors = $data['credits']['crew'];
        $writers = $data['credits']['crew'];
        $countries = $data['production_countries'] ?? [];
        $genres = $data['genres'] ?? [];
        $trailer = $data['trailers']['youtube'][0]['source'] ?? '';
        if ($trailer) {
            $trailer = 'https://www.youtube.com/watch?v=' . $trailer;
        }

        return [
            'title' => $data['title'],
            'origin_title' => $data['original_title'],
            'tv_series' => 0,
            'content' => $data['overview'],
            'thumbnail' => 'https://image.tmdb.org/t/p/w185/'.$data['poster_path'],
            'poster' => 'https://image.tmdb.org/t/p/w780/'.$data['backdrop_path'],
            'rating' => $data['vote_average'],
            'release' => $data['release_date'],
            'trailer_link' => $trailer,
            'runtime' => @$data['runtime'] . ' ' . trans('mymo::app.min'),
            'actors' => $actors,
            'directors' => $directors,
            'writers' => $writers,
            'countries' => $countries,
            'genres' => $genres,
        ];
    }

    protected function getTVShow($tmdb_id): bool|array
    {
        $api = new TmdbApi();
        $api->setAPIKey(get_config('tmdb_api_key'));
        $data = $api->getTVShow($tmdb_id);

        if (empty($data)) {
            return false;
        }

        $actors = $data['credits']['cast'];
        $directors = $data['credits']['crew'];
        $writers = $data['credits']['crew'];
        $countries = $data['production_countries'] ?? [];
        $genres = $data['genres'] ?? [];
        $runtime = Arr::get($data, 'episode_run_time.0');

        $trailer = $data['trailers']['youtube'][0]['source'] ?? '';
        if ($trailer) {
            $trailer = 'https://www.youtube.com/watch?v=' . $trailer;
        }

        return [
            'title' => $data['original_name'],
            'tv_series' => 1,
            'content' => $data['overview'],
            'thumbnail' => 'https://image.tmdb.org/t/p/w185/'.$data['poster_path'],
            'poster' => 'https://image.tmdb.org/t/p/w780/'.$data['backdrop_path'],
            'rating' => $data['vote_average'],
            'release' => $data['first_air_date'],
            'runtime' => $runtime ? $runtime.' '.trans('mymo::app.min') : null,
            'max_episode' => Arr::get($data, 'number_of_episodes', 1),
            'current_episode' => Arr::get($data, 'last_episode_to_air.episode_number', 1),
            'actors' => $actors,
            'directors' => $directors,
            'writers' => $writers,
            'countries' => $countries,
            'genres' => $genres,
            'trailer_link' => $trailer,
        ];
    }
}
