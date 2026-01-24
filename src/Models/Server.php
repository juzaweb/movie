<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Juzaweb\Modules\Core\Models\Model;

class Server extends Model
{
    use HasUuids;

    protected $table = 'servers';

    protected $fillable = [
        'name',
        'display_order',
        'active',
        'movie_id',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'active' => 'boolean',
    ];

    public function serverFiles(): HasMany
    {
        return $this->hasMany(ServerFile::class, 'server_id', 'id');
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'id');
    }
}
