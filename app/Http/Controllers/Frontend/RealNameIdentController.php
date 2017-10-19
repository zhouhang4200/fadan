<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use Redis;
use Illuminate\Http\Request;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;

class RealNameIdentController extends Controller
{
    /**
	 * 实名认证页面
	 * @return response
	 */
    public function showIdent()
    {
    	// return view();
    }

    /**
     * 实名认证 post 方法
     * @param  Illuminate\Http\Request
     * @return void
     */
    public function ident(Request $request)
    {
    	$userId = Auth()->user()->parent_id ?: Auth()->id();

    	$this->validate($request, RealNameIdent::rules(), RealNameIdent::messages());

		if (Redis::get("real:name:ident:phone:$request->phone") !== $request->code) {
			return back()->withInput()->with('codeError', '验证码错误!');
		}

        if (Redis::get("real:name:ident:user:$userId") > 5) {
            return back()->withInput()->with('codeError', '超过当天发送最大次数，请明天再注册!');
        }

		$data                       = $request->all();
		$data['user_id']            = $userId;
		$data['license_number']     = $request->license_number;
		$data['corporation']        = $request->corporation;
		$data['identity_card']      = $request->identity_card;
		$data['phone_number']       = $request->phone_number;
		$data['license_picture']    = $request->license_picture;
		$data['front_card_picture'] = $request->front_card_picture;
		$data['back_card_picture']  = $request->back_card_picture;
		$data['hold_card_picture']  = $request->hold_card_picture;

		if ($realNameIdent = RealNameIdent::create($data)) {

			return redirect('home');
		}
		return back()->withInput()->with('identError', '注册失败！');
    }

    /**
     * 点击图片 ajax 上传
     * @param  Illuminate\Http\Request
     * @return json
     */
    public function uploadImages(Request $request)
	{
		if ($request->hasFile($request->name)) {

			$file = $request->file($request->name);

			$path = public_path("/resources/realname/".date('Ymd')."/");

			if ($request->name == 'license_picture') {

				$license = $this->uploadImage($file, $path);

				return json_encode($license);
			}

			if ($request->name == 'front_card_picture') {

				$front = $this->uploadImage($file, $path);

				return json_encode($front);
			}

			if ($request->name == 'back_card_picture') {

				$back = $this->uploadImage($file, $path);

				return json_encode($back);
			}

			if ($request->name == 'hold_card_picture') {

				$hold = $this->uploadImage($file, $path);

				return json_encode($hold);
			}
		}
	}
}
