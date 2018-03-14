<?php

namespace App\Console\Commands;

use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Services\DailianMama;
use App\Services\Show91;
use App\Services\SmSApi;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
use OSS\Core\OssException;
use OSS\OssClient;
use TopClient;
use TradeFullinfoGetRequest;

/**
 * Class OrderTestData
 * @package App\Console\Commands
 */
class Temp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        try {
            $filePath = public_path('resources/uploads/order/i4JsH9jj6xXy3seNlP06vJRUXsA5wJF5rq5EMNFs.png');
            // 获取oss 临时上传凭证
            $certificate = DailianMama::getTempUploadKey();
            // 实例化oss 上传文件
            $ossClient = new OssClient($certificate['AccessKeyId'], $certificate['AccessKeySecret'], substr($certificate['prefix_url'], strlen($certificate['bucket_name']) + 8), false, $certificate['SecurityToken']);
            $result = $ossClient->putObject($certificate['bucket_name'], $certificate['bucket_path'] . basename($filePath), file_get_contents($filePath));

            dd($result);
        } catch (OssException $e) {
            print $e->getMessage() . '1';
        }
    }

}