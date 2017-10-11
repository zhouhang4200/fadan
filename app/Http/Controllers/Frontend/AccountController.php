<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\User;
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
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->pid == 0) {

            $name = $request->name;

            $startDate = $request->startDate;

            $endDate = $request->endDate;

            $filters = compact('name', 'startDate', 'endDate');

            $accounts = User::filter($filters)->where('pid', $user->id)->paginate(config('frontend.page'));

            return view('frontend.account.index', compact('name', 'startDate', 'endDate', 'accounts'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('frontend.account.create');
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

            $this->validate($request, User::rules(), User::messages());

            $data = $request->all();
            $data['password'] = bcrypt($request->password);    
            $data['type'] = 2;
            $data['pid'] = Auth::id();

            $res = User::create($data);

            if (! $res) {

                return back()->withInput()->with('addError', '添加失败!');
            }
            return redirect('/accounts');
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

            User::rules()['name'] = 'required|string|max:255|unique:users,name,' . $id;

            $this->validate($request, User::rules(), User::messages());

            $newPassword = $request->password;

            $res = $user->update(['password' => $newPassword]);

            if (! $res) {
                return back()->withInput()->with('updateError', '修改密码失败！');
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
