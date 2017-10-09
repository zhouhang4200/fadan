<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\User;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->pid == 0) {

            $user = User::where('id', $user->id)->first();
            // 所有的子账号
            $childrenAccounts = $user->children;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->pid == 0) {  

            $data = $request->all();

            $data['type'] = 2;

            $data['pid'] = Auth::id();

            $res = User::create($data);

            if (! $res) {

                return back()->withInput()->with('addError', '添加失败!')
            }
            // return redirect();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        // return view(, compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->pid == 0) {

            $user = User::find($id);

            // return view(, compact('user'));
        }
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
        if (Auth::user()->pid == 0) {

            $user = User::find($id);

            User::rules()['name'] = 'required|string|max:255|unique:users,name,'.$id,

            $this->validate($request, User::rules(), User::messages());

            $newPassword = $request->password;

            $res = $user->update(['password' => $newPassword]);

            if (! $res) {
                return back()->withInput()->with('updateError', '修改密码失败！')
            }
            // return redirect();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->pid == 0) {

            $res = User::destroy($id);

            if (! $res) {
                return back()->with('destroyError', '删除失败！');
            }

            // return redirect();
        }
    }
}
