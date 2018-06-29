<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Models\User;
use App\Repositories\Frontend\GameRepository;
use Auth;
use Illuminate\Http\Request;
use App\Models\BusinessmanContactTemplate;
use App\Http\Controllers\Controller;

/**
 * 商户联系方式
 * Class BusinessmanController
 * @package App\Http\Controllers\Frontend\Setting
 */
class BusinessmanController extends Controller
{
    /**
     * @param Request $request
     * @param GameRepository $gameRepository
     * @return $view
     */
    public function index(Request $request, GameRepository $gameRepository)
    {
        $game = $gameRepository->availableByServiceId(4);

        $template = BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
            ->where('type', $request->type)
            ->get();

        if ($request->ajax()) {
            $template = BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
                ->orderBy('type')->orderBy('game_id', 'desc')->get();

            return  $template;
        }

        return view('frontend.v1.setting.businessman-contact.index')->with([
            'template' => $template,
            'type' => $request->type,
            'game' => $game,
        ]);
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $id = $request->input('id', 0);
        $gameId = $request->input('game_id', 0);
        $status = $request->input('status', 0);
        $name = $request->name;
        $type = $request->type;
        $content = $request->content;

        if (in_array($type, [1,2,3])) {

            if ($type == 3) {
                $exist = User::find($content);
                if (!$exist) {
                    return response()->ajax(0, '用户ID不存在');
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
                return response()->ajax(1, '添加成功');
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

                    return response()->ajax(1, '修改成功');
                }
            }
        }
    }

    /**
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
            ->where('id', $request->id)
            ->delete();
        return response()->ajax(1, 'success');
    }
}