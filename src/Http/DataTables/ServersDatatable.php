<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Models\Server;
use Yajra\DataTables\EloquentDataTable;

class ServersDatatable extends DataTable
{
    protected string $actionUrl = 'servers/bulk';

    protected Model $movie;

    public function scopeToServerable(Model $movie): self
    {
        $this->movie = $movie;

        // Update actionUrl based on movie type
        $movieId = $movie->id;

        if ($movie instanceof Movie) {
            $this->actionUrl = admin_url("movies/{$movieId}/servers/bulk");
        } else {
            $this->actionUrl = admin_url("tv-series/{$movieId}/servers/bulk");
        }

        return $this;
    }

    public function query(Server $model): Builder
    {
        return $model->newQuery()
            ->with('movie')
            ->withCount('movie')
            ->where('movie_id', $this->movie->id);
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::actions(),
            Column::editLink(
                'name',
                admin_url('movies/{movie_id}/servers/{id}/edit'),
                __('movie::translation.name')
            ),
            Column::make('display_order')->title(__('movie::translation.order')),
            Column::computed('server_files')->title(__('movie::translation.files')),
            Column::computed('active')->title(__('movie::translation.active')),
            Column::createdAt(),
        ];
    }

    public function actions(Model $model): array
    {
        $movieId = $this->movie->id;

        if ($this->movie instanceof Movie) {
            $editUrl = admin_url("movies/{$movieId}/servers/{$model->id}/edit");
        } else {
            $editUrl = admin_url("tv-series/{$movieId}/servers/{$model->id}/edit");
        }

        return [
            Action::edit($editUrl)->can('servers.edit'),
            Action::delete()->can('servers.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('servers.delete'),
        ];
    }

    public function renderColumns(EloquentDataTable $builder): EloquentDataTable
    {
        return parent::renderColumns($builder)
            ->editColumn('active', function (Server $model) {
                return $model->active
                    ? '<span class="badge badge-success">'.__('movie::translation.active').'</span>'
                    : '<span class="badge badge-secondary">'.__('movie::translation.inactive').'</span>';
            })
            ->editColumn('server_files', function (Server $model) {
                $count = $model->server_files_count ?? 0;

                $url = admin_url("servers/{$model->id}/files");

                return '<a href="'.$url.'" class="btn btn-sm btn-info" title="'.__('movie::translation.view_movie_files').'">'
                    .'<i class="fas fa-file-video"></i> '.$count.'</a>';
            })
            ->rawColumns(['active', 'server_files']);
    }
}
