<?php

namespace App\Http\Controllers\Frontend\V2\Account;

use Exception;
use App\Models\User;
use App\Models\NewRole;
use App\Models\NewModule;
use Illuminate\Support\Str;
use App\Models\LoginHistory;
use App\Models\RealNameIdent;
use Illuminate\Support\Facades\DB;
use App\Models\HatchetManBlacklist;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AccountController extends Controller
{
    protected static $extensions = ['png', 'jpg', 'jpeg', 'gif'];

    /**
     * 我的账号
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mine()
    {
        return view('frontend.v2.account.mine');
    }

    /**
     * 我的账号接口
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function mineForm()
    {
        return Auth::user();
    }

    /**
     * 我的账号修改
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mineUpdate()
    {
        try {
            $user = Auth::user();

            if (request('password')) {
                $user->password = bcrypt(request('password'));
            }
            $user->type = request('type');
            $user->leveling_type = request('type');
            $user->save();
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 登录记录
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loginHistory()
    {
        return view('frontend.v2.account.login-history');
    }

    /**
     * 登录记录接口
     * @return mixed
     */
    public function loginHistoryDataList()
    {
        $startDate = request('date')[0];
        $endDate = request('date')[1];

        $filter = compact( 'startDate', 'endDate');

        return LoginHistory::where('user_id', Auth::user()->id)
            ->newfilter($filter)
            ->with(['user', 'city'])
            ->latest('id')
            ->paginate(15);
    }

    /**
     * 员工管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employee()
    {
        return view('frontend.v2.account.employee');
    }

    /**
     * 子员工
     * @return mixed
     */
    public function employeeUser()
    {
        return User::where('parent_id', Auth::user()->getPrimaryUserId())->pluck('username', 'id');
    }

    /**
     * 岗位
     * @return mixed
     */
    public function employeeStation()
    {
        return NewRole::where('user_id', Auth::user()->getPrimaryUserId())->get();
    }

    /**
     * 员工管理接口
     * @return mixed
     */
    public function employeeDataList()
    {
        $userName = request('username');
        $name = request('name');
        $station = request('station');

        $filter = compact( 'userName', 'name', 'station');

        return User::staffManagementFilter($filter)
            ->with('newRoles')
            ->paginate(15);
    }

    /**
     * 员工管理开关
     * @return mixed
     */
    public function employeeSwitch()
    {
        try {
            $user = User::find(request('user_id'));
            $user->status = request('status');
            $user->save();
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 员工岗位删除
     * @return mixed
     */
    public function employeeDelete()
    {
        try {
            $user = User::find(request('user_id'));
            // 删除该员工下面的角色和权限
            $user->newRoles()->detach();
            $user->newPermissions()->detach();
            // 删除该角色并清空缓存
            $user->delete();
            // 清除缓存
            Cache::forget('newPermissions:user:'.$user->id);
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '删除成功');
    }

    /**
     * 新增岗位
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employeeCreate()
    {
        return view('frontend.v2.account.employee-create');
    }

    /**
     * 岗位新增接口
     * @return mixed
     */
    public function employeeAdd()
    {
        try{
            // 判断账号是否唯一
            $isSingle = User::where('name', request('name'))->withTrashed()->first();

            if ($isSingle) {
                return response()->ajax(0, '账号名已存在!');
            }
            myLog('test', [request('station')]);
            // 数据
            $data['api_token'] = Str::random(25);
            $data['username'] = request('username');
            $data['name'] = request('name');
            $data['phone'] = request('phone');
            $data['qq'] = request('qq');
            $data['wechat'] = request('wechat');
            $data['remark'] = request('remark');
            $data['leveling_type'] = request('leveling_type');
            $data['password'] = bcrypt(request('password'));
            $data['parent_id'] = Auth::user()->getPrimaryUserId();
            $data['email'] = mt_rand()."@qq.com";
            $data['app_id'] = str_random(60);
            $data['app_secret'] = str_random(60);
            $data['voucher'] = "/frontend/v1/images/default-avatar.png";
            $roleIds = request('station', []);
            // 添加子账号同时添加角色
            $user = User::create($data);
            $user->newRoles()->sync($roleIds);
            // 清除缓存
            Cache::forget('newPermissions:user:'.$user->id);
        } catch (Exception $e) {
            myLog('test', [$e->getMessage()]);
            return response()->ajax(0, '请重新提交数据!');
        }
        return response()->ajax(1, '添加成功');
    }

    /**
     * 员工岗位修改
     * @return mixed
     */
    public function employeeUpdate()
    {
        DB::beginTransaction();

        try {
            // 子账号
            $user = User::find(request('id'));
            // 如果存在密码则修改密码
            if (request('password')) {
                $user->password = bcrypt(request('password'));
            }
            // 关联到管理员-角色表
            $roleIds = request('station', []);
            $user->newRoles()->sync($roleIds);
            // 更新账号
            $user->username = request('username');
            $user->phone = request('phone');
            $user->qq = request('qq');
            $user->wechat = request('wechat');
            $user->leveling_type = request('leveling_type');
            $user->remark = request('remark');
            $user->save();
            // 清除缓存
            Cache::forget('newPermissions:user:'.$user->id);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->ajax(0, '修改失败！');
        }
        DB::commit();
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 黑名单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blackList()
    {
        return view('frontend.v2.account.black-list');
    }

    /**
     * 打手昵称
     * @return mixed
     */
    public function blackListName()
    {
        return HatchetManBlacklist::where('user_id', Auth::user()->getPrimaryUserId())->pluck('hatchet_man_name', 'id');
    }

    /**
     * 打手黑名单新增
     * @return mixed
     */
    public function blackListAdd()
    {
        try {
            if (is_null(request('hatchet_man_name')) || is_null(request('hatchet_man_qq')) || is_null(request('hatchet_man_phone'))) {
                return response()->ajax(0, '带*为必填内容');
            }
            $data['user_id'] = Auth::user()->getPrimaryUserId();
            $data['hatchet_man_name'] = request('hatchet_man_name');
            $data['hatchet_man_phone'] = request('hatchet_man_phone');
            $data['hatchet_man_qq'] = request('hatchet_man_qq');
            $data['content'] = request('content', '无');

            HatchetManBlacklist::create($data);
        } catch (Exception $e) {
            return response()->ajax(0, '添加失败');
        }
        return response()->ajax(1, '添加成功');
    }

    /**
     * 打手黑名单修改
     * @return mixed
     */
    public function blackListUpdate()
    {
        try {
            if (is_null(request('hatchet_man_name')) || is_null(request('hatchet_man_qq')) || is_null(request('hatchet_man_phone'))) {
                return response()->ajax(0, '带*为必填内容');
            }

            $blackList = HatchetManBlacklist::find(request('id'));

            $blackList->hatchet_man_name = request('hatchet_man_name');
            $blackList->hatchet_man_phone = request('hatchet_man_phone');
            $blackList->hatchet_man_qq = request('hatchet_man_qq');
            $blackList->content = request('content', '无');

            $blackList->save();
        } catch (Exception $e) {
            return response()->ajax(0, '修改失败!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 打手黑名单接口
     * @return mixed
     */
    public function blackListDataList()
    {
        $hatchetManName = request('hatchet_man_name');
        $hatchetManPhone = request('hatchet_man_phone');
        $hatchetManQq = request('hatchet_man_qq');

        // 筛选
        $filters = compact('hatchetManName', 'hatchetManPhone', 'hatchetManQq');

        return HatchetManBlacklist::where('user_id', Auth::user()->getPrimaryUserId())
            ->filter($filters)
            ->paginate(15);
    }

    /**
     * 打手黑名单删除
     * @return mixed
     */
    public function blackListDelete()
    {
        if (! request('id')) {
            return response()->ajax(0, '该条记录未找到');
        }
        $del = HatchetManBlacklist::destroy(request('id'));

        if (! $del) {
            return response()->ajax(0, '删除失败');
        }

        return response()->ajax(1, '删除成功');
    }

    /**
     * 实名认证
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function authentication()
    {
        return view('frontend.v2.account.authentication');
    }

    /**
     * 图片上传
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticationUpload()
    {
        try {
            $file = request('file');
            $path =  public_path("/resources/ident/".date('Ymd')."/");

            $extension = $file->getClientOriginalExtension();

            if (!request('name')) {
                return response()->ajax(0, '参数缺失！');
            }

            if ($extension && ! in_array(strtolower($extension), static::$extensions)) {
                return response()->ajax(0, '图片格式不正确!');
            }

            if (! $file->isValid()) {
                return response()->ajax(0, '无效的图片！');
            }

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $randNum = rand(1, 100000000) . rand(1, 100000000);

            $fileName = time().substr($randNum, 0, 6).'.'.$extension;

            $path = $file->move($path, $fileName);

            $path = strstr($path, '/resources');

            $finalPath =  str_replace('\\', '/', $path);

            return response()->json(['status' => 1, 'name' => request('name'), 'path' => $finalPath]);
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常！');
        }
    }

    /**
     * 实名认证新增
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticationAdd()
    {
        try {
            if (RealNameIdent::where('user_id', Auth::id())->first()) {
                return response()->ajax(0, '您已提交申请，请勿重复提交！');
            }

            $userId = Auth::user()->getPrimaryUserId();

            if (request('front_card_picture')) {
                $data['type']                      = 1;
                $data['name']                      = request('name');
                $data['bank_name']                 = request('bank_name');
                $data['bank_number']               = request('bank_number');
                $data['user_id']                   = $userId;
                $data['identity_card']             = request('identity_card');
                $data['phone_number']              = request('phone_number');
                $data['front_card_picture']        = request('front_card_picture');
                $data['back_card_picture']         = request('back_card_picture');
                $data['hold_card_picture']         = request('hold_card_picture');
                RealNameIdent::create($data);
            } elseif (request('license_picture')) {
                $data['type']                      = 2;
                $data['name']                      = request('name');
                $data['bank_name']                 = request('bank_name');
                $data['bank_number']               = request('bank_number');
                $data['user_id']                   = $userId;
                $data['phone_number']              = request('phone_number');
                $data['corporation']               = request('corporation');
                $data['license_number']            = request('license_number');
                $data['license_picture']           = request('license_picture');
                $data['bank_open_account_picture'] = request('bank_open_account_picture');
                $data['agency_agreement_picture']  = request('agency_agreement_picture');
                RealNameIdent::create($data);
            } else {
                return response()->ajax(0, '页面数据异常，请重新填写！');
            }
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常！');
        }
        return response()->ajax(1, '实名认证申请成功，请等待后台工作人员处理！');
    }

    /**
     * 实名认证修改
     * @return mixed
     */
    public function authenticationUpdate()
    {
        try {
            $userId = Auth::user()->getPrimaryUserId();

            $authentication = RealNameIdent::where('user_id', $userId)->first();

            if (request('front_card_picture')) {
                $authentication->name                      = request('name');
                $authentication->bank_name                 = request('bank_name');
                $authentication->bank_number               = request('bank_number');
                $authentication->identity_card             = request('identity_card');
                $authentication->phone_number              = request('phone_number');
                $authentication->front_card_picture        = request('front_card_picture');
                $authentication->back_card_picture         = request('back_card_picture');
                $authentication->hold_card_picture         = request('hold_card_picture');
                $authentication->save();
            } elseif (request('license_picture')) {
                $authentication->name                      = request('name');
                $authentication->bank_name                 = request('bank_name');
                $authentication->bank_number               = request('bank_number');
                $authentication->phone_number              = request('phone_number');
                $authentication->corporation               = request('corporation');
                $authentication->license_number            = request('license_number');
                $authentication->license_picture           = request('license_picture');
                $authentication->bank_open_account_picture = request('bank_open_account_picture');
                $authentication->agency_agreement_picture  = request('agency_agreement_picture');
                $authentication->save();
            } else {
                return response()->ajax(0, '页面数据异常，请重新填写！');
            }
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常！');
        }
        return response()->ajax(1, '修改成功，请等待后台工作人员处理！');
    }

    /**
     * 页面数据
     * @return mixed
     */
    public function authenticationForm()
    {
        return RealNameIdent::where('user_id', Auth::user()->getPrimaryUserId())->first();
    }

    /**
     * 岗位管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function station()
    {
        return view('frontend.v2.account.station');
    }

    /**
     * 岗位列表接口
     * @return mixed
     */
    public function stationDataList()
    {
        return NewRole::where('user_id', Auth::user()->getPrimaryUserId())
            ->with(['newUsers', 'newPermissions'])
            ->paginate(15);
    }

    /**
     * 用户所有的权限
     * @return mixed
     */
    public function stationPermission()
    {
        $user = User::find(Auth::user()->getPrimaryUserId());

        // 获取此账号下面所有的模块
        $permissionIds = $user->getUserPermissions()->pluck('id');

        return NewModule::whereHas('newPermissions', function ($query) use ($permissionIds) {
            $query->whereIn('id', $permissionIds);
        })->with('newPermissions')->select('id', 'name as alias')->get();
    }

    /**
     * 岗位表单接口
     * @return mixed
     */
    public function stationForm()
    {
        try {
            myLog('test', [request()->all()]);
            $data['name'] = request('name');
            $data['alias'] = request('name');
            $data['user_id'] = Auth::user()->getPrimaryUserId();

            $newRole = NewRole::create($data);
            // 角色-权限关联
            $newRole->newPermissions()->sync(request('permission'));
            // 清除缓存
            if ($newRole->newUsers) {
                foreach ($newRole->newUsers as $childUser) {
                    Cache::forget('newPermissions:user:'.$childUser->id);
                }
            }

        } catch (Exception $e) {

        }
        return response()->ajax(1, '添加成功!');
    }

    /**
     * 岗位添加
     * @return mixed
     */
    public function stationAdd()
    {
        try {
            $data['name'] = request('name');
            $data['alias'] = request('name');
            $data['user_id'] = Auth::user()->getPrimaryUserId();

            $newRole = NewRole::create($data);
            $permission = explode(',', request('permission'));
            // 角色-权限关联
            $newRole->newPermissions()->sync($permission);
            // 清除缓存
            if ($newRole->newUsers) {
                foreach ($newRole->newUsers as $childUser) {
                    Cache::forget('newPermissions:user:'.$childUser->id);
                }
            }

        } catch (Exception $e) {

        }
        return response()->ajax(1, '添加成功!');
    }

    /**
     * 岗位修改
     * @return mixed
     */
    public function stationUpdate()
    {
        try {
            if (request('permission')) {
                $permission = explode(',', request('permission'));
                // 主账号
                $user = User::find(Auth::user()->getPrimaryUserId());
                // 清除缓存
                Cache::forget('newPermissions:user:'.$user->id);
                // 获取当前角色
                $userRole = NewRole::find(request('id'));
                // 数据
                $userRole->user_id = $user->id;
                $userRole->alias = request('name');
                $userRole->name = request('name');
                // 修改岗位
                $userRole->save();
                // 关联岗位-权限
                $userRole->newPermissions()->sync($permission);
                // 清除缓存
                if ($userRole->newUsers) {
                    foreach ($userRole->newUsers as $user) {
                        Cache::forget('newPermissions:user:'.$user->id);
                    }
                }
                // 清除缓存
                if ($userRole->newUsers) {
                    foreach ($userRole->newUsers as $childUser) {
                        Cache::forget('newPermissions:user:'.$childUser->id);
                    }
                }
            } else {
                return response()->ajax(1, '请勾选权限！');
            }
        } catch (Exception $e) {
            return response()->ajax(1, '服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 岗位删除
     * @return mixed
     */
    public function stationDelete()
    {
        try {
            // 获取当前岗位
            $userRole = NewRole::find(request('id'));
            // 岗位删除成功之后，再删除子账号下面的权限
            $userRole->delete();
            // 删除此岗位下面所有的权限值
            $userRole->newPermissions()->detach();
            // 删除此角色下的用户
            $userRole->newUsers()->detach();
            // 清除缓存
            if ($userRole->newUsers) {
                foreach ($userRole->newUsers as $childUser) {
                    Cache::forget('newPermissions:user:'.$childUser->id);
                }
            }
        } catch (Exception $e) {
            return response()->ajax(1, '服务器错误!');
        }
        return response()->ajax(1, '删除成功!');
    }
}
