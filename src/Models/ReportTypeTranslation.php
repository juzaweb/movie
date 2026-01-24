<?php

namespace Juzaweb\Modules\Movie\Models;

use Juzaweb\Modules\Core\Models\Model;

class ReportTypeTranslation extends Model
{
    protected $table = 'report_types_translations';

    protected $fillable = [
        'name',
    ];
}
