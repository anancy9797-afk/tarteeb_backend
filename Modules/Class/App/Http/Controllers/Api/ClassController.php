<?php

namespace Modules\Class\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Modules\Class\DTO\ClassDto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Class\App\Models\Classroom;
use Modules\Class\Service\ClassService;
use Modules\Class\App\resources\ClassResource;
use Modules\Class\App\Http\Requests\ClassRequest;

class ClassController extends Controller
{
   private $classService;
  //  public function __construct(ClassService $classService){
  //     $this->middleware('auth:user');
  //     $this->middleware('role:Super Admin|School Manager|Teacher');
  //     $this->middleware('permission:Index-class', ['only' => ['index']]);
  //     $this->middleware('permission:Create-class', ['only' => ['store']]);
  //     $this->middleware('permission:Edit-class', ['only' => ['update', 'activate']]);
  //     $this->middleware('permission:Delete-class', ['only' => ['destroy']]);
  //     $this->classService = $classService;
  //  }


public function __construct(ClassService $classService)
{
    $this->middleware('auth:user')->except(['index']);

    $this->middleware('role:Super Admin|School Manager|Teacher')
        ->except(['index']);

    $this->middleware('permission:Create-class')->only(['store']);

    $this->middleware('permission:Edit-class')
        ->only(['update', 'activate']);

    $this->middleware('permission:Delete-class')->only(['destroy']);

    $this->classService = $classService;
}
   public function index(Request $request){
      $data = $request->all();
      $classes = $this->classService->findAll($data);
      return returnMessage(true, 'Classes Fetched Successfully', ClassResource::collection($classes)->response()->getData(true));
   }

   public function store(ClassRequest $request){
      try{
         DB::beginTransaction();
         $data = (new ClassDto($request))->dataFromRequest();
         $class = $this->classService->create($data);
         DB::commit();
         return returnMessage(true, 'Class Created Successfully', $class);
      }catch(Exception $e){
         DB::rollBack();
         return returnMessage(false, $e->getMessage(), null, 500);
      }
   }

   public function update(ClassRequest $request, Classroom $class){
      try{
         DB::beginTransaction();
         $data = (new ClassDto($request))->dataFromRequest();
         $class = $this->classService->update($class, $data);
         DB::commit();
         return returnMessage(true, 'Class Updated Successfully', $class);
      }catch(Exception $e){
         DB::rollBack();
         return returnMessage(false, $e->getMessage(), null, 500);
      }
   }
}
