<?php

use Juzaweb\Movie\Models\Ads;
use Juzaweb\Models\Taxonomy;

function mymo_get_ads(string $key) {
    $ads = Ads::where('key', '=', $key)
        ->where('status', '=', 1)
        ->first(['body']);
    if (empty($ads)) {
        return false;
    }

    return $ads->body;
}

if (!function_exists('taxonomy_info')) {
    function taxonomy_info($id)
    {
        return Taxonomy::where('id', '=', $id)
            ->first(['id', 'name']);
    }
}

if (!function_exists('get_youtube_id')) {
    function get_youtube_id($url)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        if (@$match[1]) {
            return $match[1];
        }
        return false;
    }
}

if (!function_exists('get_vimeo_id')) {
    function get_vimeo_id($url)
    {
        $regs = [];
        $id = '';
        if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
            $id = $regs[3];
        }
        return $id;
    }
}

if (!function_exists('get_google_drive_id')) {
    function get_google_drive_id(string $url)
    {
        return explode('/', $url)[5];
    }
}
