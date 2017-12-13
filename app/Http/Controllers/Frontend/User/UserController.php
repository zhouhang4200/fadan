<?php

namespace App\Http\Controllers\Frontend\User;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserController extends Controller
{
	protected static $extensions = ['png', 'jpg', 'jpeg', 'gif'];
	/**
	 * 修改资料
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function updatePersional(Request $request)
    {
        if (Auth::user()->parent_id== 0) {
            $user = Auth::user();
        } else {
            $user = Auth::user()->parent;
        }
        $user->nick_name = $request->data['nick_name'];
        $user->age = $request->data['age'];
        $user->qq = $request->data['qq'];
        $user->wechat = $request->data['wechat'];
        $user->phone = $request->data['phone'];
        $user->wangwang = $request->data['wangwang'];

        $bool = $user->save();

        if ($bool) {
            return response()->json(['code' => 1, 'message' => '修改成功!']);
        }
        return response()->json(['code' => 2, 'message' => '修改失败!']);
    }

     /**
     * 点击图片 ajax 上传
     * @param  Illuminate\Http\Request
     * @return json
     */
    public function uploadImages(Request $request)
    {
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $path = public_path("/resources/users/".date('Ymd')."/");

            $imagePath = $this->uploadImage($file, $path);

            return response()->json(['code' => 1, 'path' => $imagePath]);
        }
    }

    /**
     * 图片上传，返回图片路径
     * @param  Symfony\Component\HttpFoundation\File\UploadedFile $file 
     * @param  $path string
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path)
    {   
        $extension = $file->getClientOriginalExtension();

        if ($extension && ! in_array(strtolower($extension), static::$extensions)) {

            return response()->json(['code' => 2, 'path' => $imagePath]);
        }

        if (! $file->isValid()) {

            return response()->json(['code' => 2, 'path' => $imagePath]);
        }

        if (!file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $randNum = rand(1, 100000000) . rand(1, 100000000);

        $fileName = time().substr($randNum, 0, 6).'.'.$extension;

        $path = $file->move($path, $fileName);

        $path = strstr($path, '/resources');

        return str_replace('\\', '/', $path);
    }

    /**
     * 修改头像
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function updateVoucher(Request $request)
    {
    	if (Auth::user()->parent_id== 0) {
            $user = Auth::user();
        } else {
            $user = Auth::user()->parent;
        }
        $user->voucher = $request->data['voucher'];

        $bool = $user->save();

        if ($bool) {
            return response()->json(['code' => 1, 'message' => '修改成功!']);
        }
        return response()->json(['code' => 2, 'message' => '修改失败!']);
    }
}
