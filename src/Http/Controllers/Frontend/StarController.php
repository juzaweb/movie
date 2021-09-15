<?php

namespace Juzaweb\Movie\Http\Controllers\Frontend;

use Juzaweb\Http\Controllers\FrontendController;
use Juzaweb\Models\Taxonomy;
use Juzaweb\Movie\Models\Movie\Movie;
use Juzaweb\Movie\Models\Category\Stars;

class StarController extends FrontendController
{
    public function index($slug) {
        $info = Taxonomy::where('slug', '=', $slug)
            ->firstOrFail(['name', 'slug']);
    
        $items = Movie::select([
            'id',
            'name',
            'other_name',
            'short_description',
            'thumbnail',
            'slug',
            'views',
            'video_quality',
            'year',
            'genres',
            'countries',
            'tv_series',
            'current_episode',
            'max_episode',
        ])
            ->wherePublish()
            ->whereRaw('find_in_set(?, stars)', [$info->id])
            ->orderBy('id', 'DESC')
            ->paginate(20);
    
        return view('genre.index', [
            'title' => $info->name,
            'description' => $info->name,
            'keywords' => $info->name,
            'items' => $items,
            'info' => $info,
        ]);
    }
}
