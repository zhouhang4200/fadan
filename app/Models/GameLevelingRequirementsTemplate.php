<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingRequirementsTemplate extends Model
{
    protected $fillable = ['user_id', 'game_id', 'name', 'status', 'content', 'created_at', 'updated_at'];

    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    public static function rules()
    {
        return [
            'name' => 'required',
            'content' => 'required',
        ];
    }

    public static function messages()
    {
        return [
            'name.required' => '模板名称没有填写',
            'content.required' => '模板内容没有填写',
        ];
    }

    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }
}
