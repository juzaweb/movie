<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Models\Year;
use Juzaweb\Modules\Movie\Tests\TestCase;

class YearTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_super_admin' => 1]);
    }

    public function test_index_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.movie-years.index'));

        $response->assertStatus(200);
    }

    public function test_create_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.movie-years.create'));

        $response->assertStatus(200);
    }

    public function test_store_year()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.movie-years.store'), [
                'name' => '2023',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('movie_years', [
            'name' => '2023',
        ]);
    }

    public function test_edit_page()
    {
        $year = Year::create(['name' => '2022']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.movie-years.edit', [$year->id]));

        $response->assertStatus(200);
    }

    public function test_update_year()
    {
        $year = Year::create(['name' => '2021']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.movie-years.update', [$year->id]), [
                'name' => '2024',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('movie_years', [
            'id' => $year->id,
            'name' => '2024',
        ]);
    }

    public function test_bulk_delete()
    {
        $year = Year::create(['name' => '2020']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.movie-years.bulk'), [
                'ids' => [$year->id],
                'action' => 'delete',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('movie_years', ['id' => $year->id]);
    }
}
