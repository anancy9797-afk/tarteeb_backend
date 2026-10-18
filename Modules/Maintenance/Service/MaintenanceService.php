<?php

namespace Modules\Maintenance\Service;

use Illuminate\Support\Facades\File;
use Modules\Common\Helpers\UploadHelper;
use Modules\Maintenance\App\Models\Maintenance;
use Modules\Notification\Service\NotificationService;

class MaintenanceService
{
    use UploadHelper;
    function findAll($data = [], $relations = [])
    {
        $maintenances = Maintenance::query()
            ->available()
            ->with($relations)
            ->latest();
        return getCaseCollection($maintenances, $data);
    }

    function findMyMaintenances($data = [], $relations = [])
    {
        $maintenances = Maintenance::query()
            ->with($relations)
            ->where('user_id', auth('user')->id())
            ->latest();
        return getCaseCollection($maintenances, $data);
    }

    function findById($id, $relations = [])
    {
        return Maintenance::with($relations)->findOrFail($id);
    }

    function findBy($key, $value, $relations = [])
    {
        return Maintenance::where($key, $value)->with($relations)->get();
    }

    function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'maintenance');
        }
        $maintenance = Maintenance::create($data);
        return $maintenance;
    }

    function update($maintenance, $data)
    {
        if (request()->hasFile('image')) {
            if ($maintenance->image) {
                File::delete(public_path('uploads/maintenance/' . $this->getImageName('maintenance', $maintenance->image)));
            }
            $data['image'] = $this->upload(request()->file('image'), 'maintenance');
        }
        $maintenance->update($data);
        return $maintenance;
    }

    function totalCost()
    {
        return Maintenance::available()->where('status', Maintenance::STATUS_ACCEPTED)->sum('price');
    }

    public function acceptMultiple($data): void
    {
        $user = auth('user')->user();

        if ($user->hasRole('Super Admin')) {
            $maintenances = Maintenance::whereIn('id', $data['maintenance_ids'])
                ->where('status', '!=', Maintenance::STATUS_ACCEPTED)
                ->with('employee')
                ->get();

            Maintenance::whereIn('id', $data['maintenance_ids'])->where('status', '!=', Maintenance::STATUS_ACCEPTED)->update(['status' => Maintenance::STATUS_ACCEPTED]);
        } else {
            $maintenances = Maintenance::whereIn('id', $data['maintenance_ids'])
                ->where('school_id', $user->school_id)
                ->where('status', '!=', Maintenance::STATUS_ACCEPTED)
                ->with('employee')
                ->get();

            $allowedIds = $maintenances->pluck('id')->toArray();
            $notAllowedIds = array_diff($data['maintenance_ids'], $allowedIds);

            if (!empty($notAllowedIds)) {
                throw new \Exception(
                    'You are not allowed to accept maintenance in ids [' . implode(', ', $notAllowedIds) . ']'
                );
            }

            if (!empty($allowedIds))
                Maintenance::whereIn('id', $allowedIds)->update(['status' => Maintenance::STATUS_ACCEPTED]);

        }

        if (!empty($maintenances))
            $this->sendNotificationsForAcceptedMaintenances($maintenances);
    }

    public function sendNotificationToUser($maintenance)
    {
        if ($maintenance->status == Maintenance::STATUS_ACCEPTED) {
            $data = [
                'title' => 'تم قبول طلب الصيانة',
                'description' => 'تم قبول طلب الصيانة الخاص بك.',
            ];
        } elseif ($maintenance->status == Maintenance::STATUS_REJECTED) {
            $data = [
                'title' => 'تم رفض طلب الصيانة',
                'description' => 'تم رفض طلب الصيانة الخاص بك. السبب: ' . ($maintenance->reject_reason ?? 'لم يتم تحديد السبب'),
            ];
        }
        (new NotificationService())->sendNotificationToUser($data, $maintenance->user_id, 'maintenance');
    }

    public function sendNotificationsForAcceptedMaintenances($maintenances): void
    {
        if ($maintenances->isEmpty()) {
            return;
        }
        $userIds = [];
        $tokens = [];

        foreach ($maintenances as $maintenance) {
            if ($maintenance->employee) {
                $userIds[] = $maintenance->employee->id;
                if ($maintenance->employee->fcm_token) {
                    $tokens[] = $maintenance->employee->fcm_token;
                }
            }
        }

        if (empty($userIds)) {
            return;
        }

        $data = [
            'title' => 'تم قبول طلب الصيانة',
            'description' => 'تم قبول طلب الصيانة الخاص بك.',
        ];

        (new NotificationService())->sendNotificationToUsers($data, $userIds, $tokens, 'maintenance');
    }
}
