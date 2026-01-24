<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\Country;

class CountriesDataTable extends DataTable
{
    protected string $actionUrl = 'movie-countries/bulk';

    public function query(Country $model): QueryBuilder
    {
        return $model->newQuery()
            ->withTranslation()
            //->with(['media'])
            ->filter(request()->all());
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::editLink('name', admin_url('movie-countries/{id}/edit'), __('movie::translation.name')),
            Column::createdAt(),
            Column::actions(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('movie-countries.delete'),
            BulkAction::make(__('core::translation.translate'), null, 'fas fa-language')
                ->type('url')
                ->action('translate')
                ->can('movie-countries.edit'),
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("movie-countries/{$model->id}/edit"))
                ->can('movie-countries.edit'),
            Action::delete()
                ->can('movie-countries.delete'),
        ];
    }
}
