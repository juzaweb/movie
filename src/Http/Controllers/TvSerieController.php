<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Enums\PostStatus;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\FileManager\MediaUploader;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\MoviesDatatable;
use Juzaweb\Modules\Movie\Http\Requests\BulkActionsRequest;
use Juzaweb\Modules\Movie\Http\Requests\MovieRequest;
use Juzaweb\Modules\Movie\Models\Actor;
use Juzaweb\Modules\Movie\Models\Country;
use Juzaweb\Modules\Movie\Models\Director;
use Juzaweb\Modules\Movie\Models\Genre;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Models\Year;
use Juzaweb\Modules\Movie\Services\Tmdb;

class TvSerieController extends AdminController
{
    public function index(MoviesDatatable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.tv_series'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->showTvSeries()->render(
            'movie::tv-serie.index',
            compact('createUrl')
        );
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.tv_series'), admin_url('tv-series'));
        Breadcrumb::add(__('movie::translation.create_movie'));

        $backUrl = action([static::class, 'index']);
        $locale = $this->getFormLanguage();

        return view(
            'movie::tv-serie.form',
            array_merge([
                'model' => new Movie,
                'action' => action([static::class, 'store']),
                'backUrl' => $backUrl,
                'locale' => $locale,
            ], $this->getFormRelationships())
        );
    }

    public function edit(string $id)
    {
        Breadcrumb::add(__('movie::translation.tv_series'), admin_url('tv-series'));

        Breadcrumb::add(__('movie::translation.edit_tv_serie'));

        $locale = $this->getFormLanguage();
        $model = Movie::withTranslation($locale)->findOrFail($id);
        $model->setDefaultLocale($locale);
        $model->loadMissing([
            'genres' => fn($q) => $q->withTranslation($locale),
            'countries' => fn($q) => $q->withTranslation($locale),
            'actors' => fn($q) => $q->withTranslation($locale),
            'directors' => fn($q) => $q->withTranslation($locale),
            'writers' => fn($q) => $q->withTranslation($locale),
        ]);
        $backUrl = action([static::class, 'index']);

        return view(
            'movie::tv-serie.form',
            array_merge([
                'action' => action([static::class, 'update'], [$id]),
                'model' => $model,
                'backUrl' => $backUrl,
                'locale' => $locale,
            ], $this->getFormRelationships())
        );
    }

    public function store(MovieRequest $request)
    {
        $locale = $this->getFormLanguage();
        $model = DB::transaction(
            function () use ($request, $locale) {
                $data = $request->validated();
                $data['is_tv_series'] = true;

                // Extract relationship data
                $relationships = [
                    'genres' => $data['genres'] ?? [],
                    'countries' => $data['countries'] ?? [],
                    'actors' => $data['actors'] ?? [],
                    'directors' => $data['directors'] ?? [],
                    'writers' => $data['writers'] ?? [],
                ];

                // Remove relationship data from main data
                $data = collect($data)->except(['genres', 'countries', 'actors', 'directors', 'writers'])
                    ->toArray();

                $model = new Movie($data);
                $model->setDefaultLocale($locale);
                $model->save();

                $model->setThumbnail($request->input('thumbnail'));
                $model->attachOrUpdateMedia($request->input('poster'), 'poster');

                // Sync relationships
                $model->genres()->sync($relationships['genres']);
                $model->countries()->sync($relationships['countries']);
                $model->syncActors($relationships['actors']);
                $model->syncDirectors($relationships['directors']);
                $model->syncWriters($relationships['writers']);

                if ($model->year) {
                    Year::firstOrCreate(['name' => $model->year]);
                }

                return $model;
            }
        );

        return $this->success([
            'redirect' => route('admin.tvserie-servers.index', [$model->id]),
            'message' => __('movie::translation.movie_name_created_successfully', ['name' => $model->name]),
        ]);
    }

    public function update(MovieRequest $request, string $id)
    {
        $locale = $this->getFormLanguage();
        $model = Movie::findOrFail($id);
        $model->setDefaultLocale($locale);

        $model = DB::transaction(
            function () use ($request, $model) {
                $data = $request->validated();

                // Extract relationship data
                $relationships = [
                    'genres' => $data['genres'] ?? [],
                    'countries' => $data['countries'] ?? [],
                    'actors' => $data['actors'] ?? [],
                    'directors' => $data['directors'] ?? [],
                    'writers' => $data['writers'] ?? [],
                ];

                // Remove relationship data from main data
                $data = collect($data)->except(['genres', 'countries', 'actors', 'directors', 'writers'])
                    ->toArray();

                $model->update($data);
                $model->setThumbnail($request->input('thumbnail'));
                $model->attachOrUpdateMedia($request->input('poster'), 'poster');

                if (! $model->wasChanged()) {
                    $model->touch();
                }

                // Sync relationships
                $model->genres()->sync($relationships['genres']);
                $model->countries()->sync($relationships['countries']);
                $model->syncActors($relationships['actors']);
                $model->syncDirectors($relationships['directors']);
                $model->syncWriters($relationships['writers']);

                if ($model->year) {
                    Year::firstOrCreate(['name' => $model->year]);
                }

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('movie::translation.movie_name_updated_successfully', ['name' => $model->name]),
        ]);
    }

    public function bulk(BulkActionsRequest $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = Movie::whereIn('id', $ids)->get();

        foreach ($models as $model) {
            if ($action === 'delete') {
                $model->delete();
            }
        }

        return $this->success([
            'message' => __('movie::translation.bulk_action_performed_successfully'),
        ]);
    }

    public function importFromTmdb(Request $request)
    {
        $tmdbId = $request->input('tmdb_id');

        if (!$tmdbId) {
            return $this->error(__('movie::translation.tmdb_id_required'));
        }

        $apiKey = setting('tmdb_api_key');
        if (!$apiKey) {
            return $this->error(__('movie::translation.tmdb_api_key_not_configured'));
        }

        try {
            $tmdb = new Tmdb();
            $tvData = $tmdb->getTVShow($tmdbId);
        } catch (Exception $e) {
            return $this->error(__('movie::translation.import_error') . ': ' . $e->getMessage());
        }

        $locale = $this->getFormLanguage();

        $tvSerie = DB::transaction(function () use ($tvData, $tmdb, $locale) {
            $originalTitle = $movieData['original_name'] ?? null;
            // Prepare TV series data
            $data = [
                'origin_name' => $tvData['name'] != $originalTitle ? $originalTitle : null,
                'tmdb_rating' => $tvData['vote_average'] ? round($tvData['vote_average'] / 2, 2) : null,
                'release' => $tvData['first_air_date'] ?? null,
                'year' => $tvData['first_air_date'] ? date('Y', strtotime($tvData['first_air_date'])) : null,
                'runtime' => $tvData['episode_run_time'][0] ?? null,
                'max_episode' => $tvData['number_of_episodes'] ?? null,
                'trailer_link' => isset($tvData['videos']['results'][0]) ? 'https://www.youtube.com/watch?v=' . $tvData['videos']['results'][0]['key'] : null,
                'is_tv_series' => true,
                'status' => PostStatus::DRAFT,
                'video_quality' => 'HD',
            ];

            // Create TV series
            $tvSerie = new Movie($data);
            $tvSerie->setDefaultLocale($locale);
            $tvSerie->name = $tvData['name'] ?? '';
            $tvSerie->content = $tvData['overview'] ?? '';
            $tvSerie->save();

            // Set images
            if (isset($tvData['poster_path']) && $tvData['poster_path']) {
                $posterUrl = $tmdb->getImageURL('w500') . $tvData['poster_path'];

                $media = MediaUploader::make($posterUrl)->upload();

                $tvSerie->setThumbnail($media);
            }

            if (isset($tvData['backdrop_path']) && $tvData['backdrop_path']) {
                $backdropUrl = $tmdb->getImageURL('w780') . $tvData['backdrop_path'];

                $media = MediaUploader::make($backdropUrl)->upload();

                $tvSerie->attachOrUpdateMedia($media, 'poster');
            }

            // Handle genres
            $genreIds = [];
            foreach ($tvData['genres'] ?? [] as $genreData) {
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
            $tvSerie->genres()->sync($genreIds);

            // Handle countries
            $countryIds = [];
            foreach ($tvData['production_countries'] ?? [] as $countryData) {
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
            $tvSerie->countries()->sync($countryIds);

            // Handle actors
            $actorIds = [];
            foreach (array_slice($tvData['credits']['cast'] ?? [], 0, 10) as $castData) {
                $actor = Actor::whereHas('translations', function ($query) use ($castData, $locale) {
                    $query->where('name', $castData['name'])->where('locale', $locale);
                })->first();

                if (!$actor) {
                    $actor = new Actor();
                    $actor->setDefaultLocale($locale);
                    $actor->name = $castData['name'];
                    $actor->save();
                }
                $actorIds[] = $actor->id;
            }
            $tvSerie->actors()->sync($actorIds);

            // Handle directors
            $directorIds = [];
            foreach ($tvData['credits']['crew'] ?? [] as $crewData) {
                if (($crewData['job'] ?? '') === 'Director') {
                    $director = Director::whereHas('translations', function ($query) use ($crewData, $locale) {
                        $query->where('name', $crewData['name'])->where('locale', $locale);
                    })->first();

                    if (!$director) {
                        $director = new Director();
                        $director->setDefaultLocale($locale);
                        $director->name = $crewData['name'];
                        $director->save();
                    }
                    $directorIds[] = $director->id;
                }
            }
            $tvSerie->directors()->sync($directorIds);

            // Create year if needed
            if ($tvSerie->year) {
                Year::firstOrCreate(['name' => $tvSerie->year]);
            }

            return $tvSerie;
        });

        return $this->success([
            'redirect' => action([static::class, 'edit'], [$tvSerie->id]),
            'message' => __('movie::translation.tvserie_imported_and_created_successfully', ['name' => $tvSerie->name]),
        ]);
    }

    protected function getFormRelationships(): array
    {
        return [
            'genres' => Genre::withTranslation()->get(),
            'countries' => Country::withTranslation()->get(),
        ];
    }
}
