<?php

namespace Juzaweb\Movie\Http\Controllers\Backend;

use Illuminate\Support\Facades\Validator;
use Juzaweb\Http\Controllers\BackendController;
use Juzaweb\Movie\Http\Datatable\VideoFileDatatable;
use Juzaweb\Movie\Models\Video\VideoFile;
use Juzaweb\Movie\Models\Video\VideoServer;
use Juzaweb\Traits\ResourceController;

class MovieUploadController extends BackendController
{
    use ResourceController {
        getDataForIndex as DataForIndex;
        getDataForForm as DataForForm;
    }

    protected $viewPrefix = 'mymo::movie_upload';

    /**
     * Get data table resource
     *
     * @return \Juzaweb\Abstracts\DataTable
     */
    protected function getDataTable($page_type, $server_id)
    {
        $dataTable = new VideoFileDatatable();
        $dataTable->mountData($page_type);
        return $dataTable;
    }

    /**
     * Validator for store and update
     *
     * @param array $attributes
     * @return Validator|array
     */
    protected function validator(array $attributes, $page_type, $server_id)
    {
        return [
            'label' => 'required'
        ];
    }

    /**
     * Get model resource
     *
     * @return string // namespace model
     */
    protected function getModel($page_type, $server_id)
    {
        return VideoFile::class;
    }

    /**
     * Get title resource
     *
     * @return string
     **/
    protected function getTitle($page_type, $server_id)
    {
        return trans('mymo::app.upload_videos');
    }

    protected function getDataForIndex($page_type, $server_id)
    {
        $data = $this->DataForIndex($page_type, $server_id);
        $data['page_type'] = $page_type;
        $data['server_id'] = $server_id;
        $data['server'] = VideoServer::find($server_id);
        return $data;
    }

    protected function getDataForForm($model, $page_type, $server_id)
    {
        $data = $this->DataForForm($model, $page_type, $server_id);
        $data['page_type'] = $page_type;
        $data['server_id'] = $server_id;
        $data['server'] = VideoServer::find($server_id);
        return $data;
    }
}
