<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    public $timestamps = false;
    public $fillable = ['game_id', 'type'];
}
