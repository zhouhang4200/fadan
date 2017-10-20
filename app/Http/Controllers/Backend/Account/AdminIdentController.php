<?php

namespace App\Http\Controllers\Backend\Account;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;

class AdminIdentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->name;

        $startDate = $request->startDate;

        $endDate = $request->endDate;

        $filters = compact('name', 'startDate', 'endDate');

        $idents = RealNameIdent::filter($filters)->where('status', 0)->paginate(config('backend.page'));

        return view('backend.account.ident.index', compact('idents', 'name', 'startDate', 'endDate'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * ajax 审核通过
     * @param  Request $request [description]
     * @return string
     */
    public function pass(Request $request)
    {
        $userId = $request->userId;

        if (RealNameIdent::where('user_id', $userId)->update(['status' => 1])) {

            return jsonMessages(1, '审核通过!');
        }
        return jsonMessages(0, '操作异常错误，请重试!');  
    }

    /**
     * ajax 审核不通过
     * @param  Request $request [description]
     * @return string
     */
    public function refuse(Request $request)
    {
        $userId = $request->userId;

        if (RealNameIdent::where('user_id', $userId)->update(['status' => 2, 'fail_message' => '请按要求填写资料!'])) {
            
            return jsonMessages(2, '审核不通过!');
        }
        return jsonMessages(0, '操作异常错误，请重试!');
    }
}
