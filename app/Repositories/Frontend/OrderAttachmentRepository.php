<?php
namespace App\Repositories\Frontend;

use App\Models\OrderAttachment;
use App\Exceptions\CustomException;
use App\Services\DailianMama;
use App\Services\Show91;
use OSS\OssClient;
use Storage;
use File;

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
}
