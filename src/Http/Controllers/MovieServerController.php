<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\ServersDatatable;
use Juzaweb\Modules\Movie\Http\Requests\BulkActionsRequest;
use Juzaweb\Modules\Movie\Http\Requests\ServerRequest;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Models\Server;

class MovieServerController extends AdminController
{
    public function index(ServersDatatable $dataTable, string $movieId)
    {
        $movie = Movie::findOrFail($movieId);

        Breadcrumb::add(__('movie::translation.movies'), admin_url('movies'));
        Breadcrumb::add($movie->name, action([MovieController::class, 'edit'], [$movieId]));
        Breadcrumb::add(__('movie::translation.servers'));

        $createUrl = action([static::class, 'create'], [$movieId]);

        return $dataTable->scopeToServerable($movie)->render(
            'movie::server.index',
            compact('createUrl', 'movie')
        );
    }

    public function create(string $movieId)
    {
        $movie = Movie::findOrFail($movieId);

        Breadcrumb::add(__('movie::translation.movies'), admin_url('movies'));
        Breadcrumb::add($movie->name, action([MovieController::class, 'edit'], [$movieId]));
        Breadcrumb::add(__('movie::translation.servers'), action([static::class, 'index'], [$movieId]));
        Breadcrumb::add(__('movie::translation.create_server'));

        $backUrl = action([static::class, 'index'], [$movieId]);

        return view(
            'movie::server.form',
            [
                'model' => new Server,
                'action' => action([static::class, 'store'], [$movieId]),
                'backUrl' => $backUrl,
                'movie' => $movie,
            ]
        );
    }

    public function edit(string $movieId, string $id)
    {
        $movie = Movie::findOrFail($movieId);
        $model = Server::where('movie_id', $movieId)->findOrFail($id);

        Breadcrumb::add(__('movie::translation.movies'), admin_url('movies'));
        Breadcrumb::add($movie->name, action([MovieController::class, 'edit'], [$movieId]));
        Breadcrumb::add(__('movie::translation.servers'), action([static::class, 'index'], [$movieId]));
        Breadcrumb::add(__('movie::translation.edit_server'));

        $backUrl = action([static::class, 'index'], [$movieId]);

        return view(
            'movie::server.form',
            [
                'action' => action([static::class, 'update'], [$movieId, $id]),
                'model' => $model,
                'backUrl' => $backUrl,
                'movie' => $movie,
            ]
        );
    }

    public function store(ServerRequest $request, string $movieId)
    {
        $movie = Movie::findOrFail($movieId);

        $model = DB::transaction(
            function () use ($request, $movie) {
                $data = $request->validated();

                return $movie->servers()->create($data);
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index'], [$movieId]),
            'message' => __('movie::translation.server_name_created_successfully', ['name' => $model->name]),
        ]);
    }

    public function update(ServerRequest $request, string $movieId, string $id)
    {
        $movie = Movie::findOrFail($movieId);
        $model = Server::where('movie_id', $movieId)
            ->findOrFail($id);

        $model = DB::transaction(
            function () use ($request, $model) {
                $data = $request->validated();

                if (empty($data['slug'])) {
                    $data['slug'] = Str::slug($data['name']);
                }

                $model->update($data);

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index'], [$movieId]),
            'message' => __('movie::translation.server_name_updated_successfully', ['name' => $model->name]),
        ]);
    }

    public function bulk(BulkActionsRequest $request, string $movieId)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = Server::where('movie_id', $movieId)
            ->whereIn('id', $ids)
            ->get();

        foreach ($models as $model) {
            if ($action === 'delete') {
                $model->delete();
            }
        }

        return $this->success([
            'message' => __('movie::translation.bulk_action_performed_successfully'),
        ]);
    }
}
