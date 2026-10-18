<?php

namespace Modules\User\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Modules\User\App\Http\Requests\ForgotPasswordRequest;
use Modules\User\App\Http\Requests\ResetPasswordRequest;
use Modules\User\App\Notifications\UserResetPassword;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link to user's email
     * React Frontend: Call this when user submits forgot password form
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            // Rate limiting: max 5 attempts per hour per email
            $key = 'password-reset:' . $request->email;
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return returnMessage(false, 'Too many password reset attempts. Please try again later.', null, 'too_many_requests');
            }
            $status = Password::broker('users')->sendResetLink(
                $request->only('email'),
                function ($user, $token) {
                    $user->notify(new UserResetPassword($token));
                }
            );

            RateLimiter::hit($key, 3600); // 1 hour

            if ($status === Password::RESET_LINK_SENT) {
                return returnMessage(true, 'Password reset link sent to your email', [
                    'email' => $request->email,
                ]);
            }

            return returnMessage(false, 'Unable to send reset link. Please try again.', null, 'server_error');

        } catch (\Exception $e) {
            return returnMessage(false, 'An error occurred. Please try again later.', null, 'server_error');
        }
    }

    /**
     * Validate reset token and email
     * React Frontend: Call this when user lands on reset password page
     */
    public function validateResetToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email'
        ]);

        // Laravel stores tokens with bcrypt, not sha256
        $tokenRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRecord) {
            return returnMessage(false, 'No reset token found for this email', null, 'unprocessable_entity');
        }

        // Check if token is expired
        $isExpired = now()->gt(\Carbon\Carbon::parse($tokenRecord->created_at)->addHour());

        if ($isExpired) {
            return returnMessage(false, 'Reset token has expired', null, 'unprocessable_entity');
        }

        // Verify the token using Laravel's Hash facade (bcrypt verification)
        $tokenMatches = \Hash::check($request->token, $tokenRecord->token);

        return returnMessage(true, $tokenMatches ? 'Valid reset token' : 'Invalid reset token', [
            'valid' => $tokenMatches,
            'email' => $request->email
        ], $tokenMatches ? 'ok' : 'unprocessable_entity');
    }

    /**
     * Reset user's password using token
     * React Frontend: Call this when user submits new password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            // Laravel's password reset broker handles bcrypt token verification automatically
            $status = Password::broker('users')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => bcrypt($password)
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return returnMessage(true, 'Password has been reset successfully', [
                    'email' => $request->email,
                    'redirect' => '/login'
                ]);

            }

            $message = match ($status) {
                Password::INVALID_TOKEN => 'This password reset token is invalid or has expired',
                Password::INVALID_USER => 'We can\'t find a user with that email address',
                default => 'Unable to reset password. Please try again'
            };

            return returnMessage(false, $message, null, 'unprocessable_entity');

        } catch (\Exception $e) {
            return returnMessage(false, 'An error occurred while resetting password. Please try again.', null, 'server_error');
        }
    }
}
