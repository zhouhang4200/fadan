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
    		'alias' => 'required|max:191|unique:permissions',
    	];
    }

    public static function updateRules($id)
    {
        return [
            'alias' => ['required', Rule::unique('permissions')->ignore($id),],
        ];
    }

    public static function messages()
    {
    	return [
    		'alias.required' => '别名必须填写!',
    		'alias.unique' => '别名已经存在',
    	];
    }
}
