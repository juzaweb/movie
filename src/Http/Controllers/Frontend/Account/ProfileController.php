<?php

namespace Juzaweb\Movie\Http\Controllers\Frontend\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Juzaweb\Http\Controllers\FrontendController;
use Juzaweb\Movie\Models\Movie\Movie;

class ProfileController extends FrontendController
{
    public function index()
    {
        $viewed = Cookie::get('viewed');
        $viewed = $viewed ? json_decode($viewed, true) : [0];
        $recentlyVisited = Movie::whereIn('id', $viewed)
            ->wherePublish()
            ->paginate(5);
        
        return view('account.index', [
            'title' => trans('mymo::app.profile'),
            'user' => Auth::user(),
            'recently_visited' => $recentlyVisited
        ]);
    }
}
