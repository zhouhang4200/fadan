<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewPermission extends Model
{
    public $timestamps = false;

    protected $fillable = ['new_module_id', 'name', 'alias'];

    public function newModule()
    {
    	return $this->belongsTo(NewModule::class);
    }

    public function newRoles() {
    	return $this->belongsToMany(NewRole::class);
    }

    public function newUsers() {
    	return $this->belongsToMany(User::class);
    }
}
