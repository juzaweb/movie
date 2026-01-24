<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\Year;

class YearsDataTable extends DataTable
{
    protected string $actionUrl = 'movie-years/bulk';

    public function query(Year $model): QueryBuilder
    {
        return $model->newQuery()
            ->filter(request()->all());
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::editLink('name', admin_url('movie-years/{id}/edit'), __('movie::translation.name')),
            Column::createdAt(),
            Column::actions(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('movie-years.delete'),
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("movie-years/{$model->id}/edit"))
                ->can('movie-years.edit'),
            Action::delete()
                ->can('movie-years.delete'),
        ];
    }
}
