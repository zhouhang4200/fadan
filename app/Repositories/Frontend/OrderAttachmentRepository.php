<?php
namespace App\Repositories\Frontend;

use App\Models\OrderAttachment;
use App\Exceptions\CustomException;
use App\Services\Show91;
use Storage;
use File;

class OrderAttachmentRepository
{
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
        $thirdOrderNo = $orderDetail['third_order_no'] ?? '';

        try {
            $dataList = Show91::topic(['oid' => $thirdOrderNo]);
        }
        catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        }

        $description = OrderAttachment::where('order_no', $orderNo)->pluck('description', 'third_file_name');

        foreach ($dataList as $key => $value) {
            $imgKey = basename($value->url);
            $dataList[$key]->description = $description[$imgKey] ?? '无';
        }

        return $dataList;
    }

    /**
     * 保存图片信息并推送到show91
     * @return mixed
     */
    public static function saveImageAndUploadToShow91($orderNo, $diskName, $fileName, $description)
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
        $thirdOrderNo = $orderDetail['third_order_no'] ?? '';

        /*附件处理*/
        // 验证文件
        $filesystemAdapter = Storage::disk($diskName);
        if (!$filesystemAdapter->exists($fileName)) {
            throw new CustomException('上传的文件未找到');
        }

        $filePath = $filesystemAdapter->path($fileName); // 服务器绝对路径
        // $url      = $filesystemAdapter->url($fileName); // 访问url
        $url      = '/resources/uploads/order/' . $fileName; // 访问url
        $mimeType = $filesystemAdapter->mimeType($fileName); // 类型
        $size     = $filesystemAdapter->size($fileName); // 类型
        $md5      = hash_file('md5', $filePath); // 类型

        // 上传到show91
        $postData = [
            'oid'   => $thirdOrderNo,
            'file1' => new \cURLFile($filePath, $mimeType),
        ];

        $thirdFileName = Show91::addpic($postData);

        $OrderAttachment = new OrderAttachment;
        $OrderAttachment->order_no        = $orderNo;
        $OrderAttachment->channel_name    = $channelName;
        $OrderAttachment->third_order_no  = $thirdOrderNo;
        $OrderAttachment->file_name       = $fileName;
        $OrderAttachment->third_file_name = $thirdFileName;
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
