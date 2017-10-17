<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RbacGroup extends Model
{
    public $timestamps = true;

    protected $guarded = ['id'];

    public static function rules ()
    {
    	return [
            'name' => 'required',
    		'alias' => 'required',
    	];
    }

    public static function messages()
    {
    	return [
            'name.required' => '请填写组名!',
    		'alias.required' => '请填写别名!',
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
