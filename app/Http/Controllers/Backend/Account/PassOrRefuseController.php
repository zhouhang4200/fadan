<?php

namespace App\Http\Controllers\Backend\Account;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;

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

        if (RealNameIdent::where('user_id', $userId)->update(['status' => 1])) {

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

        if (RealNameIdent::where('user_id', $userId)->update(['status' => 2, 'message' => '请按要求填写资料!'])) {
            
            return response()->json(['code' => 1, 'message' => '审核通过!']);
        }
        return response()->json(['code' => 2, 'message' => '操作异常错误，请重试!']);  
    }
}