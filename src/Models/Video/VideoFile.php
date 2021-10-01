<?php

namespace Juzaweb\Movie\Models\Video;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Juzaweb\Traits\ResourceModel;

/**
 * Juzaweb\Movie\Models\Video\VideoFile
 *
 * @property int $id
 * @property int $server_id
 * @property int $movie_id
 * @property string $label
 * @property int $order
 * @property string $source
 * @property string $url
 * @property string|null $video_240p
 * @property string|null $video_360p
 * @property string|null $video_480p
 * @property string|null $video_720p
 * @property string|null $video_1080p
 * @property string|null $video_2048p
 * @property string|null $video_4096p
 * @property int $converted
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereConverted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereVideo1080p($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereVideo2048p($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereVideo240p($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereVideo360p($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereVideo4096p($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereVideo480p($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Video\VideoFile whereVideo720p($value)
 * @mixin \Eloquent
 * @property int $enable_remote
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFile whereEnableRemote($value)
 * @property-read \Juzaweb\Movie\Models\Video\VideoServer|null $server
 * @property-read \Illuminate\Database\Eloquent\Collection|\Juzaweb\Movie\Models\Subtitle[] $subtitles
 * @property-read int|null $subtitles_count
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFile whereFilter($params = [])
 */
class VideoFile extends Model
{
    use ResourceModel;

    protected $fieldName = 'label';
    protected $table = 'video_files';
    protected $primaryKey = 'id';
    protected $fillable = [
        'label',
        'order',
        'source',
        'url',
        'status',
        'server_id',
        'movie_id'
    ];
    
    public function server()
    {
        return $this->belongsTo(VideoServer::class, 'server_id', 'id');
    }
    
    public function getFiles() {
        switch ($this->source) {
            case 'youtube';
                return $this->getVideoYoutube();
            case 'vimeo':
                return $this->getVideoVimeo();
            case 'upload':
                return $this->getVideoUpload();
            case 'gdrive':
                return $this->getVideoGoogleDrive();
            case 'mp4';
                return $this->getVideoUrl('mp4');
            case 'mkv';
                return $this->getVideoUrl('mkv');
            case 'webm':
                return $this->getVideoUrl('webm');
            case 'm3u8':
                return $this->getVideoUrl('m3u8');
            case 'embed':
                return $this->getVideoUrl('embed');
        }
        
        return [];
    }
    
    public function subtitles()
    {
        return $this->hasMany('Juzaweb\Movie\Models\Subtitle', 'file_id', 'id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereEnabled($builder)
    {
        return $builder->where('status', '=', 1);
    }

    public function isSourceEmbed() {
        $embed_source = ['embed', 'youtube', 'vimeo'];
        if (!get_config('stream3s_use') || get_config('stream3s_link') != 'direct') {
            $embed_source[] = 'gdrive';
        }
        
        if (in_array($this->source, $embed_source)) {
            return true;
        }
        return false;
    }
    
    protected function getExtension() {
        $file_name = basename($this->url);
        return explode('.', $file_name)[count(explode('.', $file_name)) - 1];
    }
    
    protected function getVideoYoutube() {
        return [
            (object) [
                'file' => 'https://www.youtube.com/embed/' . get_youtube_id($this->url),
                'type' => 'mp4',
            ]
        ];
    }
    
    protected function getVideoVimeo() {
        return [
            (object) [
                'file' => 'https://player.vimeo.com/video/' . get_vimeo_id($this->url),
                'type' => 'mp4',
            ]
        ];
    }
    
    protected function getVideoUrl($type) {
        
        if (!is_url($this->url)) {
            return $this->getVideoUpload();
        }
        
        $tracks = $this->subtitles()
            ->where('status', '=', 1)
            ->get([
                \DB::raw("'captions' AS kind"),
                'url AS file',
                'label'
            ])->toArray();
        
        if ($tracks) {
            
            return [
                (object) [
                    'file' => $this->url,
                    'type' => $type,
                    'tracks' => $tracks,
                ]
            ];
        }
        
        return [
            (object) [
                'file' => $this->url,
                'type' => $type,
            ]
        ];
    }
    
    protected function getVideoUpload() {
        if ($this->converted == 1) {
            $files = [];
            if ($this->video_240p) {
                $files[] = (object) [
                    'label' => '240p',
                    'type' => $this->getExtension(),
                    'file' => $this->generateStreamUrl($this->video_240p),
                ];
            }
    
            if ($this->video_360p) {
                $files[] = (object) [
                    'label' => '360p',
                    'type' => $this->getExtension(),
                    'file' => $this->generateStreamUrl($this->video_360p),
                ];
            }
    
            if ($this->video_480p) {
                $files[] = (object) [
                    'label' => '480p',
                    'type' => $this->getExtension(),
                    'file' => $this->generateStreamUrl($this->video_480p),
                ];
            }
    
            if ($this->video_720p) {
                $files[] = (object) [
                    'label' => '720p',
                    'type' => $this->getExtension(),
                    'file' => $this->generateStreamUrl($this->video_720p),
                ];
            }
    
            if ($this->video_1080p) {
                $files[] = (object) [
                    'label' => '1080p',
                    'type' => $this->getExtension(),
                    'file' => $this->generateStreamUrl($this->video_1080p),
                ];
            }
    
            if ($this->video_2048p) {
                $files[] = (object) [
                    'label' => '2048p',
                    'type' => $this->getExtension(),
                    'file' => $this->generateStreamUrl($this->video_2048p),
                ];
            }
    
            if ($this->video_4096p) {
                $files[] = (object) [
                    'label' => '4096p',
                    'type' => $this->getExtension(),
                    'file' => $this->generateStreamUrl($this->video_4096p),
                ];
            }
            
            if (count($files) > 0) {
                return $files;
            }
        }
        
        return [
            (object) [
                'file' => $this->generateStreamUrl($this->url),
                'type' => $this->getExtension(),
            ]
        ];
    }
    
    protected function getVideoGoogleDrive()
    {
        $use_stream = get_config('use_stream', 1);
        
        if (empty($use_stream)) {
            return $this->getVideoGoogleDriveEmbed();
        }
        
        $gdrive = GoogleDrive::link_stream(get_google_drive_id($this->url));
        if ($gdrive) {
        
            $files = [];
            foreach ($gdrive->qualities as $quality) {
                $file = [
                    'class' => 'GoogleDrive',
                    'file' => $gdrive->stream_id
                ];
            
                $token = urlencode(base64_encode(Crypt::encryptString(json_encode($file))));
            
                $files[] = (object)[
                    'label' => $quality,
                    'file' => route('stream.service', [
                        $token, $quality,
                        $quality . '.mp4'
                    ]),
                    'type' => 'mp4'
                ];
            }
        
            return $files;
        }
    
        return [];
    }
    
    protected function getVideoGoogleDriveEmbed() {
        $files[] = (object) [
            'file' => 'https://drive.google.com/file/d/'. get_google_drive_id($this->url) .'/preview',
            'type' => 'mp4',
        ];
    
        return $files;
    }
    
    protected function generateStreamUrl($path) {
        $token = generate_token(basename($path));
        $file = json_encode([
            'path' => $path,
        ]);
        
        $file = \Crypt::encryptString($file);
        
        return $this->getStreamLink($token, $file, basename($path));
    }
    
    protected function getStreamLink($token, $file, $name) {
        return route('stream.video', [$token, base64_encode($file), $name]);
    }
}
