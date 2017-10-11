<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = true;

    protected $fillable = ['name'];

    public function loginHistories()
    {
    	return $this->hasMany(LoginHistory::class, 'city_id');
    }

    public function adminLoginHistories()
    {
    	return $this->hasMany(AdminLoginHistory::class, 'city_id');
    }
}
