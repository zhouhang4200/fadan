<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewModule extends Model
{
	public $timestamps = false;
	
    protected $fillable = ['name'];

    public function newPermissions()
    {
    	return $this->hasMany(NewPermission::class);
    }
}
