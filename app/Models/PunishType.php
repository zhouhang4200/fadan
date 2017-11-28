<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class PunishType extends Model
{
    protected $fillable = ['name'];

    public static function rules()
    {
    	return [
    		'name' => 'required|string|max:60|unique:punish_types',
    	];
    }

    public static function messages()
    {
    	return [
    		'name.required' => '名字必须填写',
    	];
    }

    public static function updateRules($id)
    {
    	return [
            'name' => ['required', Rule::unique('punish_types')->ignore($id), 'string', 'max:60',],
        ];
    }
}
