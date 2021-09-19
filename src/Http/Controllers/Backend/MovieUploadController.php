<?php

namespace Juzaweb\Movie\Http\Controllers\Backend;

use Juzaweb\Movie\Models\Movie\Movie;
use Juzaweb\Movie\Models\Video\VideoServer;
use Juzaweb\Movie\Models\Video\VideoFiles;
use Illuminate\Http\Request;
use Juzaweb\Http\Controllers\BackendController;

class MovieUploadController extends BackendController
{
    public function index($page_type, $server_id) {
        $server = VideoServer::where('id', '=', $server_id)->firstOrFail();
        
        return view('mymo::movie_upload.index', [
            'title' => trans('mymo::app.upload'),
            'server' => $server,
            'movie' => $server->movie,
            'page_type' => $page_type,
        ]);
    }
    
    public function form($page_type, $server_id, $id = null) {
        $server = VideoServer::where('id', '=', $server_id)->firstOrFail();
        $movie = Movie::where('id', '=', $server->movie_id)->firstOrFail();
        $model = VideoFiles::firstOrNew(['id' => $id]);
        
        return view('mymo::movie_upload.form', [
            'title' => $model->label ? $model->label : trans('mymo::app.add_new'),
            'server' => $server,
            'movie' => $movie,
            'model' => $model,
            'page_type' => $page_type,
        ]);
    }
    
    public function getData($page_type, $server_id, Request $request) {
        $search = $request->get('search');
        $status = $request->get('status');
        
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        
        $query = VideoFiles::query();
        $query->where('server_id', '=', $server_id);
        
        if ($search) {
            $query->orWhere('name', 'like', '%'. $search .'%');
        }
        
        if (!is_null($status)) {
            $query->where('status', '=', $status);
        }
        
        $count = $query->count();
        $query->orderBy('order', 'asc');
        $query->offset($offset);
        $query->limit($limit);
        $rows = $query->get();
        
        foreach ($rows as $row) {
            if ($row->created_at) {
                $row->created = $row->created_at->format('H:i Y-m-d');
            }
            
            $row->edit_url = route('admin.movies.servers.upload.edit', [$page_type, $server_id, $row->id]);
            $row->subtitle_url = route('admin.movies.servers.upload.subtitle', [$page_type, $row->id]);
            $row->url = substr($row->url, 0, 50);
        }
        
        return response()->json([
            'total' => $count,
            'rows' => $rows
        ]);
    }
    
    public function save($page_type, $server_id, Request $request) {
        $this->validateRequest([
            'label' => 'required|string|max:250',
            'source' => 'required|string|max:100',
            //'url' => 'required_if:source,!=,upload|max:250',
            //'url_upload' => 'required_if:source,upload|max:250',
            'order' => 'required|numeric',
        ], $request, [
            'label' => trans('mymo::app.label'),
            'source' => trans('mymo::app.source'),
            'url' => trans('mymo::app.video_url'),
            'url_upload' => trans('mymo::app.video_url'),
            'order' => trans('mymo::app.order'),
        ]);
        
        if ($request->post('source') == 'gdrive') {
            if (!get_google_drive_id($request->post('url'))) {
                return response()->json([
                    'status' => 'error',
                    'message' => trans('mymo::app.cannot_get_google_drive_id'),
                ]);
            }
        }
        
        $model = VideoFiles::firstOrNew(['id' => $request->post('id')]);
        $model->fill($request->all());
        $model->server_id = $server_id;
        $model->movie_id = $server_id;
        
        if ($request->post('source') == 'upload') {
            $model->url = image_path($request->post('url_upload'));
        }
        
        $model->save();
        
        return $this->success([
            'message' => trans('mymo::app.saved_successfully'),
            'redirect' => route('admin.movies.servers.upload', [$page_type, $server_id]),
        ]);
    }
    
    public function remove($page_type, $server_id, Request $request) {
        $this->validateRequest([
            'ids' => 'required',
        ], $request, [
            'ids' => trans('mymo::app.servers'),
        ]);
    
        VideoFiles::destroy($request->post('ids', []));
        
        return $this->success([
            'message' => trans('mymo::app.saved_successfully'),
            'redirect' => route('admin.movies.servers.upload', [
                $page_type,
                $server_id
            ]),
        ]);
    }
    
}
