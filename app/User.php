<?php

namespace App;

use App\Models\LoginHistory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function children()
    {
        return $this->hasMany(static::class, 'pid');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'pid');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class, 'user_id');
    }
}
