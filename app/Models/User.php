<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type', 'parent_id', 'group_id'
    ];

    protected $dates = ['deleted_at'];

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
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('users')->ignore($id),],
            'email' => ['required', Rule::unique('users')->ignore($id),],
        ];
    }

    public function userAsset()
    {
        if ($this->parent_id == 0) {
            return $this->hasOne(UserAsset::class);
        } else {
            return $this->parent->userAsset();
        }
    }

    public static function messages()
    {
        return [
            'name.required' => '请填写账号！',
            'name.unique' => '账号已经存在！',
            'email.unique' => '邮箱已经存在！',
            'password.required' => '请填写密码',
        ];
    }

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    // 获取主账号ID
    public function getPrimaryUserId()
    {
        if ($this->parent_id == 0) {
            return $this->id;
        } else {
            return $this->parent()->first()->id;
        }
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class, 'user_id');
    }

    public function realNameIdent()
    {
        return $this->belongsTo(RealNameIdent::class, 'user_id');
    }

    public function rbacGroups()
    {
        return $this->belongsToMany(RbacGroup::class, 'user_rbac_groups', 'user_id', 'rbac_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset()
    {
        return $this->hasOne(UserAsset::class, 'user_id');
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

            $query->where('id', $filters['name']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('created_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
        }

        return $query;
    }

    /**
     * 子账号查找
     * @param  [type] $query   [description]
     * @param  array  $filters [description]
     * @return Illuminate\Database\Eloquent\query
     */
    public static function scopeUserGroupFilter($query, $filters = [])
    {
        if ($filters['name']) {

            $query->where('id', $filters['name']);
        }

        return $query->whereHas('rbacGroups');
    }
}
