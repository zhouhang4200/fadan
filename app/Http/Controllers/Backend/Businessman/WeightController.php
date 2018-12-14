<?php
namespace App\Http\Controllers\Backend\Businessman;

use App\Models\UserWeight;
use Auth, \Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 商户权重
 * Class WeightController
 * @package App\Http\Controllers\Backend\User\Frontend
 */
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

        return view('backend.businessman.weight.index')->with([
            'userWeights' => $userWeights,
            'userId' => $userId,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
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
            $data['updated_admin_user_id'] = auth('admin')->user()->id;
            UserWeight::where('id', $request->id)->update($data);
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }

}