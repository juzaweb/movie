<?php

namespace Juzaweb\Movie\Http\Controllers\Frontend;

use Juzaweb\Http\Controllers\FrontendController;
use Juzaweb\Movie\Models\Movie\Movie;
use Juzaweb\Movie\Models\Post;
use Illuminate\Http\Request;

class CommentController extends FrontendController
{
    public function movieComment($movie_slug, Request $request) {
        $this->validateRequest([
            'content' => 'required',
        ], $request, [
            'content' => trans('theme::app.content')
        ]);
        
        $movie = Movie::where('slug', '=', $movie_slug)
            ->wherePublish()
            ->findOrFail();
        
        $movie->comments()->create([
            'content' => $request->post('content'),
            'user_id' => \Auth::id(),
        ]);
        
        return response()->json([
            'status' => 'success',
            'redirect' => $request->headers->get('referer'),
        ]);
    }
    
    public function postComment($post_slug, Request $request) {
        $this->validateRequest([
            'content' => 'required',
        ], $request, [
            'content' => trans('theme::app.content')
        ]);
        
        $post = Post::where('slug', '=', $post_slug)
            ->wherePublish()
            ->firstOrFail();
    
        $post->comments()->create([
            'content' => $request->post('content'),
            'user_id' => \Auth::id(),
        ]);
        
        return response()->json([
            'status' => 'success',
            'redirect' => $request->headers->get('referer'),
        ]);
    }
}
