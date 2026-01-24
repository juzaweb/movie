<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Juzaweb\Modules\Core\Models\Model;

class Report extends Model
{
    use HasUuids;

    protected $table = 'reports';

    protected $fillable = [
        'report_type_id',
        'reportable_id',
        'reportable_type',
        'meta',
        'description',
        'status',
    ];

    protected $casts = [
        'meta' => 'array',
        'status' => \Juzaweb\Modules\Movie\Enums\ReportStatus::class,
    ];

    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }

    public function reportable()
    {
        return $this->morphTo();
    }
}
