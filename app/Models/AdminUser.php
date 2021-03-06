<?php

namespace App\Models;

use Auth;
use Illuminate\Validation\Rule;
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
            'name' => 'required|string|max:191|unique:admin_users',
            'password' => 'required|string|min:6|max:16',
        ];
    }

    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('admin_users')->ignore($id), 'string', 'max:190', ],
        ];
    }

    public static function messages()
    {
        return [
            'name.required' => '请填写账号！',
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

    public function revisions()
    {
        return $this->hasMany(Revision::class, 'user_id', 'id');
    }
}
