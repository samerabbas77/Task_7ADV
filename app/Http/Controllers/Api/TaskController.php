<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Jobs\SaveDailyRepports;
use App\Traits\ApiResponseTrait;
use App\Services\Api\TaskServices;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Requests\task\StoreTaskRequest;
use App\Http\Requests\task\AddCommentRequest;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Requests\task\UpdateTaskRequest;
use App\Http\Requests\Task\UploadFileRequest;
use App\Http\Requests\Task\ReAssignTaskRequest;
use App\Http\Requests\task\UpdateTaskStatusRequest;

class TaskController extends Controller
{
    use ApiResponseTrait;
    protected $taskServices;

    public function __construct(TaskServices $taskServices)
    {
        $this->taskServices = $taskServices;

        $this->middleware('permission:task-list',             ['only' => ['index','getAllTaskswithFilters','getAllBluckedTasks','show']]);
        $this->middleware('permission:task-create',           ['only' => ['store']]);
        $this->middleware('permission:task-edit',             ['only' => ['update']]);
        $this->middleware('permission:task-delete',           ['only' => ['destroy']]);
        $this->middleware('permission:task_forceDelte',       ['only' => ['forceDestroy']]);
        $this->middleware('permission:task_trash',            ['only' => ['trashed']]);
        $this->middleware('permission:task_restore',          ['only' => ['restore']]);
        
        $this->middleware('permission:update_status',         ['only' => ['updateStatus']]);
        
        $this->middleware('permission:assign_task',           ['only' => ['assignTask']]);
        $this->middleware('permission:reAssign_task',         ['only' => ['reAssignTask']]);
        
        $this->middleware('permission:upload_attachment',     ['only' => ['uploadAttachment']]);
        $this->middleware('permission:add_comment',           ['only' => ['addComment']]);

        $this->middleware('permission:task_reports',          ['only' => ['taskReports']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $tasks = $this->taskServices->getAllTasks($perPage);
        return $this->success(TaskResource::collection($tasks), 'Get all tasks Successfully', 200);
    }
/*
    Get all tasks with its comments and attachments by filter
*/

    public function getAllTaskswithFilters(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $tasks = $this->taskServices->getAllTasksFiltersBy($perPage, $request->input('type'),
                                                          $request->input('status'),
                                                          $request->input('due_date'),
                                                          $request->input('priority'),
                                                          $request->input('assigned_to'),
                                                          $request->input('dependce_on'));
        return $this->success(TaskResource::collection($tasks), 'Get all tasks with comments and attachments Successfully', 200);
    }

    public function getAllBluckedTasks()
    {
        $tasks = $this->taskServices->getAllBluckedTasks();
        return $this->success(TaskResource::collection($tasks), 'Get all blucked tasks Successfully', 200);
        
    }

    //......................................................


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->only(['title','description','type','status','priority','due_date','task_dependency']);
        $user = $this->taskServices->createTask($validated);
        return $this->success(new TaskResource($user), 'Store task Successfully', 200);
    }



    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $this->success(new TaskResource($task), 'Show task Successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validated = $request->only(['title','description','type','priority','due_date','task_dependency']);
        $task = $this->taskServices->updateTask($validated,$task);
        return $this->success(new TaskResource($task),'Update task Successfully',200); 
    }

    //....................................Soft Delete..................................................
    /**
     * Remove the specified resource from storage.
     */
    //............Soft Delete....
    public function destroy(Task $task)
    {
        $this->taskServices->deleteTask($task);
        return $this->success(null,'Soft Deleting task Successfully',200);
    }

    //.............Force Delete....
    public function forceDestroy(string $id)
    {
        $this->taskServices->forceDeleteTask($id);
        return $this->success(null,'force Deleting task Successfully',200);
    }
    //..............Trashed.....

    public function trashed()
    {
        $users = $this->taskServices->getTrashedTasks();
        return $this->success(TaskResource::collection($users),'trashed tasks fetching successfully');

    }
    /**
     * Restore a trashed (soft deleted) resource by its ID.
     */
    public function restore(string $id)
    {
        $user = $this->taskServices->restoreTask($id);
        return $this->successResponse(new TaskResource($user), "User restored Successfully");
    }
    //.............................................END OF CRUD FUNCTIONS ...................................
    //.................................................................................................

    public function updateStatus(UpdateTaskStatusRequest $request,Task $task)
    {
        $validated = $request->only('status');
        $this->taskServices->updateStatus($validated,$task);
        return $this->success(null,'Update task status Successfully',200);  
    }

    

    //...........................................Assigne/Reassig Task.........................................

    public function assignTask( AssignTaskRequest $request,string $id)
    {
        $validated = $request->only('firstName','lastName');
        $task = $this->taskServices->assignTask($validated,$id);
        return $this->success(new TaskResource($task) ,'Assign task Successfully',200);
    }


    
    public function reAssignTask( ReAssignTaskRequest $request,string $id)
    {
        $validated = $request->only('firstName','lastName');
        $task = $this->taskServices->reAssignTask($validated,$id);
        return $this->success(new TaskResource($task) ,'Assign task Successfully',200);
    }
    //...........................................End Assign Task............................................

    //-------------------------------------------Upload attachments --------------------------------
    /*

    */
    public function uploadAttachment(UploadFileRequest $request, Task $task)
    {
        $validated = $request->only(['file']);
        $uploadFile = $this->taskServices->storeFile($validated['file'], $task);
        return $this->success($uploadFile, 'Upload fle successfully',200);
    }

    //.....................................Add Comments ..........................................

    public function addComment(AddCommentRequest $request, Task $task)
    {
        $validated = $request->only(['comment']);  
      $task = $this->taskServices->addComment($validated,$task);
      return $this->success(new TaskResource($task),'Add comment Successfully',200); 
    }

    //...........................................Task Repports.............................

    public function taskReports()
    {
         SaveDailyRepports::dispatch();  
         return $this->success(null,'Task reports generated successfully',200);     
    }

    

}

