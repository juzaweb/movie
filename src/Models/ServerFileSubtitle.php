<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Juzaweb\Modules\Core\Models\Model;

class ServerFileSubtitle extends Model
{
    use HasUuids;

    protected $table = 'server_file_subtitles';

    protected $fillable = [
        'label',
        'url',
        'language',
        'display_order',
        'active',
        'file_id',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'active' => 'boolean',
    ];

    public function serverFile(): BelongsTo
    {
        return $this->belongsTo(ServerFile::class, 'file_id', 'id');
    }
}
