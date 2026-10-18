<?php

namespace Modules\User\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\User\Service\UserService;
use Modules\User\App\Http\Requests\UpdateProfileRequest;
use Modules\User\App\Http\Requests\ChangePasswordRequest;

class UserController extends Controller
{
    protected $userService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth:user');
        $this->userService = $userService;
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try{
            DB::beginTransaction();
            $this->userService->changePassword($request->validated());
            DB::commit();
            return returnMessage(true, 'Password Changed Successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return returnMessage(false, $e->getMessage(),null ,500);
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try{
            DB::beginTransaction();
            $this->userService->updateProfile($request->validated());
            DB::commit();
            return returnMessage(true, 'Profile Updated Successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return returnMessage(false, $e->getMessage(),null ,500);
        }
    }
}
