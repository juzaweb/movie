<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Juzaweb\Modules\Core\FileManager\Traits\HasMedia;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\MenuBoxable;
use Juzaweb\Modules\Core\Traits\HasAPI;
use Juzaweb\Modules\Core\Traits\HasFrontendUrl;
use Juzaweb\Modules\Core\Traits\HasThumbnail;
use Juzaweb\Modules\Core\Traits\Translatable;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\Core\Translations\Contracts\Translatable as TranslatableContract;

class Actor extends Model implements TranslatableContract
{
    use HasAPI,
        HasMedia,
        HasThumbnail,
        HasUuids,
        Translatable,
        UsedInFrontend,
        HasFrontendUrl,
        MenuBoxable;

    protected $table = 'movie_actors';

    protected $translationForeignKey = 'movie_actor_id';

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
        return $this->belongsToMany(Movie::class, 'movie_movie_actor', 'movie_actor_id', 'movie_id');
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder->withTranslation();
    }

    public function scopeAdditionSearch(Builder $builder, string $keyword): Builder
    {
        return $builder->orWhere('name', 'LIKE', "%{$keyword}%");
    }

    public function getEditUrl(): string
    {
        return route('admin.movie-actors.edit', [$this->id]);
    }

    public function getUrl(): string
    {
        return home_url("actor/{$this->slug}", $this->locale);
    }
}
