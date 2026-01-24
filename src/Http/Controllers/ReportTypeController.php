<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\ReportTypesDataTable;
use Juzaweb\Modules\Movie\Http\Requests\ReportTypeActionsRequest;
use Juzaweb\Modules\Movie\Http\Requests\ReportTypeRequest;
use Juzaweb\Modules\Movie\Models\ReportType;

class ReportTypeController extends AdminController
{
    public function index(ReportTypesDataTable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.report_types'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->render(
            'movie::report-type.index',
            compact('createUrl')
        );
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.report_types'), admin_url('reporttypes'));

        Breadcrumb::add(__('movie::translation.create_report_type'));

        $backUrl = action([static::class, 'index']);

        return view(
            'movie::report-type.form',
            [
                'model' => new ReportType(),
                'action' => action([static::class, 'store']),
                'backUrl' => $backUrl,
            ]
        );
    }

    public function edit(string $id)
    {
        Breadcrumb::add(__('movie::translation.report_types'), admin_url('reporttypes'));

        Breadcrumb::add(__('movie::translation.create_report_types'));

        $model = ReportType::findOrFail($id);
        $backUrl = action([static::class, 'index']);

        return view(
            'movie::report-type.form',
            [
                'action' => action([static::class, 'update'], [$id]),
                'model' => $model,
                'backUrl' => $backUrl,
            ]
        );
    }

    public function store(ReportTypeRequest $request)
    {
        $model = DB::transaction(
            function () use ($request) {
                $data = $request->validated();

                return ReportType::create($data);
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('movie::translation.reporttype_name_created_successfully', ['name' => $model->name]),
        ]);
    }

    public function update(ReportTypeRequest $request, string $id)
    {
        $model = ReportType::findOrFail($id);

        $model = DB::transaction(
            function () use ($request, $model) {
                $data = $request->validated();

                $model->update($data);

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('movie::translation.reporttype_name_updated_successfully', ['name' => $model->name]),
        ]);
    }

    public function bulk(ReportTypeActionsRequest $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = ReportType::whereIn('id', $ids)->get();

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
