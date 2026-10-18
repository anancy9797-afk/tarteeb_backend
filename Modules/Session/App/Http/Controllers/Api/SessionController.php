<?php

namespace Modules\Session\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Imports\SessionsImport;
use Illuminate\Support\Facades\DB;
use Modules\Session\DTO\SessionDto;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Session\App\Models\Session;
use Modules\Session\Service\SessionService;
use Modules\Session\App\resources\SessionResource;
use Modules\School\App\Http\Requests\SchoolImportRequest;
use Modules\Session\App\Http\Requests\SessionStoreRequest;
use Modules\Session\App\Http\Requests\SessionUpdateRequest;
use Modules\Session\App\Http\Requests\SessionDestroyRequest;
use Modules\Session\App\Http\Requests\SessionDestroyMultipleRequest;

class SessionController extends Controller
{
    private $sessionService;
    public function __construct(SessionService $sessionService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager|Teacher');
        $this->middleware('permission:Index-session', ['only' => ['index']]);
        $this->middleware('permission:Create-session', ['only' => ['store', 'importSessions']]);
        $this->middleware('permission:Edit-session', ['only' => ['update', 'activate']]);
        $this->middleware('permission:Delete-session', ['only' => ['destroy','deleteMultiple']]);
        $this->sessionService = $sessionService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $sessions = $this->sessionService->findAll($data);
        return returnMessage(true, 'Sessions Fetched Successfully', SessionResource::collection($sessions)->response()->getData(true));
    }

    public function store(SessionStoreRequest $request)
    {
        $session = $this->sessionService->getSession($request->all());
        if ($session) {
            return returnMessage(false, 'Session Already Exists', null, 'bad_request');
        }
        try {
            DB::beginTransaction();
            $data = (new SessionDto($request))->dataFromRequest();
            $session = $this->sessionService->create($data);
            DB::commit();
            return returnMessage(true, 'Session Created Successfully', $session);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(SessionUpdateRequest $request, Session $session)
    {
        try {
            DB::beginTransaction();
            $data = (new SessionDto($request))->dataFromRequest();
            $session = $this->sessionService->update($session, $data);
            DB::commit();
            return returnMessage(true, 'Session Updated Successfully', $session);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(SessionDestroyRequest $request, Session $session)
    {
        try {
            DB::beginTransaction();
            $this->sessionService->delete($session);
            DB::commit();
            return returnMessage(true, 'Session Deleted Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function deleteMultiple(SessionDestroyMultipleRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $this->sessionService->deleteMultiple($data['session_ids']);
            DB::commit();
            return returnMessage(true, 'Sessions Deleted Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
    public function importSessions(SchoolImportRequest $request)
    {
        Excel::import(new SessionsImport, $request->file('file'));
        return returnMessage(true, 'Sessions Imported Successfully', null);
    }
}