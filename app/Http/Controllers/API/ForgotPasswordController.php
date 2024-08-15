<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
class ForgotPasswordController extends BaseController
{
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $response = Password::sendResetLink($request->only('email'));

        if($response == Password::RESET_LINK_SENT) {
            return $this->sendResponse('', 'Password reset link sent to your email address.');
        } else {
            return $this->sendError('Unable to send reset link.', ['Unable to send reset link.']);
        }
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $response = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
        });

        if($response == Password::RESET_LINK_SENT) {
            return $this->sendResponse('', 'Password has been reset.');
        } else {
            return $this->sendError('Failed to reset password.', ['Failed to reset password.']);
        }
    }
}
