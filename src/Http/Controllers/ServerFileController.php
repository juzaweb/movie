<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\ServerFilesDataTable;
use Juzaweb\Modules\Movie\Http\Requests\ServerFileActionsRequest;
use Juzaweb\Modules\Movie\Http\Requests\ServerFileRequest;
use Juzaweb\Modules\Movie\Models\Server;
use Juzaweb\Modules\Movie\Models\ServerFile;

class ServerFileController extends AdminController
{
    public function index(ServerFilesDataTable $dataTable, string $serverId)
    {
        Breadcrumb::add(__('movie::translation.server_files'));

        $createUrl = action([static::class, 'create'], [$serverId]);
        $server = Server::findOrFail($serverId);

        return $dataTable->withServer($server)->render(
            'movie::server-file.index',
            compact(
                'createUrl',
                'server'
            )
        );
    }

    public function create(string $serverId)
    {
        Breadcrumb::add(__('movie::translation.server_files'), admin_url('serverfiles'));

        Breadcrumb::add(__('movie::translation.create_server_file'));

        $backUrl = action([static::class, 'index'], [$serverId]);

        return view(
            'movie::server-file.form',
            [
                'model' => new ServerFile(),
                'action' => action([static::class, 'store'], [$serverId]),
                'backUrl' => $backUrl,
            ]
        );
    }

    public function edit(string $serverId, string $id)
    {
        Breadcrumb::add(__('movie::translation.server_files'), action([static::class, 'index'], [$serverId]));

        Breadcrumb::add(__('movie::translation.create_server_files'));

        $model = ServerFile::findOrFail($id);
        $backUrl = action([static::class, 'index'], [$serverId]);

        return view(
            'movie::server-file.form',
            [
                'action' => action([static::class, 'update'], [$serverId, $id]),
                'model' => $model,
                'backUrl' => $backUrl,
            ]
        );
    }

    public function store(ServerFileRequest $request, string $serverId)
    {
        $model = DB::transaction(
            function () use ($request, $serverId) {
                $data = $request->validated();
                $data['server_id'] = $serverId;

                return ServerFile::create($data);
            }
        );

        return $this->success([
            'message' => __('movie::translation.serverfile_name_created_successfully', ['name' => $model->name]),
        ]);
    }

    public function update(ServerFileRequest $request, string $serverId, string $id)
    {
        $model = ServerFile::findOrFail($id);

        $model = DB::transaction(
            function () use ($request, $model) {
                $data = $request->validated();

                $model->update($data);

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index'], [$serverId]),
            'message' => __('movie::translation.server_file_name_updated_successfully', ['name' => $model->name]),
        ]);
    }

    public function bulk(ServerFileActionsRequest $request, string $serverId)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = ServerFile::whereIn('id', $ids)
            ->where('server_id', $serverId)
            ->get();

        foreach ($models as $model) {
            if ($action === 'activate') {
                $model->update(['active' => true]);
            }

            if ($action === 'deactivate') {
                $model->update(['active' => false]);
            }

            if ($action === 'delete') {
                $model->delete();
            }
        }

        return $this->success([
            'message' => __('movie::translation.bulk_action_performed_successfully'),
        ]);
    }
}
