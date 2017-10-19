<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class Module extends Model
{
    use RevisionableTrait;
    
    public $timestamps = true;

    protected $guarded = ['id'];

    protected $keepRevisionOf = array(
        'name', 'alias'
    );

    protected $revisionCreationsEnabled = true;

    public static function rules()
    {
    	return [
    		'name' => 'required|unique:modules',
    		'alias' => 'required|unique:modules',
    	];
    }

    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('modules')->ignore($id),],
            'alias' => ['required', Rule::unique('modules')->ignore($id),],
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

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
