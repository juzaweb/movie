<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Traits\HasDescription;
use Juzaweb\Modules\Core\Traits\HasSlug;

class LiveTvTranslation extends Model
{
    use HasDescription, HasSlug;

    protected $table = 'live_tv_translations';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'locale',
        'live_tv_id',
        'content',
        'description',
    ];

    public function liveTv(): BelongsTo
    {
        return $this->belongsTo(LiveTv::class, 'live_tv_id', 'id');
    }
}
