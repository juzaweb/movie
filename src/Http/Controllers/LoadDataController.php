<?php

namespace Juzaweb\Movie\Http\Controllers;

use Illuminate\Http\Request;
use Juzaweb\Http\Controllers\BackendController;
use Juzaweb\Movie\Models\Slider;

class LoadDataController extends BackendController
{
    public function loadData($func, Request $request) {
        if (method_exists($this, $func)) {
            return $this->{$func}($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Function not found',
        ]);
    }

    protected function loadSliders(Request $request) {
        $search = $request->get('search');
        $explodes = $request->get('explodes');

        $query = Slider::query();
        $query->select([
            'id',
            'name AS text'
        ]);

        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->orWhere('name', 'like', '%'. $search .'%');
            });
        }

        if ($explodes) {
            $query->whereNotIn('id', $explodes);
        }

        $paginate = $query->paginate(10);
        $data['results'] = $query->get();
        if ($paginate->nextPageUrl()) {
            $data['pagination'] = ['more' => true];
        }

        return response()->json($data);
    }

    protected function loadLiveTvCategory(Request $request) {
        $search = $request->get('search');
        $explodes = $request->get('explodes');

        $query = LiveTvCategory::query();
        $query->select([
            'id',
            'name AS text'
        ]);

        if ($search) {
            $query->where('name', 'like', '%'. $search .'%');
        }

        if ($explodes) {
            $query->whereNotIn('id', $explodes);
        }

        $paginate = $query->paginate(10);
        $data['results'] = $query->get();
        if ($paginate->nextPageUrl()) {
            $data['pagination'] = ['more' => true];
        }

        return response()->json($data);
    }
}
