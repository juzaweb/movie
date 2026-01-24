<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Enums\PostStatus;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\MoviesDatatable;
use Juzaweb\Modules\Movie\Http\Requests\BulkActionsRequest;
use Juzaweb\Modules\Movie\Http\Requests\MovieRequest;
use Juzaweb\Modules\Movie\Models\Country;
use Juzaweb\Modules\Movie\Models\Genre;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Models\Year;
use Juzaweb\Modules\Movie\Services\ImportService;

class MovieController extends AdminController
{
    public function __construct(protected ImportService $importService) {}

    public function index(MoviesDatatable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.movies'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->render(
            'movie::movie.index',
            compact('createUrl'),
            [
                'translateModel' => Movie::class,
            ]
        );
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.movies'), admin_url('movies'));
        Breadcrumb::add(__('movie::translation.create_movie'));

        $backUrl = action([static::class, 'index']);
        $locale = $this->getFormLanguage();

        return view(
            'movie::movie.form',
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
        Breadcrumb::add(__('movie::translation.movies'), admin_url('movies'));
        Breadcrumb::add(__('movie::translation.edit_movie'));

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
            'movie::movie.form',
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
                $data['is_tv_series'] = false;

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

                $movie = new Movie($data);
                $movie->setDefaultLocale($locale);
                $movie->save();

                $movie->setThumbnail($request->input('thumbnail'));
                $movie->attachOrUpdateMedia($request->input('poster'), 'poster');

                // Sync relationships
                $movie->genres()->sync($relationships['genres']);
                $movie->countries()->sync($relationships['countries']);
                $movie->syncActors($relationships['actors']);
                $movie->syncDirectors($relationships['directors']);
                $movie->syncWriters($relationships['writers']);

                if ($movie->year) {
                    Year::firstOrCreate(['name' => $movie->year]);
                }

                return $movie;
            }
        );

        return $this->success([
            'redirect' => route('admin.movie-servers.index', [$model->id]),
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
            $this->validateError(
                'tmdb_id',
                __('movie::translation.tmdb_id_required')
            );
        }

        $apiKey = setting('tmdb_api_key');
        if (!$apiKey) {
            return $this->error(__('movie::translation.tmdb_api_key_not_configured'));
        }

        try {
            $movie = $this->importService->import($tmdbId, 'movie', PostStatus::DRAFT);
        } catch (RequestException $e) {
            if (is_json($e->response->body())) {
                $errorData = json_decode($e->response->body(), true);
                return $this->error($errorData['status_message'] ?? __('movie::translation.movie_not_found_in_tmdb'));
            }

            return $this->error($e->getMessage());
        }

        return $this->success([
            'redirect' => action([static::class, 'edit'], [$movie->id]),
            'message' => __('movie::translation.movie_imported_and_created_successfully', ['name' => $movie->name]),
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
