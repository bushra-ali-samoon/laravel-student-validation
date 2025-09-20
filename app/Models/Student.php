<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'email', 
        'is_verified', 
        'verification_token'
    ];
}



 
  