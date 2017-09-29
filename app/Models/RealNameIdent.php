<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RealNameIdent extends Model
{
    public $timestamps = true;

    protected $guarded = ['user_id', 'message', 'status'];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function rules()
    {
    	return [
			'license_name'       => 'required',
			'license_number'     => 'required',
			'corporation'        => 'required',
			'identity_card'      => 'required',
			'phone_number'       => 'required',
			'license_picture'    => 'required',
			'front_card_picture' => 'required',
			'back_card_picture'  => 'required',
			'hold_card_picture'  => 'required',
    	];
    }

    public function messages()
    {
    	return [
			'license_name.required'       => '请填写执照名称！',
			'license_number.required'     => '请填写执照号！',
			'corporation.required'        => '请填写法人代表！',
			'identity_card.required'      => '请填写身份证号！',
			'phone_number.required'       => '请填写手机号！',
			'license_picture.required'    => '请上传营业执照照片！',
			'front_card_picture.required' => '请上传身份证前照照片！',
			'back_card_picture.required'  => '请上传身份证背面照片！',
			'hold_card_picture.required'  => '请上传手持身份证照片！',
    	];
    }

    //返回图片保存的路径
	public function uploadImage(UploadedFile $file, $path)
    {   
        $extension = $file->getClientOriginalExtension();

        if ($extension && ! in_array(strtolower($extension), static::$extensions)) {
            exit('只能上传图片!');
        }

        if (!$file->isValid()) {
            exit('文件上传出错！');
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
}
