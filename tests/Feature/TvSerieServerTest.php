<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Core\Enums\PostStatus;
use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Models\Server;
use Juzaweb\Modules\Movie\Tests\TestCase;

class TvSerieServerTest extends TestCase
{
    protected function createAdminUser()
    {
        return User::factory()->create(['is_super_admin' => 1]);
    }

    protected function createTvSerie()
    {
        $movie = new Movie();
        $movie->fill([
            'name' => 'Test TV Series',
            'is_tv_series' => true,
            'status' => PostStatus::PUBLISHED->value,
        ]);
        $movie->setDefaultLocale('en');
        $movie->save();

        return $movie;
    }

    public function test_index_page()
    {
        $admin = $this->createAdminUser();
        $tvSerie = $this->createTvSerie();

        $response = $this->actingAs($admin)
            ->get(route('admin.tvserie-servers.index', [$tvSerie->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::server.index');
    }

    public function test_create_page()
    {
        $admin = $this->createAdminUser();
        $tvSerie = $this->createTvSerie();

        $response = $this->actingAs($admin)
            ->get(route('admin.tvserie-servers.create', [$tvSerie->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::server.form');
    }

    public function test_store()
    {
        $admin = $this->createAdminUser();
        $tvSerie = $this->createTvSerie();

        $response = $this->actingAs($admin)
            ->post(route('admin.tvserie-servers.store', [$tvSerie->id]), [
                'name' => 'Server 1',
                'display_order' => 1,
                'active' => 1,
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.tvserie-servers.index', [$tvSerie->id]));

        $this->assertDatabaseHas('servers', [
            'name' => 'Server 1',
            'movie_id' => $tvSerie->id,
            'display_order' => 1,
        ]);
    }

    public function test_edit_page()
    {
        $admin = $this->createAdminUser();
        $tvSerie = $this->createTvSerie();
        $server = $tvSerie->servers()->create([
            'name' => 'Server to Edit',
            'display_order' => 1,
            'active' => 1,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.tvserie-servers.edit', [$tvSerie->id, $server->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::server.form');
    }

    public function test_update()
    {
        $admin = $this->createAdminUser();
        $tvSerie = $this->createTvSerie();
        $server = $tvSerie->servers()->create([
            'name' => 'Old Server Name',
            'display_order' => 1,
            'active' => 1,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.tvserie-servers.update', [$tvSerie->id, $server->id]), [
                'name' => 'Updated Server Name',
                'display_order' => 2,
                'active' => 1,
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.tvserie-servers.index', [$tvSerie->id]));

        $this->assertDatabaseHas('servers', [
            'id' => $server->id,
            'name' => 'Updated Server Name',
            'display_order' => 2,
        ]);
    }

    public function test_bulk_delete()
    {
        $admin = $this->createAdminUser();
        $tvSerie = $this->createTvSerie();
        $server = $tvSerie->servers()->create([
            'name' => 'Server to Delete',
            'display_order' => 1,
            'active' => 1,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.tvserie-servers.bulk', [$tvSerie->id]), [
                'ids' => [$server->id],
                'action' => 'delete',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('servers', [
            'id' => $server->id,
        ]);
    }
}
