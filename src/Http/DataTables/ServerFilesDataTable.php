<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\Server;
use Juzaweb\Modules\Movie\Models\ServerFile;

class ServerFilesDataTable extends DataTable
{
    protected string $actionUrl = 'server-files/bulk';

    protected Server $server;

    public function query(ServerFile $model): Builder
    {
        return $model->newQuery()
            ->where('server_id', $this->server->id);
    }

    public function getColumns(): array
    {
        return [
			Column::checkbox(),
			Column::id(),
			Column::actions(),
            Column::make('name'),
            Column::make('source'),
			Column::createdAt(),
		];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("servers/{$this->server->id}/files/{$model->id}/edit"))->can('server-files.edit'),
            Action::delete()->can('server-files.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('server-files.delete'),
        ];
    }

    public function withServer(Server $server)
    {
        $this->server = $server;

        return $this;
    }
}
