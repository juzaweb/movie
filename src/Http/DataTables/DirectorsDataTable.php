<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\Director;

class DirectorsDataTable extends DataTable
{
    protected string $actionUrl = 'movie-directors/bulk';

    public function query(Director $model): QueryBuilder
    {
        return $model->newQuery()
            ->withTranslation()
            ->with(['media'])
            ->filter(request()->all());
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::editLink('name', admin_url('movie-directors/{id}/edit'), __('movie::translation.name')),
            Column::createdAt(),
            Column::actions(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('movie-directors.delete'),
            BulkAction::make(__('core::translation.translate'), null, 'fas fa-language')
                ->type('url')
                ->action('translate')
                ->can('movie-directors.edit'),
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("movie-directors/{$model->id}/edit"))
                ->can('movie-directors.edit'),
            Action::delete()
                ->can('movie-directors.delete'),
        ];
    }
}
