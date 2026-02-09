<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Country;
use Juzaweb\Modules\Movie\Tests\TestCase;

class CountryTest extends TestCase
{
    public function test_index_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-countries.index'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.country.index');
    }

    public function test_create_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-countries.create'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.country.form');
    }

    public function test_store()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);

        $response = $this->actingAs($admin)
            ->post(route('admin.movie-countries.store'), [
                'name' => 'Vietnam',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('movie_country_translations', [
            'name' => 'Vietnam',
        ]);
    }

    public function test_edit_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $country = new Country();
        $country->fill(['name' => 'USA']);
        $country->save();

        $response = $this->actingAs($admin)
            ->get(route('admin.movie-countries.edit', [$country->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::admin.country.form');
    }

    public function test_update()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $country = new Country();
        $country->fill(['name' => 'Old Name']);
        $country->save();

        $response = $this->actingAs($admin)
            ->put(route('admin.movie-countries.update', [$country->id]), [
                'name' => 'New Name',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('movie_country_translations', [
            'name' => 'New Name',
        ]);
    }

    public function test_bulk_delete()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $country = new Country();
        $country->fill(['name' => 'To Delete']);
        $country->save();

        $response = $this->actingAs($admin)
            ->post(route('admin.movie-countries.bulk'), [
                'ids' => [$country->id],
                'action' => 'delete',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('movie_countries', ['id' => $country->id]);
    }
}
