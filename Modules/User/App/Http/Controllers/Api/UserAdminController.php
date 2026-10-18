<?php

namespace Modules\User\App\Http\Controllers\Api;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\User\Service\UserService;

class UserAdminController extends Controller
{

    public function __construct(private UserService $userService) {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager|Financial Director');
    }

    public function toggleActivate(User $user)
    {
        try {
            DB::beginTransaction();
            $user = $this->userService->toggleActivate($user);
            DB::commit();
            return returnMessage(true, 'User Updated Successfully', $user);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'error');
        }
    }
}
