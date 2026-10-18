<?php

namespace Modules\Common\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Modules\Common\DTO\IntroDto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Common\App\Models\Intro;
use Modules\Common\Service\IntroService;
use Modules\Common\App\resources\IntroResource;
use Modules\Common\App\Http\Requests\IntroStoreRequest;
use Modules\Common\App\Http\Requests\IntroUpdateRequest;

class IntroController extends Controller
{
    protected $introService;
    public function __construct(IntroService $introService)
    {
        $this->middleware('auth:user')->except(['index']);
        $this->middleware('role:Super Admin')->except(['index']);
        $this->middleware('permission:Create-intro', ['only' => ['store']]);
        $this->middleware('permission:Edit-intro', ['only' => ['update']]);
        $this->middleware('permission:Delete-intro', ['only' => ['destroy']]);
        $this->introService = $introService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $intros = $this->introService->findAll($data);
        return returnMessage(true, 'Intros fetched successfully', IntroResource::collection($intros)->response()->getData(true));
    }

    public function store(IntroStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new IntroDto($request))->dataFromRequest();
            $intro = $this->introService->create($data);
            DB::commit();
            return returnMessage(true, 'Intro Created Successfully', $intro);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }
    public function update(IntroUpdateRequest $request, Intro $intro)
    {
        try {
            DB::beginTransaction();
            $data = (new IntroDto($request))->dataFromRequest();
            $intro = $this->introService->update($intro, $data);
            DB::commit();
            return returnMessage(true, 'Intro Updated Successfully', $intro);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function destroy(Intro $intro)
    {
        try {
            DB::beginTransaction();
            $intro = $this->introService->delete($intro);
            DB::commit();
            return returnMessage(true, 'Intro Deleted Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }
}