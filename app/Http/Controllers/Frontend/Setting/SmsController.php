<?php
namespace App\Http\Controllers\Frontend\Setting;

use Auth;
use App\Exceptions\CustomException;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 短信管理
 * Class SmsController
 * @package App\Http\Controllers\Frontend\Setting
 */
class SmsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $type = $request->input('type', 1);
        $autoSmsTemplate = SmsTemplate::where('user_id', auth()->user()->getPrimaryUserId())->where('type', 1)->paginate(10);
        $userSmsTemplate = SmsTemplate::where('user_id', auth()->user()->getPrimaryUserId())->where('type', 2)->paginate(10);

        if ($request->ajax()) {
            if ($type == 1) {
                return response()->json(\View::make('frontend.v1.setting.sms.auto-list', [
                    'userSmsTemplate' => $userSmsTemplate,
                    'autoSmsTemplate' => $autoSmsTemplate,
                ])->render());
            } else {
                return response()->json(\View::make('frontend.v1.setting.sms.manual-list', [
                    'userSmsTemplate' => $userSmsTemplate,
                    'autoSmsTemplate' => $autoSmsTemplate,
                ])->render());
            }

        }

        return view('frontend.v1.setting.sms.index', compact('autoSmsTemplate', 'userSmsTemplate'));
    }

    /**
     * @param Request $request
     */
    public function add(Request $request)
    {
        try {

            $template = SmsTemplate::firstOrNew(['user_id'=> Auth::user()->getPrimaryUserId(), 'name'=> $request->name]);

            if (!is_null($template->id)) {
                return response()->ajax(0, '该模板已经存在');
            } else {
                $template->user_id = Auth::user()->getPrimaryUserId();
                $template->name = $request->name;
                $template->contents = $request->contents;
                $template->type = 2;
                $template->save();
            }
            return response()->ajax(1, '添加成功');
        } catch (CustomException $exception){
            return response()->ajax(0, '添加失败');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
       $template = SmsTemplate::where('user_id', Auth::user()->getPrimaryUserId())
           ->where('id', $request->id)
           ->first();

        return response()->json(\View::make('frontend.v1.setting.sms.edit', [
            'template' => $template,
        ])->render());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function edit(Request $request)
    {
        try {

            $template = SmsTemplate::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> $request->id])->first();

            if (is_null($template->id)) {
                return response()->ajax(0, '模板不存在');
            } else {
                $template->name = $request->name;
                $template->contents = $request->contents;
                $template->save();
            }
            return response()->ajax(1, '修改成功');
        } catch (CustomException $exception){
            return response()->ajax(0, '修改失败');
        }
    }

    /**
     * 修改状态
     * @param Request $request
     */
    public function status(Request $request)
    {
        $status  = $request->status;
        try {
            if (in_array($status, [1 ,2])) {
                $template = SmsTemplate::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> $request->id])->first();

                if (is_null($template->id)) {
                    return response()->ajax(0, '模板不存在');
                } else {
                    $template->status = $status;
                    $template->save();
                }
                return response()->ajax(1, '设置成功');
            }
            throw new CustomException();
        } catch (CustomException $exception){
            return response()->ajax(0, '设置失败');
        }
    }

    /**
     * @param Request $request
     */
    public function delete(Request $request)
    {
        SmsTemplate::where('user_id', Auth::user()->getPrimaryUserId())
            ->where('id', $request->id)
            ->where('type', 2)
            ->delete();
        return response()->ajax(1, '删除成功');
    }
}
