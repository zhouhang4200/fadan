<?php

namespace App\Http\Controllers\Frontend\V2\Setting;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessmanContactTemplate;

/**
 * 商户联系方式
 * Class BusinessmanController
 * @package App\Http\Controllers\Frontend\Setting
 */
class BusinessmanContactTemplateController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $template = BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
            ->where('type', request('type', 2))
            ->orderBy('game_id')
            ->orderBy('status')
            ->get();

        return response()->json(['status' => 1, 'data' => $template]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        return response()->json(['status' => 1, 'data' => BusinessmanContactTemplate::find(request('id'))]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $id = request('id', 0);
        $gameId = request('game_id');
        $status = request('status');
        $name = request('name');
        $type = request('type', 2);
        $content = request('content');

        if (in_array($type, [1,2,3])) {

            if ($type == 3) {
                $exist = User::find($content);
                if (!$exist) {
                    return response()->json(['status' => 0, 'message' => '用户不存在']);
                }
            }

            if ($id == 0) {
                if ($status) {
                    BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
                        ->where('game_id', $gameId)
                        ->update(['status' => 0]);
                }
                BusinessmanContactTemplate::create([
                    'user_id' => auth()->user()->getPrimaryUserId(),
                    'name' => $name,
                    'type' => $type,
                    'status' => $status,
                    'game_id' => $gameId,
                    'content' => $content
                ]);
                return response()->json(['status' => 1, 'message' => '添加成功']);
            } else {

                $template = BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
                    ->where('id', $id)
                    ->first();
                $template->name = $name;
                $template->type = $type;
                $template->status = $status;
                $template->game_id = $gameId;
                $template->content = $content;
                $template->save();

                if ($status) {
                    BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
                        ->where('id', '!=', $template->id)
                        ->where('game_id',  $template->game_id)
                        ->update(['status' => 0]);

                    return response()->json(['status' => 1, 'message' => '修改成功']);
                }
            }
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
            ->where('id', $request->id)
            ->delete();
        return response()->json(['status' => 1, 'message' => '删除成功']);
    }
}