<?php

namespace App\Http\Controllers\Frontend\Setting;

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
            $template = BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())->get();

            return  $template;
        }

        return view('frontend.setting.businessman-contact.index')->with([
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
        if ($request->id != 0) {
            $template = BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())
                ->where('id', $request->id)
                ->first();
            $template->name = $request->name;
            $template->status = $request->status;
            $template->game_id = $request->game_id;
            $template->content = $request->content;
            $template->save();
            return response()->ajax(1, '修改成功');
        } else {
            BusinessmanContactTemplate::create([
              'user_id' => auth()->user()->getPrimaryUserId(),
              'name' => $request->name,
              'type' => $request->type,
              'status' => $request->status,
              'game_id' => $request->game_id,
              'content' => $request->content,
            ]);
            return response()->ajax(1, '添加成功');
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