<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Enums\ReportStatus;
use Juzaweb\Modules\Movie\Models\Report;
use Juzaweb\Modules\Movie\Models\ServerFile;
use Yajra\DataTables\EloquentDataTable;

class ReportsDataTable extends DataTable
{
    protected string $actionUrl = 'reports/bulk';

    public function query(Report $model): Builder
    {
        return $model->newQuery()->with([
            'reportType' => fn ($q) => $q->withTranslation(),
            'reportable',
        ])
            ->where('reportable_type', ServerFile::class);
    }

    public function getColumns(): array
    {
        return [
			Column::checkbox(),
			Column::id(),
            Column::actions(),
			Column::make('report_type.name')
                ->title(__('movie::translation.report_type')),
            Column::make('reportable')
                ->title(__('movie::translation.reported_video')),
            Column::make('description')
                ->title(__('movie::translation.description'))
                ->width('40%'),
			Column::make('status')
                ->title(__('movie::translation.status'))
                ->width(100),
            Column::createdAt(),
		];
    }

    public function renderColumns(EloquentDataTable $builder): EloquentDataTable
    {
        return parent::renderColumns($builder)
            ->editColumn('status', function (Report $model) {
                return $model->status->badge();
            })
            ->editColumn(
                'reportable',
                function (Report $model) {
                    return '<a href="'. route('admin.server-files.edit', [$model->reportable->server_id, $model->reportable->id]) .'">'. $model->reportable->name .'</a>';
                }
            )
            ->rawColumns(['status', 'actions', 'reportable']);
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("reports/{$model->id}/edit"))->can('reports.edit'),
            Action::delete()->can('reports.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('reports.delete'),
            BulkAction::make(ReportStatus::PROCESSED->value, __('movie::translation.mark_as_processed'))->can('reports.edit'),
            BulkAction::make(ReportStatus::PENDING->value, __('movie::translation.mark_as_pending'))->can('reports.edit'),
        ];
    }
}
