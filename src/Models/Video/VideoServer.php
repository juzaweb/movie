<?php

namespace Juzaweb\Movie\Models\Video;

use Illuminate\Database\Eloquent\Model;
use Juzaweb\Movie\Models\Movie\Movie;
use Juzaweb\Traits\ResourceModel;

/**
 * Juzaweb\Movie\Models\Video\VideoServer
 *
 * @property int $id
 * @property string $name
 * @property int $order
 * @property int $movie_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoServer whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Juzaweb\Movie\Models\Movie\Movie $movie
 * @property-read \Illuminate\Database\Eloquent\Collection|\Juzaweb\Movie\Models\Video\VideoFiles[] $video_files
 * @property-read int|null $video_files_count
 * @method static \Illuminate\Database\Eloquent\Builder|VideoServer whereFilter($params = [])
 */
class VideoServer extends Model
{
    use ResourceModel;

    protected $table = 'servers';
    protected $fillable = [
        'name',
        'order',
        'status',
        'movie_id'
    ];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereEnabled($builder)
    {
        return $builder->where('status', '=', 1);
    }
    
    public function movie()
    {
        return $this->hasOne(Movie::class, 'id', 'movie_id');
    }
    
    public function videoFiles()
    {
        return $this->hasMany(VideoFile::class, 'server_id', 'id');
    }
}
