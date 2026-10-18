<?php

namespace Modules\Maintenance\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Maintenance\DTO\MaintenanceDto;
use Modules\Maintenance\App\Models\Maintenance;
use Modules\Maintenance\Service\MaintenanceService;
use Modules\Notification\Service\NotificationService;
use Modules\Maintenance\App\Http\Requests\MaintenanceRequest;
use Modules\Maintenance\App\Http\Middleware\CheckMaintenanceStatusForUpdate;

class MaintenanceController extends Controller
{
    private $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Maintenance Employee');
        $this->middleware(CheckMaintenanceStatusForUpdate::class)->only('update');
        $this->maintenanceService = $maintenanceService;
    }

    public function myMaintenances(Request $request)
    {
        $maintenances = $this->maintenanceService->findMyMaintenances($request->all());
        return returnMessage(true, 'Maintenances fetched successfully', $maintenances);
    }

    public function store(MaintenanceRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new MaintenanceDto($request, true))->dataFromRequest();
            $maintenance = $this->maintenanceService->create($data);
            $this->sendNotificationToAdmins($maintenance);
            DB::commit();
            return returnMessage(true, 'Maintenance created successfully', $maintenance);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(MaintenanceRequest $request, Maintenance $maintenance)
    {
        try {
            DB::beginTransaction();
            $data = (new MaintenanceDto($request))->dataFromRequest();
            $maintenance = $this->maintenanceService->update($maintenance, $data);
            $this->sendNotificationToAdmins($maintenance, 'update');
            DB::commit();
            return returnMessage(true, 'Maintenance updated successfully', $maintenance);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    private function sendNotificationToAdmins($maintenance, $update = false)
    {
        $data = [
            'title' => $update ? 'تم تحديث طلب صيانة رقم : ' . $maintenance->id : 'رقم : ' . $maintenance->id . ' تم إنشاء طلب صيانة',
            'description' => $update ? 'تم تحديث طلب صيانة بواسطة موظف الصيانة: ' . auth('user')->user()->name : 'تم إنشاء طلب صيانة جديد بواسطة موظف الصيانة: ' . auth('user')->user()->name,
        ];
        (new NotificationService())->sendNotificationToAdmins($data, $maintenance->school_id, 'maintenance');
    }
}
