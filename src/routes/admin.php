<?php

use Illuminate\Support\Facades\Route;
use Juzaweb\Modules\Movie\Http\Controllers\ActorController;
use Juzaweb\Modules\Movie\Http\Controllers\CountryController;
use Juzaweb\Modules\Movie\Http\Controllers\DirectorController;
use Juzaweb\Modules\Movie\Http\Controllers\GenreController;
use Juzaweb\Modules\Movie\Http\Controllers\LiveTvController;
use Juzaweb\Modules\Movie\Http\Controllers\MovieController;
use Juzaweb\Modules\Movie\Http\Controllers\MovieServerController;
use Juzaweb\Modules\Movie\Http\Controllers\ReportController;
use Juzaweb\Modules\Movie\Http\Controllers\ReportTypeController;
use Juzaweb\Modules\Movie\Http\Controllers\ServerFileController;
use Juzaweb\Modules\Movie\Http\Controllers\SettingController;
use Juzaweb\Modules\Movie\Http\Controllers\TvSerieController;
use Juzaweb\Modules\Movie\Http\Controllers\WriterController;
use Juzaweb\Modules\Movie\Http\Controllers\YearController;

Route::get('/movie-settings', [SettingController::class, 'index'])
    ->name('admin.movie-settings.index');

Route::put('/movie-settings', [SettingController::class, 'update'])
    ->name('admin.movie-settings.update');

Route::post('/movies/import-from-tmdb', [MovieController::class, 'importFromTmdb'])
    ->name('admin.movies.import-from-tmdb');

Route::post('/tv-series/import-from-tmdb', [TvSerieController::class, 'importFromTmdb'])
    ->name('admin.tv-series.import-from-tmdb');

Route::admin('movies', MovieController::class);
Route::admin('tv-series', TvSerieController::class);
Route::admin('live-tvs', LiveTvController::class);

// Scoped server management for movies
Route::admin('movies/{movieId}/servers', MovieServerController::class)
    ->name('admin.movie-servers');

// Scoped server management for TV series
Route::admin('tv-series/{tvSerieId}/servers', MovieServerController::class)
    ->name('admin.tvserie-servers');

// Movie file management routes
Route::admin('servers/{serverId}/files', ServerFileController::class)
    ->name('admin.server-files');

Route::admin('movie-genres', GenreController::class);
Route::admin('movie-countries', CountryController::class);
Route::admin('movie-years', YearController::class);
Route::admin('movie-actors', ActorController::class);
Route::admin('movie-directors', DirectorController::class);
Route::admin('movie-writers', WriterController::class);
Route::admin('reports', ReportController::class);
Route::admin('report-types', ReportTypeController::class);
