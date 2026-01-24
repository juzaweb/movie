<?php

namespace Juzaweb\Modules\Movie\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Juzaweb\Modules\Core\Models\Model;

class ReportType extends Model
{
    use HasUuids,  Translatable;

    protected $table = 'report_types';

    protected $fillable = [];

    public $translatedAttributes = [
        'name',
        'locale',
    ];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
