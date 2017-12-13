<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\User;
use App\Models\RbacGroup;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->parent_id == 0) {

            $children = $user->children;
            $name = $request->name;
            $startDate = $request->startDate;
            $endDate = $request->endDate;

            $filters = compact('name', 'startDate', 'endDate');

            $users = User::filter($filters)->where('parent_id', $user->id)->paginate(config('frontend.page'));

            return view('frontend.user.index', compact('name', 'children', 'startDate', 'endDate', 'users'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('frontend.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->parent_id == 0) {  

            $this->validate($request, User::sonRules(), User::messages());

            $data = $request->all();
            $data['password'] = bcrypt($request->password);
            $data['email'] = Auth::id() . 'email' . rand(1, 100000000) . '@qq.com';
            $data['parent_id'] = Auth::id();
            $data['type'] = $request->type;

            $res = User::create($data);

            if (! $res) {

                return back()->withInput()->with('addError', '添加失败!');
            }
            return redirect('/users')->with('succ', '添加成功!');
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
        if (Auth::user()->parent_id == 0) {

            $user = User::find($id);

            $groups = RbacGroup::where('user_id', $id)->get();

            return view('frontend.user.edit', compact('user', 'groups'));
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
        if (Auth::user()->parent_id == 0) {

            $user = User::find($id);

            $this->validate($request, User::updateRules($user->id), User::messages());

            $newPassword = $request->password;

            if ($newPassword) {
                $res = $user->update(['type' => $request->type, 'password' => bcrypt($newPassword)]);

                if (! $res) {
                    return back()->withInput()->with('updateError', '修改密码失败！');
                }
            }
            $user->update(['type' => $request->type]);

            return redirect(route('users.index'))->with('succ', '更新成功!');
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
        if (Auth::user()->parent_id == 0) {

            $user = User::find($id);

            $user->permissions()->detach();
            $bool = $user->delete();

            if (! $bool) {

                return response()->json(['code' => '2', 'message' => '删除失败！']);
            }
            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
    }
}
