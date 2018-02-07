<?php

namespace App\Http\Controllers\Frontend;

use App\Models\LevelingMessage;
use App\Models\UserTransferAccountInfo;
use Auth, Weight;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\LoginHistory;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;
use App\Extensions\Order\ForeignOrder\ForeignOrderFactory;

class LevelingMessageController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message = LevelingMessage::where('user_id', Auth::user()->getPrimaryUserId())->get();
        return view('frontend.leveling-message.index', compact('message'));
    }

    /**
     * 代练留言
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function del(Request $request)
    {
        LevelingMessage::where('id', $request->id)->where('user_id', Auth::user()->getPrimaryUserId())->delete();
        levelingMessageCount(auth()->user()->getPrimaryUserId(), 2);
        return response()->ajax(1, '删除成功');
    }

    /**
     * 代练留言
     */
    public function delAll()
    {
        LevelingMessage::where('user_id', Auth::user()->getPrimaryUserId())->delete();
        levelingMessageCount(auth()->user()->getPrimaryUserId(), 3);
        return response()->ajax(1, '删除成功');
    }
}
