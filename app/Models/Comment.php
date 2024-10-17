<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use SoftDeletes , HasFactory;

    protected $table = 'comments';
    
    protected $fillable = ['content'];

    //.................Relation..........................

    public function commentable()
    {
        return $this->morphTo();
    }
}
