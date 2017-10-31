<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\User;
use App\Models\RbacGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserGroupController extends Controller
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

        	$childUsers = $user->children()->whereHas('rbacGroups')->get();

            $name = $request->name;

            $filters = compact('name', 'startDate', 'endDate');

            $users = User::userGroupFilter($filters)->where('parent_id', $user->id)->paginate(config('frontend.page'));

            return view('frontend.user.group.index', compact('name', 'childUsers', 'startDate', 'endDate', 'users'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    	$user = Auth::user();

    	if ($user->parent_id == 0) {

    		$childUser = User::find($request->id);

    		if ($childUser->parent_id != $user->id) {

    			return back()->with('masterError', '子账号与当前登录账号不匹配!');
    		}

	    	$groups = RbacGroup::where('user_id', $user->id)->get();

	        return view('frontend.user.group.create', compact('groups', 'childUser'));
    	}
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

    		$user = User::find($request->id);

    		if ($user->parent_id != Auth::user()->id) {

    			return back()->with('masterError', '子账号与当前登录账号不匹配!');
    		}
    		
    		if (! $request->groups) {

    			return back()->with('missError', '请选择组名!');
    		}

    		$array = $user->rbacgroups()->sync($request->groups);

    		if ($array['attached'] || $array['detached'] || $array['updated']) {

                $rbacGroups = $user->rbacGroups;

                $permissions = $rbacGroups->map(function ($rbacGroup) {

                    return $rbacGroup->permissions;

                })->flatten();

                $user->permissions()->sync($permissions->pluck('id')->toArray());

	            return redirect(route('users.index'))->with('succ', '添加成功!');
	        }
	        return back()->with('createFail', '添加失败！');
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

            $groups = RbacGroup::where('user_id', Auth::id())->get();

            return view('frontend.user.group.edit', compact('user', 'groups'));
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

            if ($user->parent_id != Auth::user()->id) {

    			return back()->with('masterError', '子账号与当前登录账号不匹配!');
    		}

            if (! $request->groups) {

    			return back()->with('missError', '请选择组名!');
    		}

            $array = $user->rbacgroups()->sync($request->groups);

            $rbacGroups = $user->rbacGroups;

            $permissions = $rbacGroups->map(function ($rbacGroup) {

                return $rbacGroup->permissions;
                
            })->flatten();

            $user->permissions()->sync($permissions->pluck('id')->toArray());

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

            $user->rbacGroups()->detach();

            return response()->json(['code' => '1', 'message' => '删除成功']);
        }
    }
}
