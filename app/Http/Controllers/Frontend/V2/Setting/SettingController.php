<?php

namespace App\Http\Controllers\Frontend\V2\Setting;

use Exception;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * 短信管理页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function message()
    {
        return view('frontend.v2.setting.message');
    }

    /**
     * 短信管理开关设置
     * @return mixed
     */
    public function messageStatus()
    {
        try {
            if (in_array(request('status'), [0, 1, 2])) {
                $template = SmsTemplate::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> request('id')])->first();

                if (! $template) {
                    return response()->ajax(0, '模板不存在!');
                }

                $template->status = request('status');
                $template->save();

                return response()->ajax(1, '设置成功!');
            }
        } catch (CustomException $e) {
            return response()->ajax(0, '服务器异常!');
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(0, '数据信息有误!');
    }

    /**
     * 短信管理表单数据
     * @return mixed
     */
    public function messageDataList()
    {
        return SmsTemplate::where('user_id', auth()->user()->getPrimaryUserId())->where('type', 1)->paginate(10);
    }

    /**
     * 短信管理修改
     * @return mixed
     */
    public function messageUpdate()
    {
        try {
            $template = SmsTemplate::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> request('id')])->first();

            if (! $template) {
                return response()->ajax(0, '模板不存在!');
            }

            $template->name = request('name');
            $template->contents = request('contents');
            $template->save();

        } catch (CustomException $e){
            return response()->ajax(0, '服务器异常!');
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }
}
