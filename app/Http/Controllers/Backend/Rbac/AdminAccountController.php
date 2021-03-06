<?php

namespace App\Http\Controllers\Backend\Rbac;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 后台账号
 */
class AdminAccountController extends Controller
{
    public function index()
    {
    	$users = AdminUser::latest('id')->paginate(config('backend.page'));

    	return view('backend.rbac.account.admin.index', compact('users'));
    }

    /**
     * 修改密码
     * @param  $id
     * @return response
     */
    public function edit($id)
    {
    	$adminUser = AdminUser::find($id);

        return view('backend.rbac.account.admin.edit', compact('adminUser'));
    }

    /**
     * 修改密码
     * @param  $id
     * @return response
     */
    public function update(Request $request, $id)
    {
    	$adminUser = AdminUser::find($id);

        $this->validate($request, AdminUser::updateRules($adminUser->id), AdminUser::messages());

        $newPassword = $request->password;

        if ($newPassword) {

            $res = $adminUser->update(['password' => bcrypt($newPassword)]);

            if (! $res) {

                return back()->withInput()->with('updateError', '修改密码失败！');
            }
        }
        return redirect(route('admin-accounts.index'))->with('succ', '修改密码成功!');
    }

    public function create(Request $request)
    {
        return view('backend.rbac.account.admin.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, AdminUser::rules(), AdminUser::messages());

        $datas = $request->all();
        $datas['password'] = bcrypt($request->password);

        AdminUser::create($datas);

        return back()->with('succ', '添加成功!');
    }
}
