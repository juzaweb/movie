<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Enums\ReportStatus;
use Juzaweb\Modules\Movie\Http\DataTables\ReportsDataTable;
use Juzaweb\Modules\Movie\Http\Requests\ReportActionsRequest;
use Juzaweb\Modules\Movie\Models\Report;

class ReportController extends AdminController
{
    public function index(ReportsDataTable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.reports'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->render(
            'movie::report.index',
            compact('createUrl')
        );
    }

    public function edit(string $id)
    {
        Breadcrumb::add(__('movie::translation.reports'), admin_url('reports'));

        Breadcrumb::add(__('movie::translation.create_reports'));

        $model = Report::query()->findOrFail($id);
        $backUrl = action([static::class, 'index']);

        return view(
            'movie::report.form',
            [
                'action' => action([static::class, 'update'], [$id]),
                'model' => $model,
                'backUrl' => $backUrl,
            ]
        );
    }

    public function bulk(ReportActionsRequest $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        switch ($action) {
            case 'delete':
                Report::destroy($ids);
                break;
            case ReportStatus::PROCESSED->value:
                Report::whereIn('id', $ids)->update(['status' => ReportStatus::PROCESSED]);
                break;
            case ReportStatus::PENDING->value:
                Report::whereIn('id', $ids)->update(['status' => ReportStatus::PENDING]);
                break;
        }

        return $this->success([
            'message' => __('movie::translation.bulk_action_performed_successfully'),
        ]);
    }
}
