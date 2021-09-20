<?php

namespace Juzaweb\Movie\Models\Video;

use Illuminate\Database\Eloquent\Model;
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
    
    public function movie() {
        return $this->hasOne('Juzaweb\Movie\Models\Movie\Movie', 'id', 'movie_id');
    }
    
    public function video_files() {
        return $this->hasMany('Juzaweb\Movie\Models\Video\VideoFiles', 'server_id', 'id');
    }
}
