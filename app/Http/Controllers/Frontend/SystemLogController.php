<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\Revision;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SystemLogController extends Controller
{
	public function __contruct()
	{
		// $this->middleware(['role:home.manager']);
	}

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

	        $systemLogs = Revision::userFilter($filters)->paginate(config('backend.page'));

	        return view('frontend.user.system.index', compact('systemLogs', 'startDate', 'endDate'));
    	}
    }
}