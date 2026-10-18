<?php

namespace Modules\Maintenance\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Maintenance\App\Models\Maintenance;

class CheckMaintenanceStatusForUpdate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $maintenance = $request->route('maintenance');
        if ($maintenance->status == Maintenance::STATUS_ACCEPTED) {
            return returnMessage(false, 'Maintenance can only be updated when status is pending or rejected.', null, 'unprocessable_entity');
        }
        return $next($request);
    }
}
