<?php

namespace Juzaweb\Modules\Movie\Tests\Feature;

use Juzaweb\Modules\Core\Models\User;
use Juzaweb\Modules\Movie\Enums\ReportStatus;
use Juzaweb\Modules\Movie\Models\Report;
use Juzaweb\Modules\Movie\Models\ReportType;
use Juzaweb\Modules\Movie\Tests\TestCase;

class ReportTest extends TestCase
{
    public function test_index_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $type = ReportType::create(['en' => ['name' => 'Type']]);
        Report::create([
            'report_type_id' => $type->id,
            'description' => 'Test Report',
            'status' => ReportStatus::PENDING->value,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('movie::report.index');
    }

    public function test_edit_page()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $type = ReportType::create(['en' => ['name' => 'Type']]);
        $report = Report::create([
            'report_type_id' => $type->id,
            'description' => 'Test Report',
            'status' => ReportStatus::PENDING->value,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.edit', [$report->id]));

        $response->assertStatus(200);
        $response->assertViewIs('movie::report.form');
        $response->assertViewHas('model', function ($model) use ($report) {
            return $model->id === $report->id;
        });
    }

    public function test_bulk_delete()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $type = ReportType::create(['en' => ['name' => 'Type']]);
        $report = Report::create([
            'report_type_id' => $type->id,
            'description' => 'To Delete',
            'status' => ReportStatus::PENDING->value,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.reports.bulk'), [
                'ids' => [$report->id],
                'action' => 'delete',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }

    public function test_bulk_status_update()
    {
        $admin = User::factory()->create(['is_super_admin' => 1]);
        $type = ReportType::create(['en' => ['name' => 'Type']]);
        $report = Report::create([
            'report_type_id' => $type->id,
            'description' => 'Status Update',
            'status' => ReportStatus::PENDING->value,
        ]);

        // Mark as processed
        $response = $this->actingAs($admin)
            ->postJson(route('admin.reports.bulk'), [
                'ids' => [$report->id],
                'action' => ReportStatus::PROCESSED->value,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => ReportStatus::PROCESSED->value,
        ]);

        // Mark as pending
        $response = $this->actingAs($admin)
            ->postJson(route('admin.reports.bulk'), [
                'ids' => [$report->id],
                'action' => ReportStatus::PENDING->value,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => ReportStatus::PENDING->value,
        ]);
    }
}
