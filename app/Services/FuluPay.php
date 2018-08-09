<?php
namespace App\Services;

use GuzzleHttp\Client;
use Cache;
use App\Exceptions\CustomException;

class FuluPay
{
    protected $token;

    protected $authTokenApi = 'http://10.0.0.138:8090/oauth/token'; // 获取token接口
    // protected $authTokenApi = 'https://passport.fulu.com/oauth/token'; // 获取token接口

    protected $withdrawApi = 'http://10.0.1.199:11113/api/PushBillStatementOther'; // 提现请求接口

    protected $noticeUrl = 'http://latest.38sd.com/api/test/p'; // 回调地址

    protected static $md5Scramble = 'r85dCuLznlcG0Oho'; // md5 扰码

    public function __construct()
    {
        // 获取token
        $this->token = Cache::remember('fulupay:', 60, function () {
            $data = [
                'grant_type'    => 'password',
                'client_id'     => '10000014',
                'client_secret' => '04D70CE6-7535-44C1-ACD6-76AAF9EED4D8',
                'username'      => '9024',
                'password'      => '123456',
            ];

            $res = (new Client)->request('post', $this->authTokenApi, ['form_params' => $data]);
            $json = $res->getBody()->getContents();
            $res = json_decode($json);

            return $res;
        });
    }

    // 发起提现，返回单号
    public function withdraw(
        $orderPrimaryId,
        $tradeAmount,
        $accType,
        $paymentMode,
        $colleAccount,
        $colleAccountName,
        $colleBankName,
        $colleBankCode = '',
        $colleBankAddr = ''
    )
    {
        $dateStr = date('Ymd');
        $billId = "FL-SYTX-{$dateStr}-{$orderPrimaryId}";
        $data = [
            'BillID'           => $billId,
            'BillType'         => 'SYTX', // 写死的手游提现拼音首字母
            'BillSource'       => 5, // 1.oa 2.erp 3.csc 4.km 5.手游
            'TradeMemCode'     => '',
            'PayCompany'       => '武汉一起游网络科技有限公司',
            'TradeAmount'      => round($tradeAmount, 2), // 金额，2位小数
            'ColleAccount'     => $colleAccount, // 收款账户
            'ColleAccountName' => $colleAccountName, // 账户户名
            'ColleBankName'    => $colleBankName, // 开户行
            'ColleBankCode'    => $colleBankCode, // 开户行银联号
            'ColleBankAddr'    => $colleBankAddr,
            'ColleType'        => 1, // 收款账户类型 1.普通 2.白名单
            'SupplierID'       => '', // 供应商id
            'SupplierName'     => '', // 供应商名
            'SplitStatus'      => 1, // 是否拆分 0.不拆分 1.拆分
            'AccType'          => $accType, // 1.对公 2.对私
            'PaymentMode'      => $paymentMode, // 1.全自动 2.人工干预 3.纯人工
            'CityCode'         => '',
            'Province'         => '',
            'City'             => '',
            'SettleType'       => 1, // 结算方式 1.银行 2.支付宝 3.系统内
            'SiteID'           => '',
            'CreateDate'       => date('Y/m/d H:i:s'),
            'Remark'           => '',
            'NoticeUrl'        => $this->noticeUrl,
        ];

        myLog('finance-api-withdraw', $data);

        $headers = ['Authorization' => "{$this->token->token_type} {$this->token->access_token}"];
        $res = (new Client)->request('post', $this->withdrawApi, ['headers' => $headers, 'json' => $data]);
        $res = $res->getBody()->getContents();

        myLog('finance-api-withdraw', $res);

        $res = json_decode($res);
        if ($res->code != 0) {
            throw new CustomException($res->message);
        }

        return $billId;
    }

    // 验签
    public static function checkSign($data)
    {
        myLog('finance-api-notify', $data);

        $data['Secret'] = self::$md5Scramble;
        $data['BillDate'] = date('Y/m/d H:i:s', strtotime($data['BillDate']));

        ksort($data);

        $str = '';
        foreach ($data as $key => $value) {
            if ($key == 'Sign') continue;
            if (is_array($value)) continue;

            $str .= "{$key}={$value}&";
        }

        $str = rtrim($str, '&');
        $sign = md5($str);

        if ($sign != $data['Sign']) {
            throw new CustomException('签名验证失败');
        }

        return true;
    }
}
