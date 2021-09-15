<?php

namespace Juzaweb\Movie\Http\Controllers\Frontend;

use Juzaweb\Http\Controllers\FrontendController;

class LiveTvController extends FrontendController
{
    public function index()
    {
        return view('live-tv.index');
    }
}
