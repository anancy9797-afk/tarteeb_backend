<?php

namespace Modules\Purchase\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Purchase\DTO\PurchaseDto;
use Modules\Purchase\App\Models\Purchase;
use Modules\Purchase\Service\PurchaseService;
use Modules\Notification\Service\NotificationService;
use Modules\Purchase\App\Http\Requests\PurchaseRequest;
use Modules\Purchase\App\Http\Middleware\CheckPurchaseStatusForUpdate;

class PurchaseController extends Controller
{
    private $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Purchasing Employee');
        $this->middleware(CheckPurchaseStatusForUpdate::class)->only('update');
        $this->purchaseService = $purchaseService;
    }

    public function myPurchases(Request $request)
    {
        $purchases = $this->purchaseService->findMyPurchases($request->all());
        return returnMessage(true, 'Purchases fetched successfully', $purchases);
    }

    public function store(PurchaseRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new PurchaseDto($request, true))->dataFromRequest();
            $purchase = $this->purchaseService->create($data);
            $this->sendNotificationToAdmins($purchase);
            DB::commit();
            return returnMessage(true, 'Purchase created successfully', $purchase);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        try {
            DB::beginTransaction();
            $data = (new PurchaseDto($request))->dataFromRequest();
            $purchase = $this->purchaseService->update($purchase, $data);
            $this->sendNotificationToAdmins($purchase, 'update');
            DB::commit();
            return returnMessage(true, 'Purchase updated successfully', $purchase);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    private function sendNotificationToAdmins($purchase, $update = false)
    {
        $data = [
            'title' => $update ? 'تم تحديث طلب شراء رقم : ' . $purchase->id : 'رقم : ' . $purchase->id . ' تم إنشاء طلب شراء',
            'description' => $update ? 'تم تحديث طلب شراء بواسطة موظف المشتريات: ' . auth('user')->user()->name : 'تم إنشاء طلب شراء جديد بواسطة موظف المشتريات: ' . auth('user')->user()->name,
        ];
        (new NotificationService())->sendNotificationToAdmins($data, $purchase->school_id, 'purchase');
    }
}
