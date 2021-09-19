<?php

namespace Juzaweb\Movie\Http\Controllers\Backend;

use Juzaweb\Movie\Http\Datatable\MovieDatatable;
use Juzaweb\Traits\PostTypeController;
use Illuminate\Support\Facades\Validator;
use Juzaweb\Movie\Models\Movie\Movie;
use Juzaweb\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        return new MovieDatatable();
    }

    protected function getDataTable2(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $genre = $request->get('genre');
        $country = $request->get('country');
        
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'desc');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        
        $query = Movie::query();
        $query->where('tv_series', '=', 0);

        if ($search) {
            $query->where(function ($subquery) use ($search) {
                $subquery->orWhere('name', 'like', '%'. $search .'%');
                $subquery->orWhere('description', 'like', '%'. $search .'%');
            });
        }
    
        if ($genre) {
            $query->whereRaw('find_in_set(?, genres)', [$genre]);
        }
    
        if ($country) {
            $query->whereRaw('find_in_set(?, countries)', [$country]);
        }
        
        if ($status) {
            $query->where('status', '=', $status);
        }
        
        $count = $query->count();
        $query->orderBy($sort, $order);
        $query->offset($offset);
        $query->limit($limit);
        $rows = $query->get();
        
        foreach ($rows as $row) {
            $row->thumb_url = $row->getThumbnail();
            $row->created = $row->created_at->format('H:i Y-m-d');
            $row->description = Str::words(strip_tags($row->description), 15);
            $row->edit_url = route('admin.movies.edit', [$row->id]);
            $row->preview_url = route('watch', [$row->slug]);
            $row->upload_url = route('admin.movies.servers', ['movies', $row->id]);
            $row->download_url = route('admin.movies.download', ['movies', $row->id]);
        }
        
        return response()->json([
            'total' => $count,
            'rows' => $rows
        ]);
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
