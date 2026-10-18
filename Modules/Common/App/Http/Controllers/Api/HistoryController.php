<?php
namespace Modules\Common\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Common\Service\HistoryService;
use Modules\Common\App\resources\HistoryResource;

class HistoryController extends Controller
{
    private $historyService;

    public function __construct(HistoryService $historyService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager|Teacher|DataEntry');
        $this->historyService = $historyService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $histories = $this->historyService->findAll($data);
              // dd($histories);

        return returnMessage(true, 'Histories fetched successfully', HistoryResource::collection($histories)->response()->getData(true));
    }
}