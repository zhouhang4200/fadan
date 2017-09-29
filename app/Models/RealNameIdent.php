<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class RealNameIdent extends Model
{
    public $timestamps = true;

    protected $guarded = [];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
