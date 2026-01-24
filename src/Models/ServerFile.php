<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Juzaweb\Modules\Core\Enums\VideoSource;
use Juzaweb\Modules\Core\Models\Model;

class ServerFile extends Model
{
    use HasUuids;

    protected $table = 'server_files';

    protected $fillable = [
        'path',
        'source',
        'name',
        'disk',
        'server_id',
    ];

    protected $casts = [
        'source' => VideoSource::class,
    ];

    protected $appends = [
        'mime_type',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id', 'id');
    }

    public function subtitles(): HasMany
    {
        return $this->hasMany(ServerFileSubtitle::class, 'file_id', 'id');
    }

    public function getMimeTypeAttribute(): string
    {
        return $this->source->getMimeType();
    }

    public function getVideoUrl(): string
    {
        return $this->source->getUrl($this->path);
    }
}
