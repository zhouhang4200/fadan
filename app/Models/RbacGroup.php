<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\RevisionableTrait;

class RbacGroup extends Model
{
    use RevisionableTrait;

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $keepRevisionOf = array(
        'name', 'alias',
    );

    protected $revisionCreationsEnabled = true;

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
