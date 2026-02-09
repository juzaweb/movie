<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Core\Enums\PostStatus;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Models\Server;
use Juzaweb\Modules\Movie\Tests\TestCase;

class MovieServerTest extends TestCase
{
    protected $user;
    protected $movie;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($this->user);

        $this->movie = Movie::create([
            'name' => 'Test Movie',
            'origin_name' => 'Test Movie Origin',
            'status' => PostStatus::PUBLISHED->value,
            'is_tv_series' => false,
        ]);
    }

    public function test_index_page_loads()
    {
        $response = $this->get(route('admin.movie-servers.index', [$this->movie->id]));

        $response->assertStatus(200);
        $response->assertSee('Test Movie');
    }

    public function test_create_server_page_loads()
    {
        $response = $this->get(route('admin.movie-servers.create', [$this->movie->id]));

        $response->assertStatus(200);
    }

    public function test_store_server()
    {
        $response = $this->post(route('admin.movie-servers.store', [$this->movie->id]), [
            'name' => 'Server 1',
            'display_order' => 1,
            'active' => 1,
        ]);

        $response->assertRedirect(action([\Juzaweb\Modules\Movie\Http\Controllers\MovieServerController::class, 'index'], [$this->movie->id]));

        $this->assertDatabaseHas('servers', [
            'name' => 'Server 1',
            'movie_id' => $this->movie->id,
        ]);
    }

    public function test_edit_server_page_loads()
    {
        $server = Server::create([
            'name' => 'Server 1',
            'movie_id' => $this->movie->id,
            'active' => true,
            'display_order' => 1,
        ]);

        $response = $this->get(route('admin.movie-servers.edit', [$this->movie->id, $server->id]));

        $response->assertStatus(200);
        $response->assertSee('Server 1');
    }

    public function test_update_server()
    {
        $server = Server::create([
            'name' => 'Server 1',
            'movie_id' => $this->movie->id,
            'active' => true,
            'display_order' => 1,
        ]);

        $response = $this->put(route('admin.movie-servers.update', [$this->movie->id, $server->id]), [
            'name' => 'Server Updated',
            'display_order' => 2,
            'active' => 1,
        ]);

        $response->assertRedirect(action([\Juzaweb\Modules\Movie\Http\Controllers\MovieServerController::class, 'index'], [$this->movie->id]));

        $this->assertDatabaseHas('servers', [
            'id' => $server->id,
            'name' => 'Server Updated',
        ]);
    }

    public function test_bulk_delete_server()
    {
        $server = Server::create([
            'name' => 'Server 1',
            'movie_id' => $this->movie->id,
            'active' => true,
            'display_order' => 1,
        ]);

        $response = $this->postJson(route('admin.movie-servers.bulk', [$this->movie->id]), [
            'ids' => [$server->id],
            'action' => 'delete',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('servers', [
            'id' => $server->id,
        ]);
    }
}
