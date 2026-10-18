<?php

namespace Modules\Maintenance\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Maintenance\App\Http\Requests\MaintenanceAcceptRequest;
use Modules\Maintenance\App\Models\Maintenance;
use Modules\Maintenance\Service\MaintenanceService;

class MaintenanceAdminController extends Controller
{
    private $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager|Financial Director');
        $this->maintenanceService = $maintenanceService;
    }

    public function index(Request $request)
    {
        $relations = ['employee', 'school'];
        $maintenances = $this->maintenanceService->findAll($request->all(), $relations);
        $totalCost = $this->maintenanceService->totalCost();
        return returnMessage(true, 'Maintenances fetched successfully', [
            'data' => $maintenances,
            'total_cost' => $totalCost,
        ]);
    }

    public function accept(MaintenanceAcceptRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->maintenanceService->acceptMultiple($request->validated());
            DB::commit();
            return returnMessage(true, 'Maintenance accepted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, 'Failed to accept maintenance', null, 'server_error');
        }
    }

    public function reject(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:255'
        ]);
        try {
            DB::beginTransaction();
            $maintenance->update([
                'status' => Maintenance::STATUS_REJECTED,
                'reject_reason' => $request->reject_reason
            ]);
            $this->maintenanceService->sendNotificationToUser($maintenance);
            DB::commit();
            return returnMessage(true, 'Maintenance rejected successfully', $maintenance);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, 'Failed to reject maintenance', null, 'server_error');
        }
    }


}
