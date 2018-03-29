<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        return view('frontend.user.account.index', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();

        return view('frontend.user.account.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if ($request->password) {
            $data['password'] = bcrypt(clientRSADecrypt($request->password));
        }

        $data['type'] = $request->data['type'];
        $data['leveling_type'] = $request->data['leveling_type'];
        $res = $user->update($data);

        return response()->ajax(1, '更新成功!');
    }
}
