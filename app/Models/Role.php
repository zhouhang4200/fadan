<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use App\Extensions\Revisionable\RevisionableTrait;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function __construct(array $attributes = [])
    {
    	$attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->setTable(config('permission.table_names.roles'));
    }

    protected $keepRevisionOf = array(
        'name', 'alias'
    );

    protected $revisionCreationsEnabled = true;

    public function setNameAttribute($value)
    {
        return $this->attributes['name'] = trim($value);
    }

    public static function rules()
    {
    	return [
    		'alias' => 'required|unique:roles',
    	];
    }

    public static function updateRules($id)
    {
        return [
            'alias' => ['required', Rule::unique('roles')->ignore($id),],
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
