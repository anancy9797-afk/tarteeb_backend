<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Route;
use Modules\School\App\Models\SchoolSetting;
use Modules\Student\App\Jobs\ParentNotificationOnStudentRegisterWhatsAppJob;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// API Tester Route
Route::get('/api-tester', function () {
    return view('api-tester');
})->name('api-tester');

/** Test WhatsApp parent registration notification (local only; remove or tighten if needed). */
Route::get('/test/whatsapp-parent-register-notification', function () {
    abort_unless(app()->isLocal(), 404);

    $schoolSettings = SchoolSetting::query()
        ->whereNotNull('ultramsg_token')
        ->whereNotNull('ultramsg_instance_id')
        ->first();

    if (!$schoolSettings) {
        return response()->json([
            'ok' => false,
            'message' => 'No school_settings row with ultramsg_token and ultramsg_instance_id.',
        ], 422);
    }

    $student = (object) [
        'name' => 'Test Student',
        'identity_number' => '0000000000',
        'parent_phone' => '201099912408',
    ];

    Bus::dispatchSync(new ParentNotificationOnStudentRegisterWhatsAppJob(
        $student,
        $schoolSettings,
        'TestPassword123'
    ));

    return response()->json([
        'ok' => true,
        'message' => 'Job ran synchronously. Check storage/logs and WhatsApp on 201099912408.',
    ]);
})->name('test.whatsapp-parent-register-notification');
