<?php

namespace Juzaweb\Movie\Http\Controllers\Frontend\Auth;

use Juzaweb\Http\Controllers\BackendController;
use Juzaweb\Models\User;

class VerificationController extends BackendController
{
    public function index($token)
    {
        $user = User::where('verification_token', '=', $token)
            ->where('status', '=', 2)
            ->firstOrFail(['id']);
    
        User::where('id', '=', $user->id)->update([
            'status' => 1,
            'verification_token' => null,
        ]);
        
        \Auth::loginUsingId($user->id);
    
        return view('message', [
            'title' => trans('theme::app.verified_success'),
            'description' => trans('theme::app.verified_success_description'),
        ]);
    }
}
