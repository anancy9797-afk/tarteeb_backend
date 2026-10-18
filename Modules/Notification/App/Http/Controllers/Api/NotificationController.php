<?php

namespace Modules\Notification\App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\User\App\Models\User;
use Illuminate\Routing\Controller;
use Modules\Notification\App\Models\Notification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    public function index()
    {
        $notifications = auth('user')
            ->user()
            ->notifications()
            ->orderByDesc('id')
            ->get()
            ->groupBy('group_by');

        return returnMessage(true, 'User Notifications', $notifications);
    }

    public function readNotification(Request $request)
    {
        Notification::whereIn('id', $request['notifications_ids'])->update(['read_at' => Carbon::now()]);
        return returnMessage(true, 'Notification read successfully');
    }

    public function unReadNotificationsCount()
    {
        $unReadCount = Notification::whereNull('read_at')->whereHasMorph('notifiable', [User::class], function ($query) {
            $query->where('notifiable_id', auth('user')->id());
        })->count();
        return returnMessage(true, 'un Read Notifications Count', $unReadCount);
    }
}
