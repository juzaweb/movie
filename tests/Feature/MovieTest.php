<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Movie\Tests\TestCase;
use Juzaweb\Modules\Movie\Providers\MovieServiceProvider;
use Illuminate\Support\Facades\Route;

class MovieTest extends TestCase
{
    public function test_movie_service_provider_registered()
    {
        $this->assertArrayHasKey(MovieServiceProvider::class, $this->app->getLoadedProviders());
    }

    public function test_movie_admin_route_exists()
    {
        // Check if route exists in the route list
        // Note: Route::admin('movies', MovieController::class) creates resources routes.
        // The index route should be 'admin.movies.index'.
        $route = Route::getRoutes()->getByName('admin.movies.index');
        $this->assertNotNull($route, 'Route admin.movies.index should exist');
    }
}
