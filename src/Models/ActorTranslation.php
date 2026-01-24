<?php

namespace Juzaweb\Modules\Movie\Models;

use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Traits\HasSlug;

class ActorTranslation extends Model
{
    use HasSlug;

    protected $table = 'movie_actor_translations';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'bio',
    ];
}
