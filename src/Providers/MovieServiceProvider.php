<?php

namespace Juzaweb\Modules\Movie\Providers;

use Illuminate\Support\Facades\File;
use Juzaweb\Modules\Core\Contracts\Sitemap;
use Juzaweb\Modules\Core\Facades\Menu;
use Juzaweb\Modules\Core\Facades\MenuBox;
use Juzaweb\Modules\Core\Providers\ServiceProvider;
use Juzaweb\Modules\Movie\Models\Actor;
use Juzaweb\Modules\Movie\Models\Country;
use Juzaweb\Modules\Movie\Models\Director;
use Juzaweb\Modules\Movie\Models\Genre;
use Juzaweb\Modules\Movie\Models\MovieTranslation;
use Juzaweb\Modules\Movie\Models\Writer;
use Juzaweb\Modules\Movie\Models\Year;

class MovieServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        MenuBox::make('genres', Genre::class, function () {
            return [
                'label' => __('movie::translation.genres'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        MenuBox::make('countries', Country::class, function () {
            return [
                'label' => __('movie::translation.countries'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        MenuBox::make('years', Year::class, function () {
            return [
                'label' => __('movie::translation.years'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        MenuBox::make('actors', Actor::class, function () {
            return [
                'label' => __('movie::translation.actors'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        MenuBox::make('directors', Director::class, function () {
            return [
                'label' => __('movie::translation.directors'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        MenuBox::make('writers', Writer::class, function () {
            return [
                'label' => __('movie::translation.writers'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        $this->app[Sitemap::class]->register(
            'movies',
            MovieTranslation::class
        );

        $this->registerMenus();
    }

    public function register(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->app->register(RouteServiceProvider::class);
        // $this->commands([
        //     \App\Console\Commands\FetchPopularMovies::class,
        // ]);
    }

    protected function registerMenus(): void
    {
        Menu::make('movie-management', function () {
            return [
                'title' => __('movie::translation.movies'),
                'icon' => 'fas fa-film',
                'priority' => 35,
                'url' => 'movies',
            ];
        });

        Menu::make('movies', function () {
            return [
                'title' => __('movie::translation.movies'),
                'parent' => 'movie-management',
                'permissions' => ['movies.index'],
            ];
        });

        Menu::make('tv-series', function () {
            return [
                'title' => __('movie::translation.tv_series'),
                'parent' => 'movie-management',
                'permissions' => ['tv-series.index'],
            ];
        });

        Menu::make('live-tvs', function () {
            return [
                'title' => __('movie::translation.live_tvs'),
                'icon' => 'fas fa-tv',
                'permissions' => ['live-tvs.index'],
            ];
        });

        Menu::make('movie-genres', function () {
            return [
                'title' => __('movie::translation.genres'),
                'parent' => 'movie-management',
                'url' => 'movie-genres',
                'permissions' => ['movie-genres.index'],
            ];
        });

        Menu::make('movie-countries', function () {
            return [
                'title' => __('movie::translation.countries'),
                'parent' => 'movie-management',
                'url' => 'movie-countries',
                'permissions' => ['movie-countries.index'],
            ];
        });

        Menu::make('movie-years', function () {
            return [
                'title' => __('movie::translation.years'),
                'parent' => 'movie-management',
                'url' => 'movie-years',
                'permissions' => ['movie-years.index'],
            ];
        });

        Menu::make('movie-actors', function () {
            return [
                'title' => __('movie::translation.actors'),
                'parent' => 'movie-management',
                'url' => 'movie-actors',
                'permissions' => ['movie-actors.index'],
            ];
        });

        Menu::make('movie-directors', function () {
            return [
                'title' => __('movie::translation.directors'),
                'parent' => 'movie-management',
                'url' => 'movie-directors',
                'permissions' => ['movie-directors.index'],
            ];
        });

        Menu::make('movie-writers', function () {
            return [
                'title' => __('movie::translation.writers'),
                'parent' => 'movie-management',
                'url' => 'movie-writers',
                'permissions' => ['movie-writers.index'],
            ];
        });

        Menu::make('movie-settings', function () {
            return [
                'title' => __('movie::translation.movie_settings'),
                'parent' => 'movie-management',
                'url' => 'movie-settings',
                'permissions' => ['movie-settings.index'],
            ];
        });

        Menu::make('reports-management', function () {
            return [
                'title' => __('movie::translation.reports'),
                'icon' => 'fas fa-flag',
                'url' => 'reports',
                'priority' => 70,
                'permissions' => ['reports.index', 'report-types.index'],
            ];
        });

        Menu::make('reports', function () {
            return [
                'title' => __('movie::translation.reports'),
                'parent' => 'reports-management',
                'permissions' => ['reports.index'],
            ];
        });

        Menu::make('report-types', function () {
            return [
                'title' => __('movie::translation.report_types'),
                'parent' => 'reports-management',
                'url' => 'report-types',
                'permissions' => ['report-types.index'],
            ];
        });
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/movie.php' => config_path('movie.php'),
        ], 'movie-config');
        $this->mergeConfigFrom(__DIR__ . '/../../config/movie.php', 'movie');
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'movie');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang');
    }

    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/movie');

        $sourcePath = __DIR__ . '/../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', 'movie-module-views']);

        $this->loadViewsFrom($sourcePath, 'movie');
    }
}
