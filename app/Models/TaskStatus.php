<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskStatus extends Model
{
    use SoftDeletes , HasFactory;

    protected $table = 'taskStatuses';

    protected $fillable = ['old_status', 'new_status','task_id'];


    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
