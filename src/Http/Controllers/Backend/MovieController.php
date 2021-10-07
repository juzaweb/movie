<?php

namespace Juzaweb\Movie\Http\Controllers\Backend;

use Juzaweb\Movie\Http\Datatables\MovieDatatable;
use Juzaweb\Traits\PostTypeController;
use Illuminate\Support\Facades\Validator;
use Juzaweb\Movie\Models\Movie\Movie;
use Juzaweb\Http\Controllers\BackendController;

class MovieController extends BackendController
{
    use PostTypeController;

    protected $viewPrefix = 'mymo::movies';

    protected function getModel()
    {
        return Movie::class;
    }

    protected function getDataTable()
    {
        $dataTable = new MovieDatatable();
        $dataTable->mountData('movies', 0);
        return $dataTable;
    }

    protected function validator(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'title' => 'required|string|max:250',
            'description' => 'nullable',
            'content' => 'nullable',
            'status' => 'required|in:draft,publish,trash,private',
            'thumbnail' => 'nullable|string|max:250',
            'poster' => 'nullable|string|max:250',
            'rating' => 'nullable|string|max:25',
            'release' => 'nullable|date_format:Y-m-d',
            'runtime' => 'nullable|string|max:100',
            'video_quality' => 'nullable|string|max:100',
            'trailer_link' => 'nullable|string|max:100',
        ]);

        return $validator;
    }
}
