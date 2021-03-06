<?php

namespace App\Models;

use Cache, Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Extensions\Revisionable\RevisionableTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles, SoftDeletes,  RevisionableTrait;

    /**
     * 开启监听
     * @var bool
     */
    protected $revisionCreationsEnabled = true;

    /**
     * 自动清除记录
     * @var bool
     */
    protected $revisionCleanup = true;

    /**
     * 保存多少条记录
     * @var int
     */
    protected $historyLimit = 50000;

    /**
     * 不监听的字段
     * @var array
     */
    protected $keepRevisionOf = ['id', 'type'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'qq', 'phone', 'password', 'type', 'leveling_type','parent_id', 'group_id',
        'username', 'wechat', 'status', 'age', 'remark', 'wang_wang', 'store_wang_wang',
        'online', 'nickname', 'voucher', 'api_token', 'api_token_expire','app_id', 'app_secret'
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
     * 转账信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transferAccountInfo()
    {
        return $this->hasOne(UserTransferAccountInfo::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function parentInfo()
    {
        if ($this->parent_id == 0) {
            return $this;
        } else {
            return self::find($this->parent_id);
        }
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
     * 获取主账号信息
     */
    public function getPrimaryInfo()
    {
        if ($this->parent_id == 0) {
            return $this;
        } else {
            return self::find($this->parent_id);
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function smsBalance()
    {
        return $this->hasOne(SmsBalance::class, 'user_id');
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
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['nickname'])) {
            $query->where('nickname', 'like', '%' . $filters['nickname'] . '%');
        }

        if (isset($filters['id'])) {
            $query->where('id',  $filters['id']);
        }

        if (isset($filters['startDate']) && empty($filters['endDate'])) {
            $query->where('created_at', '>=', $filters['startDate']);
        }

        if (isset($filters['endDate']) && empty($filters['startDate'])) {
            $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
        }

        if (isset($filters['endDate']) && $filters['startDate']) {
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

    public function revisions()
    {
        return $this->hasMany(Revision::class, 'user_id', 'id');
    }

    /**
     * 代练员工管理查询
     * @param  [type] $query   [description]
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public static function scopeStaffManagementFilter($query, $filters = [])
    {
        if ($filters['userName']) {
            $query->where('id', $filters['userName']);
        }

        if ($filters['name']) {
            $query->where('name', $filters['name']);
        }

        if ($filters['station']) {
            $userIds = NewRole::find($filters['station'])->newUsers->pluck('id');
            $query->whereIn('id', $userIds);
        }
        return $query->where('parent_id', Auth::user()->getPrimaryUserId());
    }

    public function employeeStatistics()
    {
        return $this->hasMany(EmployeeStatistic::class);
    }

    public function orderStatistics()
    {
        return $this->hasMany(OrderStatistic::class);
    }

    public function creatorOrders()
    {
        return $this->hasMany(Order::class, 'creator_user_id', 'id');
    }

    public function gainerOrders()
    {
        return $this->hasMany(Order::class, 'gainer_user_id', 'id');
    }

    public function creatorPrimaryOrders()
    {
        return $this->hasMany(Order::class, 'creator_primary_user_id', 'id');
    }

    public function gainerPrimaryOrders()
    {
        return $this->hasMany(Order::class, 'gainer_primary_user_id', 'id');
    }

    public function newRoles() {
        return $this->belongsToMany(NewRole::class);
    }

    public function newPermissions() {
        return $this->belongsToMany(NewPermission::class);
    }

    /**
     * 后去账号下的所有权限
     * @return [type] [description]
     */
    public function getUserPermissions()
    {
        $key = 'newPermissions:user:'.$this->id;

        return Cache::rememberForever($key, function () {
            return $this->newPermissions
                ->merge($this->load('newRoles', 'newRoles.newPermissions')
                ->newRoles->flatMap(function ($role) {
                    return $role->newPermissions;
                })->sort()->values())
                ->sort()
                ->values();
        });
    }

     /**
     * 当前等路人是否有权限查看当前路由，视图是否显示
     * @return [type] [description]
     */
    public function could($permission)
    {
        $userHasPermissions = $this->getUserPermissions() ? $this->getUserPermissions()->pluck('name')->toArray() : [];
        
        // 如果是数组
        if (is_array($permission)) {
            foreach ($permission as $value) {
                // 如果有权限,判断当前页面权限是否在等路人权限中
                if (in_array($value, $userHasPermissions)) {
                    return $value;
                }
            }
        } else {
            // 如果有权限,判断当前页面权限是否在等路人权限中
            if (in_array($permission, $userHasPermissions)) {
                return $permission;
            }
        }
        return false;
    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
