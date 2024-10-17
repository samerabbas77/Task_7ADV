<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes, HasFactory, Notifiable , HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
    ];

    protected $guarded =['role'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          =>'hashed'
    ];

/**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

   
    //.............................Task Relation...............................................
    /**
     * relation to know wich tasks assigned to this user
     * PK:id   FK:assigned_to
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class,'assigned_to','id');
    }
   
    //................Acceccor...........................
    /**
     * get the full Name
     * @var array
     */
    protected $appends = ['full_name'];
    public function getFullNameAttribute()
    {
        return "{$this->firstName} {$this->lastName}" ;
    }

    //...................Mutators.............................
    /**
     * set the role manully not in mass assignment
     * @param mixed $value
     * @return void
     */
    // public function setRoleAttribute($value)
    // {
    //     $this->attributes['role'] = $value;
    // }

    //...................filter Scope............................

    public function scopeFilterTasks($querry,$status=null,$priority = null)
    {
        $querry->whereRelation('assignedTasks',function($q) use ($status,$priority)
    {
       if(!empty($priority)) $q->where('priority',$priority);
       if(!empty($status))  $q->where('status',$status);


    });

    return $querry;
    }
}
