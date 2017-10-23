<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RealNameIdent extends Model
{
    public $timestamps = true;

    protected $guarded = [];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public static function rules()
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

    public static function messages()
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

    /**
     * 子账号查找
     * @param  [type] $query   [description]
     * @param  array  $filters [description]
     * @return Illuminate\Database\Eloquent\query
     */
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['name']) {

            $query->where('user_id', $filters['name']);
        }

        if (is_numeric($filters['status'])) {

            $query->where('status', $filters['status']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('created_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
        }

        return $query->latest('created_at');
    }
}
