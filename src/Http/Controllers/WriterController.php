<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\WritersDataTable;
use Juzaweb\Modules\Movie\Http\Requests\WriterRequest;
use Juzaweb\Modules\Movie\Models\Writer;

class WriterController extends AdminController
{
    public function index(WritersDataTable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.movie_writers'));

        return $dataTable->render('movie::admin.writer.index');
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.movie_writers'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.create_new_writer'));

        $locale = $this->getFormLanguage();

        return view(
            'movie::admin.writer.form',
            [
                'model' => new Writer(),
                'action' => action([self::class, 'store']),
                'locale' => $locale,
            ]
        );
    }

    public function edit(string $id)
    {
        $locale = $this->getFormLanguage();
        $model = Writer::withTranslation($locale)->findOrFail($id);
        $model->setDefaultLocale($locale);

        Breadcrumb::add(__('movie::translation.movie_writers'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.edit_writer_name', ['name' => $model->name]));

        return view(
            'movie::admin.writer.form',
            [
                'model' => $model,
                'action' => action([self::class, 'update'], [$id]),
                'locale' => $locale,
            ]
        );
    }

    public function store(WriterRequest $request)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();

        $writer = DB::transaction(function () use ($data, $locale) {
            $model = new Writer();
            $model->setDefaultLocale($locale);
            $model->fill($data);
            $model->save();
            return $model;
        });

        return $this->success(
            [
                'message' => __('movie::translation.writer_created_successfully'),
                // 'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function update(WriterRequest $request, string $id)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();
        $writer = Writer::findOrFail($id);
        $writer->setDefaultLocale($locale);

        DB::transaction(fn() => $writer->update($data));

        return $this->success(
            [
                'message' => __('movie::translation.writer_updated_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if ($action == 'delete') {
            Writer::whereIn('id', $ids)
                ->get()
                ->each
                ->delete();
        }

        return $this->success(
            [
                'message' => __('movie::translation.writer_updated_successfully'),
            ]
        );
    }

    public function quickStore(WriterRequest $request)
    {
        $data = $request->validated();
        $locale = $this->getFormLanguage();

        $writer = DB::transaction(function () use ($data, $locale) {
            $model = new Writer();
            $model->setDefaultLocale($locale);
            $model->fill($data);
            $model->save();
            return $model;
        });

        return $this->success(
            [
                'message' => __('movie::translation.writer_created_successfully'),
                'data' => [
                    'id' => $writer->id,
                    'name' => $writer->name,
                ],
            ]
        );
    }
}
