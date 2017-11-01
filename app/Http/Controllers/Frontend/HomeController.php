<?php

namespace App\Http\Controllers\Frontend;

use Auth, Weight;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\LoginHistory;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;
use App\Extensions\Order\ForeignOrder\ForeignOrderFactory;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $user = Auth::user();

        $loginHistoryTime = LoginHistory::where('user_id', $user->id)->latest('created_at')->value('created_at');

        $masterId = $user->parent_id == 0 ? $user->id : $user->parent_id;

        $ident = RealNameIdent::where('user_id', $masterId)->first();

        return view('frontend.index', compact('user', 'loginHistoryTime', 'ident'));
    }
}
