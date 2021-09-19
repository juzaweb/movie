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

Route::group(['prefix' => '{type}/servers/upload/subtitle/{file_id}'], function () {
    Route::get('/',
        'Backend\SubtitleController@index')->name('admin.movies.servers.upload.subtitle')->where('file_id',
        '[0-9]+');

    Route::get('create',
        'Backend\SubtitleController@form')->name('admin.movies.servers.upload.subtitle.create')->where('file_id',
        '[0-9]+');

    Route::get('edit/{id}',
        'Backend\SubtitleController@form')->name('admin.movies.servers.upload.subtitle.edit')->where('file_id',
        '[0-9]+')->where('id', '[0-9]+');

    Route::get('getdata',
        'Backend\SubtitleController@getData')->name('admin.movies.servers.upload.subtitle.getdata')->where('file_id',
        '[0-9]+');

    Route::post('save',
        'Backend\SubtitleController@save')->name('admin.movies.servers.upload.subtitle.save')->where('file_id',
        '[0-9]+');

    Route::post('remove',
        'Backend\SubtitleController@remove')->name('admin.movies.servers.upload.subtitle.remove')->where('file_id',
        '[0-9]+');
});

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



require_once __DIR__ . '/backend/components/tmdb.route.php';
