<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/laravel-cms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://juzaweb.com/cms
 * @license    MIT
 */

Route::jwResource('{type}/servers/{movie_id}', 'Backend\MovieServerController', [
    'name' => 'movies.servers'
]);

Route::jwResource('{type}/servers/upload/{server_id}', 'Backend\MovieUploadController', [
    'name' => 'movies.servers.upload'
]);

Route::jwResource('{type}/servers/upload/subtitle/{file_id}', 'Backend\SubtitleController', [
    'name' => 'movies.servers.upload.subtitle',
]);

Route::group(['prefix' => '{type}/download/{movie_id}'], function () {
    Route::get('/', 'Backend\MovieDownloadController@index')->name('admin.movies.download');

    Route::get('/getdata', 'Backend\MovieDownloadController@getData')->name('admin.movies.download.getdata');

    Route::get('/create', 'Backend\MovieDownloadController@form')->name('admin.movies.download.create');

    Route::get('/edit/{id}',
        'Backend\MovieDownloadController@form')->name('admin.movies.download.edit')->where('id', '[0-9]+');

    Route::post('/save', 'Backend\MovieDownloadController@save')->name('admin.movies.download.save');

    Route::post('/remove', 'Backend\MovieDownloadController@remove')->name('admin.movies.download.remove');
});

Route::postTypeResource('movies', 'Backend\MovieController');

Route::jwResource('tv-series', 'Backend\TVSerieController');

Route::jwResource('video-ads', 'Backend\VideoAdsController');

Route::post('/add-movie', 'Backend\TmdbController@addMovie')->name('admin.tmdb.add_movie');
