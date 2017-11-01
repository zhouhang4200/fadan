<?php

namespace App\Http\Controllers\Backend;

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
        $startDate = $request->startDate;

        $endDate = $request->endDate;

        $filters = compact('startDate', 'endDate');

        $systemLogs = Revision::filter($filters)->paginate(config('backend.page'));

        return view('backend.system.index', compact('systemLogs', 'startDate', 'endDate'));
    }
}
