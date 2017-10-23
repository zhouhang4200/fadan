<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\User;
use App\Models\Revision;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SystemLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	if (Auth::user()->parent_id == 0) {

	        $startDate = $request->startDate;

	        $endDate = $request->endDate;

	        $filters = compact('startDate', 'endDate');

            $childIds = User::where('parent_id', Auth::id())->pluck('id');

	        $systemLogs = Revision::userFilter($filters)->where(function ($query) use ($childIds) {

                $query->whereIn('user_id', $childIds)->orWhere('user_Id', Auth::id());
                
            })->paginate(config('backend.page'));

	        return view('frontend.user.system.index', compact('systemLogs', 'startDate', 'endDate'));
    	}
    }
}
