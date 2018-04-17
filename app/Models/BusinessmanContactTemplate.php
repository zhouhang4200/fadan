<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessmanContactTemplate extends Model
{
    public $fillable = [
      'user_id',
      'name',
      'content',
      'type',
    ];
}