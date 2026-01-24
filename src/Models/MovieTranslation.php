<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Builder;
use Juzaweb\Modules\Core\Contracts\Sitemapable;
use Juzaweb\Modules\Core\Enums\PostStatus;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Traits\HasDescription;
use Juzaweb\Modules\Core\Traits\HasSitemap;
use Juzaweb\Modules\Core\Traits\HasSlug;

class MovieTranslation extends Model implements Sitemapable
{
    use HasSlug, HasDescription, HasSitemap;

    public $timestamps = false;

    protected $table = 'movie_translations';

    protected $fillable = [
        'movie_id',
        'locale',
        'name',
        'content',
        'description',
        'slug',
    ];

    public function scopeForSitemap(Builder $builder): Builder
    {
        // Default: return all records ordered by updated_at
        return $builder
            ->join('movies', 'movie_translations.movie_id', '=', 'movies.id')
            ->where('movies.status', PostStatus::PUBLISHED->value)
            ->select(['movie_translations.*', 'movies.is_tv_series'])
            ->cacheDriver('file')
            ->cacheFor(3600 * 24)
            ->orderBy('id', 'asc');
    }

    public function getUrl(): string
    {
        $prefix = $this->is_tv_series ? 'tv-serie' : 'movie';

        if ($this->locale != setting('language')) {
            return home_url("{$this->locale}/{$prefix}/{$this->slug}");
        }

        return home_url("{$prefix}/{$this->slug}");
    }
}
