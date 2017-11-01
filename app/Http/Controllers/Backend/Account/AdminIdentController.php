<?php

namespace App\Http\Controllers\Backend\Account;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;

class AdminIdentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->name;

        $status = $request->status;

        $startDate = $request->startDate;

        $endDate = $request->endDate;

        $filters = compact('name', 'startDate', 'endDate', 'status');

        $idents = RealNameIdent::filter($filters)->paginate(config('backend.page'));

        // dd($idents);

        return view('backend.account.ident.index', compact('idents', 'name', 'status', 'startDate', 'endDate'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ident = RealNameIdent::find($id);

        return view('backend.account.ident.show', compact('ident'));
    }
}
