<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Movie\Tests\TestCase;
use Juzaweb\Modules\Core\Models\User;

class SettingTest extends TestCase
{
    protected function createAdminUser()
    {
        $user = User::where('email', 'admin@test.com')->first();

        if ($user) {
            return $user;
        }

        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@test.com';
        $user->password = bcrypt('password');
        $user->is_super_admin = 1;
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    public function test_movie_settings_index()
    {
        $response = $this->get(route('admin.movie-settings.index'));

        $response->assertStatus(302); // Expect redirect to login if not authenticated
    }

    public function test_movie_settings_index_authenticated()
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get(route('admin.movie-settings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::setting.index');
    }

    public function test_movie_settings_update()
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->putJson(route('admin.movie-settings.update'), [
            'tmdb_api_key' => 'test_api_key',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('settings', [
            'code' => 'tmdb_api_key',
            'value' => 'test_api_key',
        ]);
    }
}
