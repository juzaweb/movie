<?php

namespace Juzaweb\Modules\Movie\Models;

use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Traits\HasSlug;

class WriterTranslation extends Model
{
    use HasSlug;

    protected $table = 'movie_writer_translations';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'bio',
    ];
}
