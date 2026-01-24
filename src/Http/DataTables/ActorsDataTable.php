<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\Actor;

class ActorsDataTable extends DataTable
{
    protected string $actionUrl = 'movie-actors/bulk';

    public function query(Actor $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['media'])
            ->withTranslation()
            ->filter(request()->all());
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::editLink('name', admin_url('movie-actors/{id}/edit'), __('movie::translation.name')),
            Column::createdAt(),
            Column::actions(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('movie-actors.delete'),
            BulkAction::make(__('core::translation.translate'), null, 'fas fa-language')
                ->type('url')
                ->action('translate')
                ->can('movie-actors.edit'),
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("movie-actors/{$model->id}/edit"))
                ->can('movie-actors.edit'),
            Action::delete()
                ->can('movie-actors.delete'),
        ];
    }
}
