<?php

namespace App\Services\Api;

use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Attachment;
use App\Models\TaskStatus;
use Illuminate\Support\Str;
use App\Models\DependencyTask;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class TaskServices
{
    use ApiResponseTrait;
    /**
     *  getAllTasks
     * @param mixed $perPage
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return void
     */
    public function getAllTasks($perPage)
    { 
        try {
            return Cache::remember('getAllTasks',3600,  function () use($perPage)
             {
                $tasks = Task::paginate($perPage);
                return $tasks;
            });
            
            
        } catch (Exception $e) {
            Log::error("Error while fetch the tasks".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
        
    }
    //...................................................................
    //...................................................................
    /**
     * get All Tasks with Filters on 
     * this parameter:
     * @param mixed $perPage
     * @param mixed $type
     * @param mixed $status
     * @param mixed $dueDate
     * @param mixed $priority
     * @param mixed $assigned_to
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed
     */
    public function getAllTasksFiltersBy($perPage,$type =null,$status = null,$dueDate = null,$priority = null,$assigned_to = null,$dependce_on=null)
    {
        try {
            $cacheKey = "getAllTasksFilterBy_{$type}_{$status}_{$dueDate}_{$priority}_{$assigned_to}_{$dependce_on}_{$perPage}";

            return Cache::remember($cacheKey,3600,function() 
            use($type,$status,$dueDate,$priority,$assigned_to,$dependce_on,$perPage)
            {
                $query = Task::query()
                ->TasksFilterbyAll($type,$status,$dueDate,$priority,$assigned_to,$dependce_on);
                
                 return $query->paginate($perPage);
            });

        } catch (Exception $e) {
            Log::error("Error while fetch the tasks".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }     
 
    }
    //...................................................................
    //...................................................................

    public function getAllBluckedTasks()
    {
        try {
            return Cache::remember("getAllBluckedTasks",3600,function() 
            {
                $query = Task::query()
                ->TasksFilterbyAll(null, 'Blocked',null, null, null);
                return $query->paginate();
            });

        } catch (Exception $e) {
            Log::error("Error while fetch the blocked tasks".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        } 
    }
    //...................................................................
    //...................................................................
    /**
     *  create Task
     * the status is open when creating a new task
     * there are no user assigned to this new task
     * @param array $data
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return Task|\Illuminate\Database\Eloquent\Model
     */
    public function createTask(array $data)
    {
        try {
             $task = Task::create([
                'title'         => $data['title'],
                'description'   => $data['description'],
                'type'          => $data['type'],
                'priority'      => $data['priority'],
                'due_date'      => $data['due_date'], 
                'status'        => $data['status']             
             ]);
             Cache::forget("getAllTasks");
             Cache::forget("getallTaksfilterBy");
             Cache::forget("getAllBluckedTasks");
             //set the dependency for this task
             if(!empty($data['task_dependency']))
             {
                foreach ($data['task_dependency'] as $dependencyTaskId) {
                    // Check if the task exists in the database
                    $dependencyTask = Task::findOrFail($dependencyTaskId);
                
                    // Get the tasks that $dependencyTask depends on (eager loading or proper query)
                    $dependentOnTasks = $dependencyTask->dependencyOn()->pluck('dependent_on_task_id')->toArray();
                
                    // Check if any of these tasks (dependentOnTasks) include the new task being created
                    if (in_array($task->id, $dependentOnTasks)) { 
                        Log::error("A circular dependency error occurred:This Task is dependent on another task you sent.");
                        throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
                    }
                
                    // If no conflict, create the new dependency
                    DependencyTask::create([
                        'task_id' => $task->id, // ID of the new task
                        'dependent_on_task_id' => $dependencyTaskId
                    ]);
                }
                
             }

             $this->checkTaskDependency($task);
             return $task;
        } catch (Exception $e) {
            Log::error("Error while Storing the tasks".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
        
    }

    //....................................................................
    //....................................................................
    /**
     * update Task information, but not the status or the assigned_by
     * cause this attributes are changed by spicific functions
     * @param array $data
     * @param \App\Models\Task $task
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return Task
     */
    public function updateTask(array $data,Task $task )
    {
        try {
             $task->title = $data['title']?? $task->title;
             $task->description = $data['description']?? $task->description;
             $task->type = $data['type']?? $task->type;
             $task->priority = $data['priority']?? $task->priority;
             $task->due_date = $data['due_date']?? $task->due_date;

             $task->save();

             Cache::forget("getAllTasks");
             Cache::forget("getallTaksfilterBy");
             Cache::forget("getAllBluckedTasks");

            //set the dependency for this task
            if(!empty($data['task_dependency']))
            {

                foreach($data['task_dependency'] as $dependencytaskId)
                {
                    //check if the tasks exist in the database
                    $dependencyTask = Task::findOrFail($dependencytaskId);

                    //check if the 'task_dependency' from the request not dependce on the current task

                    //get the tasks that $dependcyTask(from request) dependce on
                    $dependentOnTasks = $dependencyTask->dependencyOn()->pluck('dependent_on_task_id')->toArray();

                    //if any task of this tasks ($dependce_onTasks) is currnt updated task throw exception
                    if(in_array($task->id, $dependentOnTasks))
                    {
                        Log::error("A circular dependency error occurred:This Task is dependent on another task you sent.");
                        throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));   
                    }

                    DependencyTask::create([
                        'task_id'  => $task->id,
                        'dependent_on_task_id' => $dependencytaskId
                    ]);
                }
                $this->checkTaskDependency($task);
            }
             return $task;
        } 
        catch (Exception $e) {
            Log::error("Error while Updating the tasks".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
        catch (ModelNotFoundException $e) {
        Log::error("Task Not Found".$e->getMessage());
        throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }

    }
    //...................................................................
    //...................................................................
    /**
     * soft delete Task
     * @param \App\Models\Task $task
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return void
     */
    public function deleteTask($task)
    {
        try {
                $task->delete();
        } catch (Exception $e) {
            Log::error("Error while Soft Deleting the task ".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server'.$e, 500));
        }
    }

    //........................................................
    //........................................................
    //.........................................Soft Delete............................................
    /**
     * force Delete Task
     * @param mixed $task
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return void
     */
    public function forceDeleteTask(string $id)
    {
        try {
            // Find the task by ID
            $task = Task::onlyTrashed()->findOrFail($id);
            
            $task->forceDelete();
               
                
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new HttpResponseException($this->error(null, 'Task not found', 404));
        } catch (Exception  $e) {
            Log::error("Error while Force Deleting  the task  ".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }

    //..............................................................
    //..............................................................
    /**
     * get Trashed Tasks
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTrashedTasks()
    {
        try {
            $tasks = Task::onlyTrashed()->paginate(perPage: 10);
           return $tasks;
        } catch (Exception $e) {
            Log::error("Error while fetching deleted tasks".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }

    
    //............................................................
    //............................................................
    /**
     *  restore Deleting Task
     * @param string $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function restoreTask(string $id)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($id);
            $task->restore();
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new HttpResponseException($this->error(null, 'Task not found', 404));
        } catch (Exception $e) {
            Log::error('Error restoring Task: ' . $e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }




    //..........................END OF CRUD FUNCTIONS --------------------------------
    //.................................................................................
    /**
     * check Task Dependency (if there task with status not 'complete' the
     * task status will set to 'blucked')
     * @param \App\Models\Task $task
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return void
     */
    public function checkTaskDependency(Task $task, $status = 'Open')
    {
        try {
                //save old status if exists
                $old_status = $task->status;

                /*check if the  task have  dependencies 
                 make it status = blocked
                 else if it has no dependencies
                 make it status = open
                */
    
                $dependecyonTask = $task->dependencyOn()
                                        ->where('status' ,'!=', 'Completed')
                                        ->exists();
                if($dependecyonTask)
                {
                    $task->update(['status' => 'Blocked']) ;
                }else{
                    $task->update(['status' => $status]) ;
                }
                //save the changes on status in new record
                TaskStatus::create([
                    'old_status' => $old_status ?? 'N/A', //if this is the first status(create not update) so there are no old status
                    'new_status' => $task->status,
                    'task_id'    => $task->id
                ]);
                    
        } catch (Exception $e) {
            Log::error("Error while check for the task dependency ".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server'.$e, 500));
        }
    }
    //...................................................................
    //...................................................................
    public function updateStatus($validated,Task $task)
    {
        try {
            //check if the task have no debbedency (or the tasks he dependece on is complete)
            //1. check if the task status was changed to completed
            if($validated['status'] == 'Completed') 
            {
                //get all the tasks that depend on this task
                //and its status is 'blocked'
                $blockedTasks = $task->dependencyOnMe()
                                    ->where('status','Blocked')
                                    ->get();
                foreach($blockedTasks as $blockedTask)
                {
                    //for each task check if the task($blockedTask) 
                    // have other dependencies(task with status not completed)
                    $haveOtherdependency = $blockedTask->dependencyOn()
                                                    ->where('status','!=','Completed')
                                                    ->exists();
                     //if the task($blockedTask) have no dependencies
                     //change its status to 'open'
                    if(!$haveOtherdependency)
                    {
                        $blockedTask->update(['status' => 'Open']);
                    }
                }
            }elseif($validated['status'] == 'Open' || $validated['status'] == 'In_Progress') 
            {                
               $this->checkTaskDependency($task,$validated['status']);

               if($task->status == 'Blocked') 
               {
                 Log::error("You cant set the status to open or In_progress, the task have dependency");
                 return false;
               }

            }elseif($validated['status'] == 'Blocked')
            {
                $dependconMeTasks = $task->dependencyOnMe()->get();

                foreach($dependconMeTasks as $dependconMeTask)
                {
                    $dependconMeTask->update(['status' => 'Blocked']);
                }
            }
            $task->update(['status' => $validated['status']]);
            return true;

        } catch (Exception $e) {
            Log::error("Error while Updating the task status".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server ', 500));
        }
    }
    //...................................................................
    //...................................................................
    /**
     * assign Task to a user
     * @param mixed $validated
     * @param string $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return Task|Task[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function assignTask($validated,string $id)
    {
       try {
            $task = Task::findOrFail($id);

            if( $task->status == 'Blocked')
            {
                Log::error("This Task is blocked, you can't assign it to a user. ");
                throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
            }
            $user = User::where('firstName',$validated['firstName'])
                         ->where('lastName',$validated['lastName'])
                         ->first();

            if(!$user)
            {
            Log::error("User not found with provided first and last name.");
            throw new HttpResponseException($this->error(null, 'User not found.', 404)); 
            } 

             // check if the user is already assigned to this task
            if($task->assigned_to != null)
            {
                Log::error("This Task is already assign  to a user. ");
                return null;
            }

            $task->assigned_to = $user->id;
            $task->save();

            return $task;

       }catch (ModelNotFoundException $e) {
            Log::error("Task Not Found".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }catch (Exception $e) {
            Log::error("Error while assigned  the task to the user :".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }

    } 
     //.....................................................................
     //.....................................................................
    /**
     * reAssign Task to new user
     * @param mixed $validated
     * @param string $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return Task|Task[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function reAssignTask($validated,string $id)
    {
       try {
            $task = Task::findOrFail($id);

            if( $task->status == 'Blocked')
            {
                Log::error("This Task is blocked, you can't assign it to a user. ");
                throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
            }
            $user = User::where('firstName',$validated['firstName'])
                         ->where('lastName',$validated['lastName'])
                         ->first();
            
            if(!$user)
            {
            Log::error("User not found with provided first and last name.");
            throw new HttpResponseException($this->error(null, 'User not found.', 404)); 
            } 

            $task->assigned_to = $user->id;
            $task->save();

            return $task;

       }catch (Exception $e) {
            Log::error("Error while reassigning the task to the use :".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }catch (ModelNotFoundException $e) {
            Log::error("Task Not Found".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }


    //...............................Uploading file in attachments...............................
    //...........................................................................................

    public function  storeFile($file,$task)
    {
        try {
            $message = '';

            // Scan the file
            $scanResult = $this->scanFile($file);
            
            // Check scan results for malicious content
            if (isset($scanResult['data']['attributes']['stats'])) {
                $maliciousCount = $scanResult['data']['attributes']['stats']['malicious'] ?? 0;
                if ($maliciousCount > 0) {
                    throw new Exception('File contains a virus!', 400);
                }
            } else {
                $message = 'Scan completed successfully, no virus found :)';
            }

            $originalName = $file->getClientOriginalName();

            // Ensure the file name doesn't contain multiple extensions
            if (preg_match('/\.[^.]+\./', $originalName)) {
                throw new Exception(trans('general.notAllowedAction'), 403);
            }

            // Check for path traversal attacks
            if (strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
                throw new Exception(trans('general.pathTraversalDetected'), 403);
            }

            // Validate the MIME type to ensure it's an allowed file type
            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            $mimeType = $file->getClientMimeType();

            if (!in_array($mimeType, $allowedMimeTypes)) {
                throw new FileException(trans('general.invalidFileType'), 403);
            }

            // Generate a safe, random file name
            $fileName = Str::random(32);
            $extension = $file->getClientOriginalExtension(); // Get the original file extension
          

            // Store the file securely
            $path = $file->storeAs('Uploads', "{$fileName}.{$extension}", 'public');
            
            // Get the full URL path of the stored file
            $url = Storage::url($path);
            

            // Store file metadata in the database
            $uploadedFile = $task->attachments()->create([
                'file_name' => $fileName,
                'file_path' => $path,
                'file_type' => $mimeType
            ]);

            return ['attachment' => $uploadedFile, 'message' => $message];


        }catch (Exception $e) {
            Log::error("Error while uploading file to Attachment table :".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server'.$e->getMessage(), 500));
        }catch (ModelNotFoundException $e) {
            Log::error("Task Not Found".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }

    //...............................................Scan files............................................................................
    //...................................................................................................

    /**
     * Scan file from virus
     * @param mixed $filePath
     * @throws \Exception
     * @return mixed
     */
    public function scanFile($filePath)
    {
        $url = 'https://www.virustotal.com/api/v3/files';

        $apiKey = env('VIRUSTOTAL_API_KEY');
        // Upload the file to VirusTotal
        $response = Http::withHeaders([
            'x-apikey' => $apiKey,
        ])->attach('file', fopen($filePath, 'r'), basename($filePath))->post($url);
            
        // Check if the file was uploaded successfully
        if ($response->successful()) {
            
            // Extract the analysis ID from the response
            $analysisId = $response->json()['data']['id'];
            return $this->pollScanResult($analysisId);
        } else {
            Log::error('VirusTotal API error:', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            throw new Exception('Failed to scan file: ' . $response->body(), $response->status());
        }
    }

    //......................
    /**
     * repeat the scan yo make sure we have sured reslut
     * @param mixed $analysisId
     * @throws \Exception
     * @return mixed
     */
    public function pollScanResult($analysisId)
    {
        $url = "https://www.virustotal.com/api/v3/analyses/{$analysisId}";
        $apiKey = env('VIRUSTOTAL_API_KEY');
        $maxAttempts = 10;
        $attempt = 0;

        // Poll every 10 seconds for the result until the scan is complete
        do {
         
            sleep(10); // wait 10 seconds between polling

            $response = Http::withHeaders([
                'x-apikey' => $apiKey,
            ])->get($url);

            $scanResult = $response->json();
         
            // Check if the scan is completed
            if (isset($scanResult['data']['attributes']['status']) && $scanResult['data']['attributes']['status'] === 'completed') {
                return $scanResult;
            }
            

            $attempt++;
        } while ($attempt < $maxAttempts);

        throw new Exception('Scan timeout or failed to complete after polling.');
    }


    //..................................................End Scan.....................................................................
    //..................................................................................................................



    //...............................Add Comments....................................
    //...............................................................................

    public function addComment($validated,$task)
    {
        try {
            
              $data =  $task->comments()->create([
                        'content' => $validated['comment']
                    ]);
              return $data;
        }catch (Exception $e) {
            Log::error("Error while add comment to Attachment table :".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }
    


}