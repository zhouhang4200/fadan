<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class RbacGroup extends Model
{
    use RevisionableTrait;

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $keepRevisionOf = array(
        'name',
    );

    protected $revisionCreationsEnabled = true;

    public function setNameAttribute($value)
    {
        return $this->attributes['name'] = trim($value);
    }

    public static function rules ()
    {
    	return [
            'name' => 'required|string|max:191',
    	];
    }

    public static function messages()
    {
    	return [
            'name.required' => '请填写组名!',
    	];
    }

    public function permissions()
    {
    	return $this->belongsToMany(Permission::class, 'rbac_group_permissions', 'rbac_group_id', 'permission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
