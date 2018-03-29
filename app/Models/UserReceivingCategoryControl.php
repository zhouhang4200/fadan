<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReceivingCategoryControl extends Model
{
    public $fillable = [
        'user_id',
        'other_user_id',
        'service_id',
        'game_id',
        'remark',
        'type',
    ];

    public function game()
    {
    	return $this->belongsTo(Game::class);
    }
}
