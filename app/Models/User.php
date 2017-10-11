<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
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

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public static function messages()
    {
        return [
            'name.required' => '请填写账号！',
            'password.required' => '请填写密码',
        ];
    }

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

    public function RealNameIdent()
    {
        return $this->belongsTo(RealNameIdent::class, 'user_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * 子账号查找
     * @param  [type] $query   [description]
     * @param  array  $filters [description]
     * @return Illuminate\Database\Eloquent\query
     */
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['name']) {

            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('create_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('create_at', '<=', $filters['endDate']);
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('create_at', [$filters['startDate'], $filters['endDate']]);
        }

        return $query;
    }
}
