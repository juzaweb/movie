<?php

namespace Juzaweb\Movie\Http\Controllers\Frontend;

use Juzaweb\Http\Controllers\FrontendController;
use Juzaweb\Movie\Models\Movie\MovieRating;
use Juzaweb\Movie\Models\Movie\Movie;
use Illuminate\Http\Request;

class RatingController extends FrontendController
{
    public function setRating($slug, Request $request) {
        $movie = Movie::where('slug', '=', $slug)
            ->wherePublish()
            ->firstOrFail(['id']);
        
        $start = $request->post('value');
        if (empty($start)) {
            return response()->json([
                'status' => 'error',
            ]);
        }
        
        $client_ip = get_client_ip();
        
        $model = MovieRating::firstOrNew([
            'movie_id' => $movie->id,
            'client_ip' => $client_ip,
        ]);
        
        $model->movie_id = $movie->id;
        $model->client_ip = $client_ip;
        $model->start = $start;
        $model->save();
        
        return $movie->getStarRating();
    }
}
