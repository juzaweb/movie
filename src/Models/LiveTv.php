<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Juzaweb\Modules\Core\FileManager\Traits\HasMedia;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Traits\HasAPI;
use Juzaweb\Modules\Core\Traits\HasThumbnail;
use Juzaweb\Modules\Core\Traits\Translatable;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;

class LiveTv extends Model
{
    use HasAPI, HasMedia, HasThumbnail, HasUuids,  Translatable, UsedInFrontend;

    protected $table = 'live_tvs';

    protected $fillable = [
        'streaming_url',
        'views',
    ];

    public $translatedAttributes = [
        'name',
        'slug',
        'description',
        'content',
        'locale',
    ];

    public $mediaChannels = [
        'thumbnail',
    ];

    protected $casts = [
        'views' => 'integer',
    ];

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnail;
    }

    public function getUrlAttribute(): string
    {
        return route('live-tv.show', ['slug' => $this->slug]);
    }
}
