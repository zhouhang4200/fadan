<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use App\Extensions\Revisionable\RevisionableTrait;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use RevisionableTrait;
    
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->setTable(config('permission.table_names.permissions'));
    }

    protected $keepRevisionOf = array(
        'name', 'alias',
    );

    protected $revisionCreationsEnabled = true;

    public function rbacGroups()
    {
    	return $this->belongsToMany(RbacGroup::class, 'rbac_group_permissions', 'permission_id', 'rbac_group_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function setNameAttribute($value)
    {
        return $this->attributes['name'] = trim($value);
    }

    public static function rules()
    {
    	return [
            'name' => 'required|string|max:150|unique:permissions',
    		'alias' => 'required|string|max:150',
    	];
    }

    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('permissions')->ignore($id),'string', 'max:150'],
            'alias' => 'required|string|max:150',
        ];
    }

    public static function messages()
    {
    	return [
    		'alias.required' => '中文名必须填写!',
            'name.required' => '名称已经存在',
            'name.unique' => '名称已经存在',
    	];
    }
}
