<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class City extends Model
{
    use RevisionableTrait;

    public $timestamps = true;

    protected $fillable = ['name'];

    protected $keepRevisionOf = array(
        'name',
    );

    protected $revisionCreationsEnabled = true;

    public function loginHistories()
    {
    	return $this->hasMany(LoginHistory::class, 'city_id');
    }

    public function adminLoginHistories()
    {
    	return $this->hasMany(AdminLoginHistory::class, 'city_id');
    }
}
