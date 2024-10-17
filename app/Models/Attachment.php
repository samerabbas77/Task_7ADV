<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use   SoftDeletes , HasFactory;

    protected $table = 'attachments';
    
    protected $fillable = ['file_name','file_path','file_type'];

    //.................Relation..........................

    public function attachable()
    {
        return $this->morphTo();
    }
}
