<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->setTable(config('permission.table_names.permissions'));
    }

    public function rbacGroups()
    {
    	return $this->belongsToMany(RbacGroup::class, 'rbac_group_permissions', 'permission_id', 'rbac_group_id');
    }

    public static function rules()
    {
    	return [
    		'name' => 'required|unique:permissions',
    		'alias' => 'required|unique:permissions',
    	];
    }

    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('permissions')->ignore($id),],
            'alias' => ['required', Rule::unique('permissions')->ignore($id),],
        ];
    }

    public static function messages()
    {
    	return [
    		'name.required' => '名称必须填写!',
    		'alias.required' => '别名必须填写!',
    		'name.unique' => '名称已经存在',
    		'alias.unique' => '别名已经存在',
    	];
    }
}
