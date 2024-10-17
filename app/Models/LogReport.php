<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogReport extends Model
{
    use HasFactory;

    protected $fillable = ['details', 'date', 'type'];
}
