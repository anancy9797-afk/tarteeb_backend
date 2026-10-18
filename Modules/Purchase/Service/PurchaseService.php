<?php

namespace Modules\Purchase\Service;

use Modules\Common\Helpers\UploadHelper;
use Modules\Purchase\App\Models\Purchase;
use Illuminate\Support\Facades\File;
use Modules\Notification\Service\NotificationService;

class PurchaseService
{
    use UploadHelper;
    function findAll($data = [], $relations = [])
    {
        $purchases = Purchase::query()
            ->available()
            ->with($relations)
            ->latest();
        return getCaseCollection($purchases, $data);
    }

    function findMyPurchases($data = [], $relations = [])
    {
        $purchases = Purchase::query()
            ->with($relations)
            ->where('user_id', auth('user')->id())
            ->latest();
        return getCaseCollection($purchases, $data);
    }

    function findById($id, $relations = [])
    {
        return Purchase::with($relations)->findOrFail($id);
    }

    function findBy($key, $value, $relations = [])
    {
        return Purchase::where($key, $value)->with($relations)->get();
    }

    function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'purchase');
        }
        $purchase = Purchase::create($data);
        return $purchase;
    }

    function update($purchase, $data)
    {
        if (request()->hasFile('image')) {
            if ($purchase->image) {
                File::delete(public_path('uploads/purchase/' . $this->getImageName('purchase', $purchase->image)));
            }
            $data['image'] = $this->upload(request()->file('image'), 'purchase');
        }
        $purchase->update($data);
        return $purchase;
    }

    function totalCost()
    {
        return Purchase::available()->where('status', Purchase::STATUS_ACCEPTED)->sum('price');
    }

    public function acceptMultiple($data): void
    {
        $user = auth('user')->user();

        if ($user->hasRole('Super Admin')) {
            $purchases = Purchase::whereIn('id', $data['purchase_ids'])
                ->where('status', '!=', Purchase::STATUS_ACCEPTED)
                ->with('employee')
                ->get();

            Purchase::whereIn('id', $data['purchase_ids'])->where('status', '!=', Purchase::STATUS_ACCEPTED)->update(['status' => Purchase::STATUS_ACCEPTED]);
        } else {
            $purchases = Purchase::whereIn('id', $data['purchase_ids'])
                ->where('school_id', $user->school_id)
                ->where('status', '!=', Purchase::STATUS_ACCEPTED)
                ->with('employee')
                ->get();

            $allowedIds = $purchases->pluck('id')->toArray();
            $notAllowedIds = array_diff($data['purchase_ids'], $allowedIds);

            if (!empty($notAllowedIds)) {
                throw new \Exception(
                    'You are not allowed to accept purchase in ids [' . implode(', ', $notAllowedIds) . ']'
                );
            }

            if (!empty($allowedIds))
                Purchase::whereIn('id', $allowedIds)->update(['status' => Purchase::STATUS_ACCEPTED]);

        }

        if (!empty($purchases))
            $this->sendNotificationsForAcceptedPurchases($purchases);
    }

    public function sendNotificationsForAcceptedPurchases($purchases): void
    {
        if ($purchases->isEmpty()) {
            return;
        }
        $userIds = [];
        $tokens = [];

        foreach ($purchases as $purchase) {
            if ($purchase->employee) {
                $userIds[] = $purchase->employee->id;
                if ($purchase->employee->fcm_token) {
                    $tokens[] = $purchase->employee->fcm_token;
                }
            }
        }

        if (empty($userIds)) {
            return;
        }

        $data = [
            'title' => 'تم قبول طلب الشراء',
            'description' => 'تم قبول طلب الشراء الخاص بك.',
        ];

        (new NotificationService())->sendNotificationToUsers($data, $userIds, $tokens, 'purchase');
    }
}
