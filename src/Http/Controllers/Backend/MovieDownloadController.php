<?php

namespace Juzaweb\Movie\Http\Controllers\Backend;

use Juzaweb\Movie\Models\Movie\Movie;
use Illuminate\Http\Request;
use Juzaweb\Http\Controllers\BackendController;
use Juzaweb\Movie\Models\DownloadLink;

class MovieDownloadController extends BackendController
{
    public function index($page_type, $movie_id) {
        $movie = Movie::findOrFail($movie_id);
        
        return view('mymo::download.index', [
            'movie_id' => $movie_id,
            'movie' => $movie,
            'page_type' => $page_type,
            'title' => trans('mymo::app.download_videos')
        ]);
    }
    
    public function form($page_type, $movie_id, $id = null) {
        $movie = Movie::findOrFail($movie_id);
        $model = DownloadLink::firstOrNew(['id' => $id]);
        
        return view('mymo::download.form', [
            'movie_id' => $movie_id,
            'movie' => $movie,
            'page_type' => $page_type,
            'model' => $model,
            'title' => $model->lable ? $model->lable : trans('mymo::app.add_new'),
        ]);
    }
    
    public function getData($page_type, $movie_id, Request $request) {
        Movie::findOrFail($movie_id);
        $search = $request->get('search');
        $status = $request->get('status');
        
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'desc');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        
        $query = DownloadLink::query();
        $query->where('movie_id', '=', $movie_id);
        
        if ($search) {
            $query->where(function ($subquery) use ($search) {
                $subquery->orWhere('label', 'like', '%'. $search .'%');
                $subquery->orWhere('url', 'like', '%'. $search .'%');
            });
        }
        
        if (!is_null($status)) {
            $query->where('status', '=', $status);
        }
        
        $count = $query->count();
        $query->orderBy($sort, $order);
        $query->offset($offset);
        $query->limit($limit);
        $rows = $query->get();
        
        foreach ($rows as $row) {
            $row->created = $row->created_at->format('H:i Y-m-d');
            $row->edit_url = route('admin.movies.download.edit', [$page_type, $movie_id, $row->id]);
        }
        
        return response()->json([
            'total' => $count,
            'rows' => $rows
        ]);
    }
    
    public function save($page_type, $movie_id, Request $request) {
        Movie::findOrFail($movie_id);
        
        $this->validateRequest([
            'label' => 'required|string|max:250',
            'url' => 'required|string|max:300',
            'order' => 'required|numeric|max:300',
            'status' => 'required|in:0,1',
        ], $request, [
            'label' => trans('mymo::app.label'),
            'url' => trans('mymo::app.url'),
            'order' => trans('mymo::app.order'),
            'status' => trans('mymo::app.status'),
        ]);
        
        $model = DownloadLink::firstOrNew(['id' => $request->post('id')]);
        $model->fill($request->all());
        $model->movie_id = $movie_id;
        $model->save();
        
        return response()->json([
            'status' => 'success',
            'message' => trans('mymo::app.saved_successfully'),
            'redirect' => route('admin.movies.download', [$page_type, $movie_id]),
        ]);
    }
    
    public function remove($page_type, $movie_id, Request $request) {
        Movie::findOrFail($movie_id);
        $this->validateRequest([
            'ids' => 'required',
        ], $request, [
            'ids' => trans('mymo::app.subtitle')
        ]);
        
        DownloadLink::destroy($request->post('ids'));
        
        return response()->json([
            'status' => 'success',
            'message' => trans('mymo::app.deleted_successfully'),
        ]);
    }
}
