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

    public function setNameAttribute($value)
    {
        return $this->attributes['name'] = trim($value);
    }

    public static function rules()
    {
    	return [
    		'alias' => 'required|unique:modules',
    	];
    }

    public static function updateRules($id)
    {
        return [
            'alias' => ['required', Rule::unique('modules')->ignore($id),],
        ];
    }

    public static function messages()
    {
    	return [
    		'alias.required' => '别名必须填写!',
    		'alias.unique' => '别名已经存在',
    	];
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
