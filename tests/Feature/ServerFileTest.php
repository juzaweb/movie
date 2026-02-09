<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Core\Enums\VideoSource;
use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Movie\Models\Server;
use Juzaweb\Modules\Movie\Models\ServerFile;
use Juzaweb\Modules\Movie\Tests\TestCase;

class ServerFileTest extends TestCase
{
    protected $admin;
    protected $movie;
    protected $server;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_super_admin' => 1]);

        $this->movie = new Movie();
        $this->movie->fill(['name' => 'Test Movie']);
        $this->movie->save();

        $this->server = new Server();
        $this->server->fill(['name' => 'Server 1', 'movie_id' => $this->movie->id]);
        $this->server->save();
    }

    public function test_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.server-files.index', [$this->server->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::server-file.index');
    }

    public function test_create()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.server-files.create', [$this->server->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::server-file.form');
    }

    public function test_edit()
    {
        $serverFile = new ServerFile();
        $serverFile->fill([
            'name' => 'Test File',
            'path' => 'http://example.com/video.mp4',
            'source' => VideoSource::cases()[0]->value ?? 'files', // Fallback if enum fails
            'server_id' => $this->server->id,
        ]);
        $serverFile->save();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.server-files.edit', [$this->server->id, $serverFile->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::server-file.form');
    }

    public function test_store()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.server-files.store', [$this->server->id]), [
                'name' => 'New Server File',
                'path' => 'http://example.com/new.mp4',
                'source' => VideoSource::cases()[0]->value ?? 'files',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('server_files', [
            'name' => 'New Server File',
            'server_id' => $this->server->id,
        ]);
    }

    public function test_update()
    {
        $serverFile = new ServerFile();
        $serverFile->fill([
            'name' => 'Old Server File',
            'path' => 'http://example.com/old.mp4',
            'source' => VideoSource::cases()[0]->value ?? 'files',
            'server_id' => $this->server->id,
        ]);
        $serverFile->save();

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.server-files.update', [$this->server->id, $serverFile->id]), [
                'name' => 'Updated Server File',
                'path' => 'http://example.com/updated.mp4',
                'source' => VideoSource::cases()[0]->value ?? 'files',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('server_files', [
            'id' => $serverFile->id,
            'name' => 'Updated Server File',
        ]);
    }

    public function test_bulk_delete()
    {
        $serverFile = new ServerFile();
        $serverFile->fill([
            'name' => 'To Delete',
            'path' => 'http://example.com/delete.mp4',
            'source' => VideoSource::cases()[0]->value ?? 'files',
            'server_id' => $this->server->id,
        ]);
        $serverFile->save();

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.server-files.bulk', [$this->server->id]), [
                'ids' => [$serverFile->id],
                'action' => 'delete',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('server_files', [
            'id' => $serverFile->id,
        ]);
    }
}
