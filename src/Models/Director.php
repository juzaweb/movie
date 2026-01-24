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

class Director extends Model implements TranslatableContract
{
    use HasAPI,
        HasMedia,
        HasThumbnail,
        HasUuids,
        Translatable,
        UsedInFrontend,
        MenuBoxable;

    protected $table = 'movie_directors';

    protected $translationForeignKey = 'movie_director_id';

    protected $fillable = [
        'name',
    ];

    public $translatedAttributes = [
        'slug',
        'bio',
        'locale',
    ];

    public $mediaChannels = ['thumbnail'];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_movie_director', 'movie_director_id', 'movie_id');
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder->withTranslation();
    }

    public function getEditUrl(): string
    {
        return route('admin.movie-directors.edit', [$this->id]);
    }

    public function getUrl(): string
    {
        return home_url("director/{$this->slug}", $this->locale);
    }
}
