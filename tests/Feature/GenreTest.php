<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Genre;
use Juzaweb\Modules\Movie\Tests\TestCase;

class GenreTest extends TestCase
{
    public function test_admin_genre_index_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-genres.index'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.genre.index');
    }

    public function test_can_create_genre()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);

        $response = $this->actingAs($admin)
            ->post(route('admin.movie-genres.store'), [
                'name' => 'Test Genre',
            ]);

        $response->assertStatus(302);

        $this->assertTrue(Genre::whereTranslation('name', 'Test Genre')->exists());
    }

    public function test_can_update_genre()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $genre = new Genre();
        $genre->fill(['name' => 'Old Genre Name']);
        $genre->save();

        $response = $this->actingAs($admin)
            ->put(route('admin.movie-genres.update', [$genre->id]), [
                'name' => 'Updated Genre Name',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.movie-genres.index'));

        $this->assertTrue(Genre::whereTranslation('name', 'Updated Genre Name')->exists());
    }

    public function test_can_bulk_delete_genre()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $genre = new Genre();
        $genre->fill(['name' => 'To Delete Genre']);
        $genre->save();

        $response = $this->actingAs($admin)
            ->post(route('admin.movie-genres.bulk'), [
                'ids' => [$genre->id],
                'action' => 'delete',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('movie_genres', ['id' => $genre->id]);
    }
}
