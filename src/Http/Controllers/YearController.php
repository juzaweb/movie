<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\YearsDataTable;
use Juzaweb\Modules\Movie\Http\Requests\YearRequest;
use Juzaweb\Modules\Movie\Models\Year;

class YearController extends AdminController
{
    public function index(YearsDataTable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.movie_years'));

        return $dataTable->render('movie::admin.year.index');
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.movie_years'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.create_new_year'));

        return view(
            'movie::admin.year.form',
            [
                'model' => new Year(),
                'action' => action([self::class, 'store']),
            ]
        );
    }

    public function edit(string $id)
    {
        $model = Year::findOrFail($id);

        Breadcrumb::add(__('movie::translation.movie_years'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.edit_year_name', ['name' => $model->name]));

        return view(
            'movie::admin.year.form',
            [
                'model' => $model,
                'action' => action([self::class, 'update'], [$id]),
            ]
        );
    }

    public function store(YearRequest $request)
    {
        $data = $request->validated();

        $year = DB::transaction(fn() => Year::create($data));

        return $this->success(
            [
                'message' => __('movie::translation.year_created_successfully'),
                // 'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function update(YearRequest $request, string $id)
    {
        $data = $request->validated();
        $year = Year::findOrFail($id);

        DB::transaction(fn() => $year->update($data));

        return $this->success(
            [
                'message' => __('movie::translation.year_updated_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if ($action == 'delete') {
            Year::whereIn('id', $ids)
                ->get()
                ->each
                ->delete();
        }

        return $this->success(
            [
                'message' => __('movie::translation.year_updated_successfully'),
            ]
        );
    }
}
