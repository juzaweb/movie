<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\LiveTvsDatatable;
use Juzaweb\Modules\Movie\Http\Requests\BulkActionsRequest;
use Juzaweb\Modules\Movie\Http\Requests\LiveTvRequest;
use Juzaweb\Modules\Movie\Models\LiveTv;

class LiveTvController extends AdminController
{
    public function index(LiveTvsDatatable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.live_tvs'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->render(
            'movie::live-tv.index',
            compact('createUrl')
        );
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.live_tvs'), admin_url('live-tvs'));
        Breadcrumb::add(__('movie::translation.create_live_tv'));

        $backUrl = action([static::class, 'index']);
        $locale = $this->getFormLanguage();

        return view(
            'movie::live-tv.form',
            [
                'model' => new LiveTv(),
                'action' => action([static::class, 'store']),
                'backUrl' => $backUrl,
                'locale' => $locale,
            ]
        );
    }

    public function edit(string $id)
    {
        Breadcrumb::add(__('movie::translation.live_tvs'), admin_url('live-tvs'));
        Breadcrumb::add(__('movie::translation.edit_live_tv'));

        $locale = $this->getFormLanguage();
        $model = LiveTv::withTranslation($locale)->findOrFail($id);
        $model->setDefaultLocale($locale);
        $backUrl = action([static::class, 'index']);

        return view(
            'movie::live-tv.form',
            [
                'action' => action([static::class, 'update'], [$id]),
                'model' => $model,
                'backUrl' => $backUrl,
                'locale' => $locale,
            ]
        );
    }

    public function store(LiveTvRequest $request)
    {
        $locale = $this->getFormLanguage();
        $model = DB::transaction(
            function () use ($request, $locale) {
                $data = $request->validated();
                $liveTv = new LiveTv($data);
                $liveTv->setDefaultLocale($locale);
                $liveTv->save();

                $liveTv->setThumbnail($request->input('thumbnail'));

                return $liveTv;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('movie::translation.live_tv_name_created_successfully', ['name' => $model->name]),
        ]);
    }

    public function update(LiveTvRequest $request, string $id)
    {
        $locale = $this->getFormLanguage();
        $model = LiveTv::findOrFail($id);
        $model->setDefaultLocale($locale);

        $model = DB::transaction(
            function () use ($request, $model) {
                $data = $request->validated();
                $model->update($data);

                $model->setThumbnail($request->input('thumbnail'));

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('movie::translation.live_tv_name_updated_successfully', ['name' => $model->name]),
        ]);
    }

    public function bulk(BulkActionsRequest $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = LiveTv::whereIn('id', $ids)->get();

        foreach ($models as $model) {
            if ($action === 'delete') {
                $model->delete();
            }
        }

        return $this->success([
            'message' => __('movie::translation.bulk_action_performed_successfully'),
        ]);
    }
}
