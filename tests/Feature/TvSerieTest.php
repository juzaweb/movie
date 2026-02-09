<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Tests\TestCase;
use Juzaweb\Modules\Movie\Models\Movie;
use Juzaweb\Modules\Core\Enums\PostStatus;

class TvSerieTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'users',
        ]);

        $this->user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
    }

    public function test_tv_series_index_is_accessible()
    {
        $this->actingAs($this->user, 'admin');

        $response = $this->get(route('admin.tv-series.index'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::tv-serie.index');
    }

    public function test_tv_series_create_is_accessible()
    {
        $this->actingAs($this->user, 'admin');

        $response = $this->get(route('admin.tv-series.create'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::tv-serie.form');
    }

    public function test_tv_series_store()
    {
        $this->actingAs($this->user, 'admin');

        $data = [
            'name' => 'Test TV Series',
            'status' => PostStatus::PUBLISHED->value,
        ];

        $response = $this->post(route('admin.tv-series.store'), $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('movies', [
            'is_tv_series' => 1,
        ]);
    }

    public function test_tv_series_edit_is_accessible()
    {
        $this->actingAs($this->user, 'admin');

        $tvSerie = new Movie();
        $tvSerie->fill([
            'is_tv_series' => 1,
            'status' => PostStatus::PUBLISHED->value,
        ]);
        $tvSerie->setAttribute('name', 'Test TV Series');
        $tvSerie->save();

        $response = $this->get(route('admin.tv-series.edit', [$tvSerie->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::tv-serie.form');
    }

    public function test_tv_series_update()
    {
        $this->actingAs($this->user, 'admin');

        $tvSerie = new Movie();
        $tvSerie->fill([
            'is_tv_series' => 1,
            'status' => PostStatus::PUBLISHED->value,
        ]);
        $tvSerie->setAttribute('name', 'Old Name');
        $tvSerie->save();

        $data = [
            'name' => 'Updated TV Series',
            'status' => PostStatus::PUBLISHED->value,
        ];

        $response = $this->put(route('admin.tv-series.update', [$tvSerie->id]), $data);

        $response->assertStatus(302);

        $tvSerie = Movie::find($tvSerie->id);
        $this->assertEquals('Updated TV Series', $tvSerie->name);
    }
}
