<?php

namespace App\Http\Controllers\Backend\Account;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;

/**
 * 审核通过与否类
 */
class PassOrRefuseController extends Controller
{
     /**
     * ajax 审核通过
     * @param  Request $request [description]
     * @return string
     */
    public function pass(Request $request)
    {
        $userId = $request->userId;

        $user = User::find($userId);

        if (RealNameIdent::where('user_id', $userId)->update(['status' => 1])) {

            $roleId = Role::where('name', 'home.qiantaitixianzu')->value('id');

            if (! in_array($roleId, $user->roles->pluck('id')->toArray())) {

                $user->roles()->attach($roleId);
            }

            return response()->json(['code' => 1, 'message' => '审核通过!']);
        }
        return response()->json(['code' => 2, 'message' => '操作异常错误，请重试!']);  
    }

    /**
     * ajax 审核不通过
     * @param  Request $request [description]
     * @return string
     */
    public function refuse(Request $request)
    {
        $userId = $request->userId;

        $message = $request->message;

        if (RealNameIdent::where('user_id', $userId)->update(['status' => 2, 'message' => $message])) {
            
            return response()->json(['code' => 1, 'message' => '审核不通过!']);
        }
        return response()->json(['code' => 2, 'message' => '操作异常错误，请重试!']);  
    }
}
