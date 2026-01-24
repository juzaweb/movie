<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Juzaweb\Modules\Core\FileManager\Traits\HasMedia;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\MenuBoxable;
use Juzaweb\Modules\Core\Traits\HasAPI;
use Juzaweb\Modules\Core\Traits\HasThumbnail;
use Juzaweb\Modules\Core\Traits\Translatable;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\Core\Translations\Contracts\Translatable as TranslatableContract;

class Writer extends Model implements TranslatableContract
{
    use HasAPI,
        HasMedia,
        HasThumbnail,
        HasUuids,
        Translatable,
        UsedInFrontend,
        MenuBoxable;

    protected $table = 'movie_writers';

    protected $translationForeignKey = 'movie_writer_id';

    protected $fillable = [
        'name',
    ];

    public $translatedAttributes = [
        'slug',
        'bio',
        'locale',
    ];

    protected $appends = [
        'thumbnail',
    ];

    public $mediaChannels = ['thumbnail'];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_movie_writer', 'movie_writer_id', 'movie_id');
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder->withTranslation();
    }

    public function getEditUrl(): string
    {
        return route('admin.movie-writers.edit', [$this->id]);
    }

    public function getUrl(): string
    {
        return home_url("writer/{$this->slug}", $this->locale);
    }
}
