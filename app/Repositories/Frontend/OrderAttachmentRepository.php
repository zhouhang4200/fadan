<?php
namespace App\Repositories\Frontend;

use App\Models\OrderAttachment;
use App\Exceptions\CustomException;
use App\Services\DailianMama;
use App\Services\Leveling\DD373Controller;
use App\Services\Show91;
use OSS\OssClient;
use Storage;
use File;
use DB;
use App\Http\Controllers\Frontend\Workbench\Leveling\IndexController;
use App\Repositories\Frontend\GameRepository;

class OrderAttachmentRepository
{
    /**
     * 获取上传留言图片
     * @param $orderNo
     * @return array
     * @throws CustomException
     */
    public static function dataList($orderNo)
    {
        // 取订单信息
        $order = (new OrderRepository)->detail($orderNo);
        if (empty($order)) {
            throw new CustomException('订单不存在');
        }

        // 取订单详情
        $orderDetail = $order->detail->pluck('field_value', 'field_name');
        // 第三方单号
        $thirdOrderNo = $orderDetail['third'] == 1 ? $orderDetail['show91_order_no'] : $orderDetail['dailianmama_order_no'];

        $dataList = [];
        try {

            if ($orderDetail['third'] == 1) {
                $dataList = Show91::topic(['oid' => $thirdOrderNo]);

            } else if ($orderDetail['third'] == 2) {
                $dataList = DailianMama::getOrderPictureList($thirdOrderNo);
            }

        } catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        }

        $description = OrderAttachment::where('order_no', $orderNo)->pluck('description', 'third_file_name');

        $imageList = [];
        foreach ($dataList as $key => $value) {

            if ($orderDetail['third'] == 1) { // 91代练
                $imgKey = basename($value->url);
                $dataList[$key]->description = $description[$imgKey] ?? '无';
//                $imageList[] = [
//                    'url' => $dataList[$key]['address'],
//                    'username' => $dataList[$key]['nickname'],
//                    'created_at' => $dataList[$key]['createtime'],
//                    'description' => $dataList[$key]['description'],
//                ];
                $imageList[] = [
                    'url' => $value->url,
                    'username' => $value->userName,
                    'created_at' => $value->created_on,
                    'description' => '',
                ];
            } elseif($orderDetail['third'] == 2) { // 代练妈妈
                $imageList[] = [
                    'url' => $dataList[$key]['address'],
                    'username' => $dataList[$key]['nickname'],
                    'created_at' => $dataList[$key]['createtime'],
                    'description' => $dataList[$key]['description'],
                ];
            }
        }

