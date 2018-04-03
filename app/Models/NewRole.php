<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewRole extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'alias', 'name'];

    public function newPermissions() {
    	return $this->belongsToMany(NewPermission::class);
    }

    public function newUsers() {
    	return $this->belongsToMany(User::class);
    }
}
