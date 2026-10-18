<?php

namespace Modules\User\App\Http\Controllers\Api;

use Modules\User\App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Student\App\Models\Student;
use Modules\User\App\resources\UserResource;
use Modules\User\App\Http\Requests\UserLoginRequest;

class UserAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function login(UserLoginRequest $request)
    // {
    //     try {
    //         $credentials = $request->validated();

    //         if (!$token = auth('user')->attempt($credentials)) {
    //             return returnValidationMessage(false, 'Unauthorized', ['password' => 'wrong credentials'], 'unauthorized');
    //         }
    //         $user = auth('user')->user();

    //         if ($user['is_active'] == 0) {
    //             return returnMessage(false, 'In-Active User Verification Required', null, 'temporary_redirect');
    //         }

    //         if ($request['fcm_token'] ?? null) {
    //             $user->update(['fcm_token' => $request->fcm_token]);
    //         }

    //         // if ($user->hasRole('Student')) {
    //         //     $student = $user->student;
    //         //     if (!$student->canLogin()['status']) {
    //         //         return returnMessage(false, $student->canLogin()['message'], null, 'temporary_redirect');
    //         //     }
    //         // }

    //         return $this->respondWithToken($token);

    //     } catch (\Exception $e) {
    //         return returnMessage(false, $e->getMessage(), null, 'server_error');
    //     }
    // }
  public function login(UserLoginRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $credentials = $request->credentials();

            if (isset($credentials['user_id'])) {
                if (!$credentials['user_id']) {
                    return returnValidationMessage(false,'Unauthorized',['identity_number' => 'Invalid identity number'],'unauthorized');
                }
                $user = User::find($credentials['user_id']);
                if (!$user) {
                    return returnValidationMessage(false,'Unauthorized',['identity_number' => 'Student account not found'],'unauthorized');
                }
                if (!Hash::check($credentials['password'], $user->password)) {
                    return returnValidationMessage(false,'Unauthorized',['password' => 'Wrong password'],'unauthorized');
                }
                $token = auth('user')->login($user);
            } else {
                if (!$token = auth('user')->attempt($credentials)) {
                    return returnValidationMessage(false,'Unauthorized',['password' => 'Wrong credentials'],'unauthorized');
                }
            }
            $user = auth('user')->user()->load('roles');
            if ($user->is_active == 0) {
                return returnMessage(false,'In-Active User Verification Required',null,'temporary_redirect');
            }
            if ($request->filled('fcm_token')) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }

            // if ($request->filled('identity_number')) {
            //     $student = Student::where('identity_number', $request->identity_number)->first();

                // if ($student) {
                //     $canLoginResult = $student->canLogin();
                //     if (!$canLoginResult['status']) {
                //         auth('user')->logout();
                //         return returnMessage(false,$canLoginResult['message'],null,'temporary_redirect');
                //     }
                // }
            // }

            return $this->respondWithToken($token);

        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return returnMessage(true, 'User Data',  new UserResource(auth('user')->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return returnMessage(true, 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return returnMessage(true, 'Successfully Logged in', [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('user')->factory()->getTTL() * 60,
            'user' => new UserResource(auth('user')->user()),
        ]);
    }
}
