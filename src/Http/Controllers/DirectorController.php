<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\DirectorsDataTable;
use Juzaweb\Modules\Movie\Http\Requests\DirectorRequest;
use Juzaweb\Modules\Movie\Models\Director;

class DirectorController extends AdminController
{
    public function index(DirectorsDataTable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.movie_directors'));

        return $dataTable->render('movie::admin.director.index');
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.movie_directors'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.create_new_director'));

        $locale = $this->getFormLanguage();

        return view(
            'movie::admin.director.form',
            [
                'model' => new Director(),
                'action' => action([self::class, 'store']),
                'locale' => $locale,
            ]
        );
    }

    public function edit(string $id)
    {
        $locale = $this->getFormLanguage();
        $model = Director::withTranslation($locale)->findOrFail($id);
        $model->setDefaultLocale($locale);

        Breadcrumb::add(__('movie::translation.movie_directors'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.edit_director_name', ['name' => $model->name]));

        return view(
            'movie::admin.director.form',
            [
                'model' => $model,
                'action' => action([self::class, 'update'], [$id]),
                'locale' => $locale,
            ]
        );
    }

    public function store(DirectorRequest $request)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();

        $director = DB::transaction(function () use ($data, $locale) {
            $model = new Director();
            $model->setDefaultLocale($locale);
            $model->fill($data);
            $model->save();
            return $model;
        });

        return $this->success(
            [
                'message' => __('movie::translation.director_created_successfully'),
                // 'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function update(DirectorRequest $request, string $id)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();
        $director = Director::findOrFail($id);
        $director->setDefaultLocale($locale);

        DB::transaction(fn() => $director->update($data));

        return $this->success(
            [
                'message' => __('movie::translation.director_updated_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if ($action == 'delete') {
            Director::whereIn('id', $ids)
                ->get()
                ->each
                ->delete();
        }

        return $this->success(
            [
                'message' => __('movie::translation.director_updated_successfully'),
            ]
        );
    }
}
