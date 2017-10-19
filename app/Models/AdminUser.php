<?php

namespace App\Models;

use Auth;
use App\Models\AdminLoginHistory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Venturecraft\Revisionable\RevisionableTrait;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use Notifiable, HasRoles, RevisionableTrait;

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

    protected $keepRevisionOf = array(
        'updated_at'
    );

    protected $revisionCreationsEnabled = true;

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:admin_users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('admin_users')->ignore($id),],
        ];
    }

    public static function messages()
    {
        return [
            'name.required' => '请填写账号！',
            'password.required' => '请填写密码',
        ];
    }

    public function adminLoginHistories()
    {
        return $this->hasMany(AdminLoginHistory::class, 'user_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }
}
