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
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $this->validate($request, User::updateRules($user->id), User::messages());

        $newPassword = $request->password;

        if ($newPassword) {

            $res = $user->update(['password' => bcrypt($newPassword)]);

            if (! $res) {

                return back()->withInput()->with('updateError', '修改密码失败！');
            }
        }
        return redirect(route('home-accounts.index'))->with('succ', '更新成功!');
    }
}
