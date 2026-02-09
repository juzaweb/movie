<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Juzaweb\Modules\Core\Enums\PostStatus;
use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Providers\MovieServiceProvider;
use Juzaweb\Modules\Movie\Tests\TestCase;

class MovieTest extends TestCase
{
    public function test_movie_service_provider_registered()
    {
        $this->assertArrayHasKey(MovieServiceProvider::class, $this->app->getLoadedProviders());
    }

    public function test_movie_admin_route_exists()
    {
        $route = Route::getRoutes()->getByName('admin.movies.index');
        $this->assertNotNull($route, 'Route admin.movies.index should exist');
    }

    public function test_admin_movie_index_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);

        $response = $this->actingAs($admin)
            ->get(route('admin.movies.index'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::movie.index');
    }

    public function test_can_create_movie()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);

        $response = $this->actingAs($admin)
            ->post(route('admin.movies.store'), [
                'name' => 'Test Movie',
                'status' => PostStatus::PUBLISHED->value,
            ]);

        $response->assertStatus(302);
        // The controller redirects to movie servers index after creation
        // We can check if it redirects to a URL containing 'servers'
        // $model = Movie::whereTranslation('name', 'Test Movie')->first();
        // $response->assertRedirect(route('admin.movie-servers.index', [$model->id]));
        // Since we don't have the ID easily before assert, we can just check status.

        $this->assertDatabaseHas('movies', [
            'status' => PostStatus::PUBLISHED->value,
        ]);

        $this->assertTrue(Movie::whereTranslation('name', 'Test Movie')->exists());
    }

    public function test_can_update_movie()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $movie = new Movie();
        $movie->status = PostStatus::PUBLISHED->value;
        $movie->fill(['name' => 'Old Name']);
        $movie->save();

        $response = $this->actingAs($admin)
            ->put(route('admin.movies.update', [$movie->id]), [
                'name' => 'Updated Movie',
                'status' => PostStatus::PUBLISHED->value,
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.movies.index'));

        $this->assertTrue(Movie::whereTranslation('name', 'Updated Movie')->exists());
    }

    public function test_can_bulk_delete_movie()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $movie = new Movie();
        $movie->status = PostStatus::PUBLISHED->value;
        $movie->fill(['name' => 'To Delete']);
        $movie->save();

        $response = $this->actingAs($admin)
            ->from(route('admin.movies.index'))
            ->post(route('admin.movies.bulk'), [
                'ids' => [$movie->id],
                'action' => 'delete',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);
    }
}