        return $imageList;
    }

    /**
     * 保存图片信息并推送到第三方
     * @param $orderNo
     * @param $diskName
     * @param $fileName
     * @param $description
     * @return mixed
     * @throws CustomException
     */
    public static function saveImageAndUploadToThirdParty($orderNo, $diskName, $fileName, $description)
    {
        // 取订单信息
        $order = (new OrderRepository)->detail($orderNo);
        if (empty($order)) {
            throw new CustomException('订单不存在');
        }

        // 取订单详情
        $orderDetail = $order->detail->pluck('field_value', 'field_name');
        // 第三方名称
        $channelName = $orderDetail['third'] ?? '';
        // 第三方单号
        $thirdOrderNo = $orderDetail['third'] == 1 ? $orderDetail['show91_order_no'] : $orderDetail['dailianmama_order_no'];

        /*附件处理*/
        // 验证文件
        $filesystemAdapter = Storage::disk($diskName);
        if (!$filesystemAdapter->exists($fileName)) {
            throw new CustomException('上传的文件未找到');
        }

        $filePath = $filesystemAdapter->path($fileName); // 服务器绝对路径
        $url      = '/resources/uploads/order/' . $fileName; // 访问url
        $mimeType = $filesystemAdapter->mimeType($fileName); // 类型
        $size     = $filesystemAdapter->size($fileName); // 类型
        $md5      = hash_file('md5', $filePath); // 类型

        $thirdFileName = '';
        $thirdFileUrl = '';
        if ($channelName == 1) {
            // 上传到show91
            $postData = [
                'oid'   => $thirdOrderNo,
                'file1' => new \cURLFile($filePath, $mimeType),
            ];
            $thirdFileName = Show91::addpic($postData);
        } else if ($channelName == 2) {
            $thirdFileName = basename($url);
            // 获取oss 临时上传凭证
            $certificate = DailianMama::getTempUploadKey();
            // 实例化oss 上传文件
            $ossClient = new OssClient($certificate['AccessKeyId'], $certificate['AccessKeySecret'], substr($certificate['prefix_url'], strlen($certificate['bucket_name']) + 8), false, $certificate['SecurityToken']);
            $result = $ossClient->putObject($certificate['bucket_name'], $certificate['bucket_path'] . $thirdFileName, file_get_contents($filePath));
            if (isset($result['oss-request-url'])) {
                // 保存上传图片路径到代练妈妈平台
                DailianMama::savePicture($thirdOrderNo, $result['oss-request-url'], $description);
                $thirdFileUrl = $result['oss-request-url'];
            } else {
                return false;
            }
        }   

        // 其他平台通用  
        if (config('leveling.third_orders')) {
            $mimeTypes = str_replace('image/', '.', $mimeType);
            // 获取订单和订单详情以及仲裁协商信息
            $orderDatas = static::getOrderAndOrderDetailAndLevelingConsult($orderNo);
            $orderDatas['description'] = $description ?? '';
            $orderDatas['file'] = fopen($filePath, 'r');

           // 遍历代练平台
            foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                // 如果订单详情里面存在某个代练平台的订单号
                if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                    // 控制器-》方法-》参数
                    call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['updateImage']], [$orderDatas]);
                }
            }
        }  

        $OrderAttachment = new OrderAttachment;
        $OrderAttachment->order_no        = $orderNo;
        $OrderAttachment->channel_name    = $channelName;
        $OrderAttachment->third_order_no  = $thirdOrderNo;
        $OrderAttachment->file_name       = $fileName;
        $OrderAttachment->third_file_name = $thirdFileName;
        $OrderAttachment->third_file_url  = $thirdFileUrl;
        $OrderAttachment->size            = $size;
        $OrderAttachment->mime_type       = $mimeType;
        $OrderAttachment->url             = $url;
        $OrderAttachment->md5             = $md5;
        $OrderAttachment->description     = $description;
        if (!$OrderAttachment->save()) {
            throw new CustomException('文件记录失败');
        }

        return true;
    }

       /**
     * 获取订单，订单详情，协商仲裁的所有信息
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public static function getOrderAndOrderDetailAndLevelingConsult($orderNo)
    {
        $collectionArr =  DB::select("
            SELECT a.order_no, 
                MAX(CASE WHEN a.field_name='region' THEN a.field_value ELSE '' END) AS region,
                MAX(CASE WHEN a.field_name='serve' THEN a.field_value ELSE '' END) AS serve,
                MAX(CASE WHEN a.field_name='account' THEN a.field_value ELSE '' END) AS account,
                MAX(CASE WHEN a.field_name='password' THEN a.field_value ELSE '' END) AS password,
                MAX(CASE WHEN a.field_name='role' THEN a.field_value ELSE '' END) AS role,
                MAX(CASE WHEN a.field_name='game_leveling_type' THEN a.field_value ELSE '' END) AS game_leveling_type,
                MAX(CASE WHEN a.field_name='game_leveling_title' THEN a.field_value ELSE '' END) AS game_leveling_title,
                MAX(CASE WHEN a.field_name='game_leveling_instructions' THEN a.field_value ELSE '' END) AS game_leveling_instructions,
                MAX(CASE WHEN a.field_name='game_leveling_requirements' THEN a.field_value ELSE '' END) AS game_leveling_requirements,
                MAX(CASE WHEN a.field_name='auto_unshelve_time' THEN a.field_value ELSE '' END) AS auto_unshelve_time,
                MAX(CASE WHEN a.field_name='game_leveling_amount' THEN a.field_value ELSE '' END) AS game_leveling_amount,
                MAX(CASE WHEN a.field_name='game_leveling_day' THEN a.field_value ELSE '' END) AS game_leveling_day,
                MAX(CASE WHEN a.field_name='game_leveling_hour' THEN a.field_value ELSE '' END) AS game_leveling_hour,
                MAX(CASE WHEN a.field_name='security_deposit' THEN a.field_value ELSE '' END) AS security_deposit,
                MAX(CASE WHEN a.field_name='efficiency_deposit' THEN a.field_value ELSE '' END) AS efficiency_deposit,
                MAX(CASE WHEN a.field_name='user_phone' THEN a.field_value ELSE '' END) AS user_phone,
                MAX(CASE WHEN a.field_name='user_qq' THEN a.field_value ELSE '' END) AS user_qq,
                MAX(CASE WHEN a.field_name='source_price' THEN a.field_value ELSE '' END) AS source_price,
                MAX(CASE WHEN a.field_name='client_name' THEN a.field_value ELSE '' END) AS client_name,
                MAX(CASE WHEN a.field_name='client_phone' THEN a.field_value ELSE '' END) AS client_phone,
                MAX(CASE WHEN a.field_name='client_qq' THEN a.field_value ELSE '' END) AS client_qq,
                MAX(CASE WHEN a.field_name='client_wang_wang' THEN a.field_value ELSE '' END) AS client_wang_wang,
                MAX(CASE WHEN a.field_name='game_leveling_require_day' THEN a.field_value ELSE '' END) AS game_leveling_require_day,
                MAX(CASE WHEN a.field_name='game_leveling_require_hour' THEN a.field_value ELSE '' END) AS game_leveling_require_hour,
                MAX(CASE WHEN a.field_name='customer_service_remark' THEN a.field_value ELSE '' END) AS customer_service_remark,
                MAX(CASE WHEN a.field_name='receiving_time' THEN a.field_value ELSE '' END) AS receiving_time,
                MAX(CASE WHEN a.field_name='checkout_time' THEN a.field_value ELSE '' END) AS checkout_time,
                MAX(CASE WHEN a.field_name='customer_service_name' THEN a.field_value ELSE '' END) AS customer_service_name,
                MAX(CASE WHEN a.field_name='third_order_no' THEN a.field_value ELSE '' END) AS third_order_no,
                MAX(CASE WHEN a.field_name='third' THEN a.field_value ELSE '' END) AS third,
                MAX(CASE WHEN a.field_name='poundage' THEN a.field_value ELSE '' END) AS poundage,
                MAX(CASE WHEN a.field_name='price_markup' THEN a.field_value ELSE '' END) AS price_markup,
                MAX(CASE WHEN a.field_name='show91_order_no' THEN a.field_value ELSE '' END) AS show91_order_no,
                MAX(CASE WHEN a.field_name='mayi_order_no' THEN a.field_value ELSE '' END) AS mayi_order_no,
                MAX(CASE WHEN a.field_name='dd373_order_no' THEN a.field_value ELSE '' END) AS dd373_order_no,
                MAX(CASE WHEN a.field_name='dailianmama_order_no' THEN a.field_value ELSE '' END) AS dailianmama_order_no,
                MAX(CASE WHEN a.field_name='hatchet_man_qq' THEN a.field_value ELSE '' END) AS hatchet_man_qq,
                MAX(CASE WHEN a.field_name='hatchet_man_phone' THEN a.field_value ELSE '' END) AS hatchet_man_phone,
                MAX(CASE WHEN a.field_name='game_leveling_requirements_template' THEN a.field_value ELSE '' END) AS game_leveling_requirements_template,
                b.no,
                b.amount,
                b.creator_user_id, 
                b.creator_primary_user_id, 
                b.game_id, 
                b.gainer_user_id, 
                b.gainer_primary_user_id,
                c.user_id,
                c.amount AS pay_amount,
                c.deposit,
                c.api_amount,
                c.api_deposit,
                c.api_service,
                c.status,
                c.consult,
                c.complain,
                c.complete,
                c.remark,
                c.revoke_message,
                c.complain_message
            FROM order_details a
            LEFT JOIN orders b
            ON a.order_no = b.no
            LEFT JOIN leveling_consults c
            ON a.order_no = c.order_no
            WHERE a.order_no='$orderNo'");
        
        $collection = is_array($collectionArr) ? $collectionArr[0] : '';

        if (empty($collection) || ! $collection->no) {
            throw new DailianException('订单号错误');
        }

        return (array) $collection;
    }
}
