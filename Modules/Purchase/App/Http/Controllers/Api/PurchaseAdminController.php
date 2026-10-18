<?php

namespace Modules\Purchase\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Purchase\App\Http\Requests\PurchaseAcceptRequest;
use Modules\Purchase\App\Models\Purchase;
use Modules\Purchase\Service\PurchaseService;
use Modules\Notification\Service\NotificationService;

class PurchaseAdminController extends Controller
{
    private $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager|Financial Director');
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        $relations = ['employee', 'school'];
        $purchases = $this->purchaseService->findAll($request->all(), $relations);
        $totalCost = $this->purchaseService->totalCost();
        return returnMessage(true, 'Purchases fetched successfully', [
            'data' => $purchases,
            'total_cost' => $totalCost,
        ]);
    }

    public function accept(PurchaseAcceptRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->purchaseService->acceptMultiple($request->validated());
            DB::commit();
            return returnMessage(true, 'Purchase accepted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, 'Failed to accept purchase', null, 'server_error');
        }
    }

    public function reject(Request $request, Purchase $purchase)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:255'
        ]);
        try {
            DB::beginTransaction();
            $purchase->update([
                'status' => Purchase::STATUS_REJECTED,
                'reject_reason' => $request->reject_reason
            ]);
            $this->sendNotificationToUser($purchase);
            DB::commit();
            return returnMessage(true, 'Purchase rejected successfully', $purchase);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, 'Failed to reject purchase', null, 'server_error');
        }
    }

    public function sendNotificationToUser($purchase)
    {
        $data = [
            'title' => 'تم رفض طلب الشراء',
            'description' => 'تم رفض طلب الشراء الخاص بك. السبب: ' . ($purchase->reject_reason ?? 'لم يتم تحديد السبب'),
        ];
        (new NotificationService())->sendNotificationToUser($data, $purchase->user_id, 'purchase');
    }
}
