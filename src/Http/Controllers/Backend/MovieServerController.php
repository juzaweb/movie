<?php

namespace Juzaweb\Movie\Http\Controllers\Backend;

use Illuminate\Support\Facades\Validator;
use Juzaweb\Http\Controllers\BackendController;
use Juzaweb\Movie\Http\Datatable\VideoServerDatatable;
use Juzaweb\Movie\Models\Movie\Movie;
use Juzaweb\Movie\Models\Video\VideoServer;
use Juzaweb\Traits\ResourceController;

class MovieServerController extends BackendController
{
    use ResourceController {
        getDataForIndex as DataForIndex;
        getDataForForm as DataForForm;
    }

    protected $viewPrefix = 'mymo::movie_server';

    /**
     * Get data table resource
     *
     * @return \Juzaweb\Abstracts\DataTable
     */
    protected function getDataTable($page_type, $movie_id)
    {
        $dataTable = new VideoServerDatatable();
        $dataTable->mountData($page_type, $movie_id);
        return $dataTable;
    }

    /**
     * Validator for store and update
     *
     * @param array $attributes
     * @return Validator|array
     */
    protected function validator(array $attributes, $page_type, $movie_id)
    {
        return [
            'name' => 'required'
        ];
    }

    /**
     * Get model resource
     *
     * @return string // namespace model
     */
    protected function getModel($page_type, $movie_id)
    {
        return VideoServer::class;
    }

    /**
     * Get title resource
     *
     * @return string
     **/
    protected function getTitle($page_type, $movie_id)
    {
        return trans('mymo::app.servers');
    }

    protected function getDataForIndex($page_type, $movie_id)
    {
        $data = $this->DataForIndex($page_type, $movie_id);
        $data['page_type'] = $page_type;
        $data['movie_id'] = $movie_id;
        $data['movie'] = Movie::find($movie_id);
        return $data;
    }

    protected function getDataForForm($model, $page_type, $movie_id)
    {
        $data = $this->DataForForm($model, $page_type, $movie_id);
        $data['movie_id'] = $movie_id;
        return $data;
    }
}
