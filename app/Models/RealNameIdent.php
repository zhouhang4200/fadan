<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Extensions\Revisionable\RevisionableTrait;

/**
 * Class RealNameIdent
 * @package App\Models
 */
class RealNameIdent extends Model
{
    use RevisionableTrait;

    /**
     * 开启监听
     * @var bool
     */
    protected $revisionCreationsEnabled = true;

    /**
     * 自动清除记录
     * @var bool
     */
    protected $revisionCleanup = true;

    /**
     * 保存多少条记录
     * @var int
     */
    protected $historyLimit = 50000;

    /**
     * 不监听的字段
     * @var array
     */
    protected $dontKeepRevisionOf = ['id'];


    public $timestamps = true;

    protected $guarded = [];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public static function personalRules()
    {
    	return [
            'name'               => 'required|max:50',
            'bank_name'          => 'required|max:190',
            'bank_number'        => 'required|max:50',
            'phone_number'       => 'required|max:50',
            'identity_card'      => 'required|max:50',
            'front_card_picture' => 'required',
            'back_card_picture'  => 'required',
			'hold_card_picture'  => 'required',
    	];
    }

    public static function companyRules()
    {
        return [
            'name'                      => 'required|max:50',
            'bank_name'                 => 'required|max:190',
            'bank_number'               => 'required|max:50',
            'license_name'              => 'required|max:50',
            'license_number'            => 'required|max:50',
            'corporation'               => 'required|max:50',
            'phone_number'              => 'required|max:50',
            'license_picture'           => 'required',
            'bank_open_account_picture' => 'required',
            'agency_agreement_picture'  => 'required',
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
     * @param $query
     * @param array $filters
     * @return mixed
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
