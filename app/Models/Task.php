<?php

namespace App\Models;

use function PHPUnit\Framework\isEmpty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable =['title','description','type','status','priority',
                            'due_date','assigned_to'];

    protected $casts = [
        'due_date' => 'datetime',
    ];
    
    //.....................Relation............................................
  
    // Inverse of assignedTasks() in the User model
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    public function taskStatus()
    {
        return $this->hasMany(TaskStatus::class,'task_id','id');
    }
    
    public function comments()
    {
        return $this->morphMany(Comment::class,'commentable');
    }

   
    public function attachments()
    {
        return $this->morphMany(Attachment::class,'attachable');
    }
    //................................................
    /*
    this relation is with dependency_tasks table, 
    this model is like two models had many to many relation and the
    dependency_tasks table is the pivot table with 
     foreign keys for task_id and dependent_on_task_id
    so we have two belongs to Many relations:
    one for task_id and dependent_on_task_id:
        task_id: this task has many tasks he depends on
        dependent_on_task_id :many tasks depend on this task

        use dependencyon to get what this task depends on.
        use dependencyonMe to get what tasks depend on this task.

    */
    public function dependencyOn()
    {
        return $this->belongsToMany(Task::class,'dependency_tasks',
                                    'task_id','dependent_on_task_id');
    }


    public function dependencyOnMe()
    {
        return $this->belongsToMany(Task::class,'dependency_tasks',
                                    'dependent_on_task_id','task_id');
    }
    
    
/*
    laravel excpect table name like this: task_statuses, but in my case it's taskStatuses.
    so i defined the foreign key and the  primary key
*/
    public function taskStatuses()
    {
        return $this->hasMany(TaskStatus::class,'task_id','id');
    }
//............................................End Of Relationship................................................................

    //.............................Mutators..................................

    public function getDueDateFormattedAttribute()
    {
        return $this->due_date ? $this->due_date->format('Y-m-d') : null;
    }

    //............................Scope (status,priority,type,due_date and assigned_to)................................



    public function scopeTasksFilterbyAll($query,$type=null,$status =null,$dueDate =null,$priority =null,$assigned_to =null,$dependce_on =null)
    {
        if (filled($type)) 
        {
            $query->where('type', $type);
        }
    
        if (filled($status))
        {
            $query->where('status', $status);
        }

        if (filled($dueDate)) {

            $query->whereDate('due_date', $dueDate);
        }
    
        if (filled($priority)) 
        {
            $query->where('priority', $priority);
        }
    
        if (filled($assigned_to)) 
        {
            $query->where('assigned_to', $assigned_to);
        }
        if (filled($dependce_on)) 
        {
            $query->whereRelation('dependencyOn','dependent_on_task_id',$dependce_on);
        }
    
        return $query;
    }

    //...........................................Delete task .......................................

    protected static function boot()
{
    parent::boot();

    static::deleting(function ($task) {
        if ($task->isForceDeleting()) {
            // Force delete related models
            if ($task->taskStatus()) {
                $task->taskStatus()->forceDelete();
            }
            if ($task->comments()) {
                $task->comments()->forceDelete();
            }
            if ($task->attachments()) {
                // Delete attachments from storage before force deleting them from the database
                foreach ($task->attachments as $attachment) {
                    // Assuming 'file_path' is the field in your attachments table that stores the file path
                    dd($attachment->file_path);
                    Storage::delete( 'Uploads/'.$attachment->file_name);
                    
                }
                $task->attachments()->forceDelete();
            }
    
            // Force delete pivot table records (DependencyTask)
            DependencyTask::where('task_id', $task->id)
                          ->orWhere('dependent_on_task_id', $task->id)
                          ->forceDelete();
        } else {
            // Soft delete related models
            if ($task->taskStatus()) {
                $task->taskStatus()->delete();
            }
            if ($task->comments()) {
                $task->comments()->delete();
            }
            if ($task->attachments()) {
                $task->attachments()->delete();
            }
    
            // Soft delete the task dependencies
            DependencyTask::where('task_id', $task->id)
                          ->orWhere('dependent_on_task_id', $task->id)
                          ->delete();
        }
    });
    
    
}


}
