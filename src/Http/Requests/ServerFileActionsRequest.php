<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    larabizcom/larabiz
 * @author     The Anh Dang
 * @link       https://cms.juzaweb.com
 */

namespace Juzaweb\Modules\Movie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\AllExist;

class ServerFileActionsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'action' => ['required', Rule::in(app(ServerFile::class)->bulkActions())],
            'ids' => ['required', 'array', 'min:1', new AllExist('server_files', 'id')],
        ];
    }
}
