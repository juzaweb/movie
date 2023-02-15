<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/juzacms
 * @author     Juzaweb Team <admin@juzaweb.com>
 * @link       https://juzaweb.com
 * @license    GNU General Public License v2.0
 */

namespace Juzaweb\Movie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Resource;

class ReportRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'type' => [
                'required',
                'in:bug,die_link,other'
            ],
            'description' => [
                'required',
                'max:500',
            ],
            'post_id' => [
                'required',
                Rule::modelExists(
                    Post::class,
                    'id',
                    fn($q) => $q->where('type', 'movies')
                ),
            ],
            'video_id' => [
                'required',
                Rule::modelExists(
                    Resource::class,
                    'id',
                    fn($q) => $q->where('type', 'files')
                ),
            ]
        ];

        if (get_config('captcha')) {
            $rules['g-recaptcha-response'] = 'bail|required|recaptcha';
        }

        return $rules;
    }
}
