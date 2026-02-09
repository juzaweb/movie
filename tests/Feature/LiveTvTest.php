<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Juzaweb\Modules\Admin\Enums\UserStatus;
use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\LiveTv;
use Juzaweb\Modules\Movie\Tests\TestCase;

class LiveTvTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $user = new User();
        $user->forceFill([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_super_admin' => 1,
            'email_verified_at' => now(),
            'status' => UserStatus::ACTIVE,
        ]);
        $user->save();
        $this->user = $user;
    }

    public function test_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.live-tvs.index'));

        $response->assertStatus(200);
    }

    public function test_create()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.live-tvs.create'));

        $response->assertStatus(200);
    }

    public function test_store()
    {
        $data = [
            'name' => 'Test Live TV',
            'streaming_url' => 'https://example.com/stream.m3u8',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.live-tvs.store'), $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('live_tvs', [
            'streaming_url' => 'https://example.com/stream.m3u8',
        ]);
    }

    public function test_edit()
    {
        $liveTv = LiveTv::create([
            'name' => 'Test Live TV Edit',
            'streaming_url' => 'https://example.com/stream.m3u8',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.live-tvs.edit', [$liveTv->id]));

        $response->assertStatus(200);
    }

    public function test_update()
    {
        $liveTv = LiveTv::create([
            'name' => 'Test Live TV Update',
            'streaming_url' => 'https://example.com/stream.m3u8',
        ]);

        $data = [
            'name' => 'Test Live TV Updated',
            'streaming_url' => 'https://example.com/stream-updated.m3u8',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.live-tvs.update', [$liveTv->id]), $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('live_tvs', [
            'id' => $liveTv->id,
            'streaming_url' => 'https://example.com/stream-updated.m3u8',
        ]);
    }

    public function test_bulk_delete()
    {
        $liveTv = LiveTv::create([
            'name' => 'Test Live TV Delete',
            'streaming_url' => 'https://example.com/stream.m3u8',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('admin.live-tvs.bulk'), [
                'ids' => [$liveTv->id],
                'action' => 'delete',
            ]);

        // It might redirect if not AJAX
        if ($response->status() === 302) {
             $response->assertStatus(302);
        } else {
             $response->assertStatus(200);
        }

        $this->assertDatabaseMissing('live_tvs', ['id' => $liveTv->id]);
    }
}
