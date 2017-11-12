<?php

namespace App\Models;

use Cache, Auth;
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

    /**
     * @return array
     */
    public static function rules()
    {
        return [
            'name' => 'required|string|max:191|unique:users',
            'email' => 'required|string|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    /**
     * @return array
     */
    public static function sonRules()
    {
        return [
            'name' => 'required|string|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public static function updateRules($id)
    {
        return [
            'name' => ['required', Rule::unique('users')->ignore($id), 'string', 'max:191',],
        ];
    }

    /**
     * 获取用户设置缓存
     * @return \Illuminate\Cache\CacheManager|mixed
     */
    public function getUserSetting()
    {
        $userPrimaryId = $this->getPrimaryUserId();
        return  Cache::rememberForever(config('redis.user.setting') . $this->getPrimaryUserId(), function() use($userPrimaryId) {
            return UserSetting::where('user_id', $userPrimaryId)->pluck('value', 'option')->toArray();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userAsset()
    {
        if ($this->parent_id == 0) {
            return $this->hasOne(UserAsset::class);
        } else {
            return $this->parent->userAsset();
        }
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required' => '请填写账号！',
            'name.unique' => '账号已经存在！',
            'email.unique' => '邮箱已经存在！',
            'password.required' => '请填写密码',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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
            return $this->parent_id;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function realNameIdent()
    {
        return $this->hasOne(RealNameIdent::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
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
     * @param $query
     * @param array $filters
     * @return mixed
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
     * @param $query
     * @param array $filters
     * @return mixed
     */
    public static function scopeUserGroupFilter($query, $filters = [])
    {
        if ($filters['name']) {

            $query->where('id', $filters['name']);
        }

        return $query->whereHas('rbacGroups');
    }

    public function punishes()
    {
        return $this->hasMany(Punish::class);
    }
}
