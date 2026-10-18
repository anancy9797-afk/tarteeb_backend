<?php

namespace Modules\Purchase\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Purchase\App\Models\Purchase;

class CheckPurchaseStatusForUpdate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $purchase = $request->route('purchase');
        if ($purchase->status == Purchase::STATUS_ACCEPTED) {
            return returnMessage(false, 'Purchase can only be updated when status is pending or rejected.', null, 'unprocessable_entity');
        }
        return $next($request);
    }
}
