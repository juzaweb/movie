<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Http\Controllers\ReportTypeController;
use Juzaweb\Modules\Movie\Models\ReportType;
use Juzaweb\Modules\Movie\Tests\TestCase;

class ReportTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'users',
        ]);

        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        $this->actingAs($user, 'admin');
    }

    public function testIndex()
    {
        $response = $this->get(action([ReportTypeController::class, 'index']));

        $response->assertStatus(200);
    }

    public function testCreate()
    {
        $response = $this->get(action([ReportTypeController::class, 'create']));

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $response = $this->post(action([ReportTypeController::class, 'store']), [
            'name' => 'Test Report Type',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('report_types_translations', [
            'name' => 'Test Report Type',
        ]);
    }

    public function testEdit()
    {
        $reportType = ReportType::create([
            'name' => 'Edit Me',
        ]);

        $response = $this->get(action([ReportTypeController::class, 'edit'], [$reportType->id]));

        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $reportType = ReportType::create([
            'name' => 'Old Name',
        ]);

        $response = $this->put(action([ReportTypeController::class, 'update'], [$reportType->id]), [
            'name' => 'New Name',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('report_types_translations', [
            'name' => 'New Name',
        ]);
    }

    public function testBulkDelete()
    {
        $reportType = ReportType::create([
            'name' => 'Delete Me',
        ]);

        $response = $this->postJson(action([ReportTypeController::class, 'bulk']), [
            'ids' => [$reportType->id],
            'action' => 'delete',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('report_types', [
            'id' => $reportType->id,
        ]);
    }
}
