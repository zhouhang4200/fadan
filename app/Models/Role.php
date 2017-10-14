<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function __construct(array $attributes = [])
    {
    	$attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->setTable(config('permission.table_names.roles'));
    }

    public static function rules()
    {
    	return [
    		'name' => 'required|unique:roles',
    		'alias' => 'required|unique:roles',
    	];
    }

    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('roles')->ignore($id),],
            'alias' => ['required', Rule::unique('roles')->ignore($id),],
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
