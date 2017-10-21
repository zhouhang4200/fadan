<?php
namespace App\Http\Controllers\Backend\Goods;

use Auth, \Exception;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->name;

        $services = Service::with(['createdAdmin', 'updatedAdmin'])
            ->orderBy('sortord')
            ->paginate(30);

        return view('backend.goods.service.index', compact('services', 'name'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        return Service::find($id);
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
            Service::where('id', $request->id)->update($data);
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->data;
            $data['created_admin_user_id'] = Auth::user()->id;
            $data['updated_admin_user_id'] = Auth::user()->id;
            Service::create($data);
            return response()->json(['code' => 1, 'message' => '添加成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '添加失败']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $service = Service::find($request->id);
        if ($service) {
            $service->status = $request->status;
            $service->created_admin_user_id = Auth::user()->id;
            $service->save();
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } else {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }
}
