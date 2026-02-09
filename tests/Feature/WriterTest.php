<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Writer;
use Juzaweb\Modules\Movie\Tests\TestCase;

class WriterTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_super_admin' => 1]);
        $this->admin->forceFill(['email_verified_at' => now()]);
        $this->admin->save();
    }

    public function test_index_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.movie-writers.index'));

        $response->assertStatus(200);
        $response->assertSee('movie-writers');
    }

    public function test_create_writer()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.movie-writers.store'), [
                'name' => 'Test Writer',
                'bio' => 'Test Bio',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('movie_writers', [
            'name' => 'Test Writer',
        ]);

        // Assert translation
        $writer = Writer::where('name', 'Test Writer')->first();
        $this->assertNotNull($writer);

        $this->assertDatabaseHas('movie_writer_translations', [
            'movie_writer_id' => $writer->id,
            'bio' => 'Test Bio',
        ]);
    }

    public function test_update_writer()
    {
        $writer = new Writer();
        $writer->fill(['name' => 'Old Writer']);
        $writer->save();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.movie-writers.update', [$writer->id]), [
                'name' => 'Updated Writer',
                'bio' => 'Updated Bio',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('movie_writers', [
            'id' => $writer->id,
            'name' => 'Updated Writer',
        ]);

        $this->assertDatabaseHas('movie_writer_translations', [
            'movie_writer_id' => $writer->id,
            'bio' => 'Updated Bio',
        ]);
    }

    public function test_bulk_delete_writer()
    {
        $writer = new Writer();
        $writer->fill(['name' => 'Delete Writer']);
        $writer->save();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.movie-writers.bulk'), [
                'ids' => [$writer->id],
                'action' => 'delete',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('movie_writers', [
            'id' => $writer->id,
        ]);
    }
}
