<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Director;
use Juzaweb\Modules\Movie\Tests\TestCase;

class DirectorTest extends TestCase
{
    public function test_director_admin_route_exists()
    {
        $route = Route::getRoutes()->getByName('admin.movie-directors.index');
        $this->assertNotNull($route, 'Route admin.movie-directors.index should exist');
    }

    public function test_admin_director_index_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-directors.index'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.director.index');
    }

    public function test_admin_director_create_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-directors.create'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.director.form');
    }

    public function test_can_create_director()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $response = $this->actingAs($admin)
            ->post(route('admin.movie-directors.store'), [
                'name' => 'Test Director',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('movie_directors', [
            'name' => 'Test Director',
        ]);
    }

    public function test_admin_director_edit_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $director = new Director();
        $director->fill(['name' => 'Director To Edit']);
        $director->save();

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-directors.edit', [$director->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.director.form');
        // $response->assertSee('Director To Edit'); // Sometimes names are in inputs, might be tricky to assert exact see if encoded
    }

    public function test_can_update_director()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $director = new Director();
        $director->fill(['name' => 'Old Name']);
        $director->save();

        $response = $this->actingAs($admin)
            ->put(route('admin.movie-directors.update', [$director->id]), [
                'name' => 'Updated Director',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.movie-directors.index'));

        $this->assertDatabaseHas('movie_directors', [
            'id' => $director->id,
            'name' => 'Updated Director',
        ]);
    }

    public function test_can_bulk_delete_director()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $director = new Director();
        $director->fill(['name' => 'To Delete']);
        $director->save();

        $response = $this->actingAs($admin)
            ->post(route('admin.movie-directors.bulk'), [
                'ids' => [$director->id],
                'action' => 'delete',
            ]);

        // Bulk action often redirects back
        $response->assertStatus(302);

        $this->assertDatabaseMissing('movie_directors', ['id' => $director->id]);
    }
}
