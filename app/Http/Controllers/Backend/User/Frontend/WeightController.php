<?php
namespace App\Http\Controllers\Backend\User\Frontend;

use App\Models\User;
use App\Models\UserWeight;
use Auth, \Exception;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeightController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userId = $request->user_id;

        $filters = compact('userId');

        $userWeights = UserWeight::with(['createdAdmin', 'updatedAdmin'])->filter($filters)->paginate(30);

        return view('backend.user.weight.index')->with([
            'userWeights' => $userWeights,
            'userId' => $userId,
        ]);
    }

    public function show($id)
    {
        return UserWeight::find($id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        try {
            $data = $request->data;
            $data['updated_admin_user_id'] = Auth::user()->id;
            UserWeight::where('id', $request->id)->update($data);
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }

}