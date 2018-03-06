<?php

namespace App\Console\Commands;

use App\Services\DailianMama;
use App\Services\Show91;
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
        $certificate= DailianMama::getTempUploadKey();

        $object = storage_path('s-2018-02-22.log');

        try {
            // 实例化oss
            $ossClient = new OssClient($certificate['AccessKeyId'], $certificate['AccessKeySecret'], substr($certificate['prefix_url'], strlen($certificate['bucket_name']) + 8), false, $certificate['SecurityToken']);
            $result = $ossClient->putObject($certificate['bucket_name'], $certificate['bucket_path'] . 's-2018-02-22.log', 'ddd');
dd($result);
//            $bucketListInfo = $ossClient->listBuckets();
//            $bucketList = $bucketListInfo->getBucketList();
//            foreach($bucketList as $bucket) {
//                print($bucket->getLocation() . "\t" . $bucket->getName() . "\t" . $bucket->getCreatedate() . "\n");
//            }

        } catch (OssException $e) {
            print $e->getMessage() . '1';
        }
    }

}