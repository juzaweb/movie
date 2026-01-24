<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\ReportType;

class ReportTypesDataTable extends DataTable
{
    protected string $actionUrl = 'report-types/bulk';

    public function query(ReportType $model): Builder
    {
        return $model->newQuery()->withTranslation();
    }

    public function getColumns(): array
    {
        return [
			Column::checkbox(),
            Column::make('name')
                ->title(trans('movie::translation.name'))
                ->orderable(false),
			Column::actions(),
			Column::createdAt()
		];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("report-types/{$model->id}/edit"))->can('report-types.edit'),
            Action::delete()->can('report-types.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('report-types.delete'),
        ];
    }
}
