<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use App\Extensions\Revisionable\RevisionableTrait;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use RevisionableTrait;

    /**
     * 开启监听
     * @var bool
     */
    protected $revisionCreationsEnabled = true;

    /**
     * 自动清除记录
     * @var bool
     */
    protected $revisionCleanup = true;

    /**
     * 保存多少条记录
     * @var int
     */
    protected $historyLimit = 50000;
    
    public function __construct(array $attributes = [])
    {
    	$attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->setTable(config('permission.table_names.roles'));
    }


    public function setNameAttribute($value)
    {
        return $this->attributes['name'] = trim($value);
    }

    public static function rules()
    {
    	return [
            'name' => 'required|string|max:190|unique:roles',
    		'alias' => 'required|string|max:190',
    	];
    }

    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('roles')->ignore($id), 'string', 'max:190'],
            'alias' => 'required|string|max:190',
        ];
    }

    public static function messages()
    {
    	return [
            'name.required' => '名称必须填写!',
    		'alias.required' => '中文名别名必须填写!',
    		'name.unique' => '中文名别名已经存在!',
    	];
    }
}
