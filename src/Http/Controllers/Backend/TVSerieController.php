<?php

namespace Juzaweb\Movie\Http\Controllers\Backend;

use Illuminate\Support\Facades\Validator;
use Juzaweb\Movie\Http\Datatable\MovieDatatable;

class TVSerieController extends MovieController
{
    protected $viewPrefix = 'mymo::tv_series';

    protected function getDataTable()
    {
        $dataTable = new MovieDatatable();
        $dataTable->mountData(1);
        return $dataTable;
    }

    protected function getTitle()
    {
        return trans('mymo::app.tv_series');
    }

    protected function validator(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'name' => 'required|string|max:250',
            'description' => 'nullable',
            'status' => 'required|in:draft,publish,trash,private',
            'thumbnail' => 'nullable|string|max:250',
            'poster' => 'nullable|string|max:250',
            'rating' => 'nullable|string|max:25',
            'release' => 'nullable|string|max:15',
            'runtime' => 'nullable|string|max:100',
            'video_quality' => 'nullable|string|max:100',
            'trailer_link' => 'nullable|string|max:100',
        ]);

        return $validator;
    }
}
