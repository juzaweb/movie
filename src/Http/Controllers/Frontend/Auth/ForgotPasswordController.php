<?php

namespace Juzaweb\Movie\Http\Controllers\Frontend\Auth;

use Juzaweb\Http\Controllers\FrontendController;
use Juzaweb\Movie\Models\Email\EmailList;
use Juzaweb\Models\PasswordReset;
use Juzaweb\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends FrontendController
{
    public function handle(Request $request)
    {
        $this->validateRequest([
            'email' => 'required',
        ], $request, [
            'email' => trans('theme::app.email')
        ]);
        
        $email = $request->post('email');
        $user = $this->checkEmailExists($email);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => trans('theme::app.email_does_not_exist'),
            ]);
        }
        
        $token = generate_token($email);
        $model = PasswordReset::firstOrNew(['email' => $email]);
        $model->email = $email;
        $model->token = $token;
        $model->save();
        
        $mail = new EmailList();
        $mail->emails = $email;
        $mail->params = json_encode([
            'name' => $user->name,
            'email' => $user->email,
            'url' => route('password.reset', [$token]),
        ]);
    
        $mail->sendByTemplate('forgot_password');
        return response()->json([
            'status' => 'success',
            'redirect' => route('password.forgot.success'),
        ]);
    }
    
    public function message()
    {
        return view('message', [
            'title' => trans('theme::app.forgot_message'),
            'description' => trans('theme::app.forgot_message_description'),
        ]);
    }
    
    protected function checkEmailExists($email)
    {
        return User::where('email', $email)
            ->where('status', 1)
            ->first();
    }
}
