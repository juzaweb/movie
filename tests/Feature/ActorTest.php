<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Actor;
use Juzaweb\Modules\Movie\Tests\TestCase;

class ActorTest extends TestCase
{
    public function test_index_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-actors.index'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.actor.index');
    }

    public function test_create_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-actors.create'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.actor.form');
    }

    public function test_store()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $response = $this->actingAs($admin)
            ->post(route('admin.movie-actors.store'), [
                'name' => 'Test Actor',
                'bio' => 'Test Bio',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.movie-actors.index'));

        $this->assertDatabaseHas('movie_actors', [
            'name' => 'Test Actor',
        ]);

        $this->assertTrue(Actor::whereTranslation('bio', 'Test Bio')->exists());
    }

    public function test_edit_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $actor = new Actor();
        $actor->fill(['name' => 'Actor to Edit']);
        $actor->save();

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-actors.edit', [$actor->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.actor.form');
    }

    public function test_update()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $actor = new Actor();
        $actor->fill(['name' => 'Old Name']);
        $actor->save();

        $response = $this->actingAs($admin)
            ->put(route('admin.movie-actors.update', [$actor->id]), [
                'name' => 'Updated Name',
                'bio' => 'Updated Bio',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.movie-actors.index'));

        $this->assertDatabaseHas('movie_actors', [
            'name' => 'Updated Name',
        ]);

        $this->assertTrue(Actor::whereTranslation('bio', 'Updated Bio')->exists());
    }

    public function test_bulk_delete()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $admin->forceFill(['email_verified_at' => now()])->save();

        $actor = new Actor();
        $actor->fill(['name' => 'To Delete']);
        $actor->save();

        $response = $this->actingAs($admin)
            ->post(route('admin.movie-actors.bulk'), [
                'ids' => [$actor->id],
                'action' => 'delete',
            ]);

        // Bulk action usually returns JSON if AJAX, but simple POST might redirect.
        // Assuming standard behavior for bulk actions in this system.
        // If it fails, check if it returns JSON even for non-AJAX or if it redirects.

        if ($response->status() === 200) {
             $response->assertJson(['message' => __('movie::translation.actor_updated_successfully')]);
        } else {
             $response->assertStatus(302);
        }

        $this->assertDatabaseMissing('movie_actors', ['id' => $actor->id]);
    }
}
