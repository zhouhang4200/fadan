<?php

namespace App\Http\Controllers;

use App\Services\KamenOrderApi;
use App\Services\TmallOrderApi;
use Illuminate\Http\Request;

use Auth;
use Asset;
use App\Extensions\Asset\Recharge;
use App\Extensions\Asset\Withdraw;
use App\Extensions\Asset\Freeze;
use App\Extensions\Asset\Unfreeze;
use App\Extensions\Asset\Consume;
use App\Extensions\Asset\Refund;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Models\PlatformAsset;
use Carbon\Carbon;
use App\Repositories\Commands\PlatformAssetDailyRepository;
use Order as OrderFacede;
use App\Extensions\Order\Operations\Create;
use App\Extensions\Order\Operations\GrabClose;
use App\Extensions\Order\Operations\Receiving;
use App\Extensions\Order\Operations\Delivery;
use App\Extensions\Order\Operations\DeliveryFailure;
use App\Extensions\Order\Operations\AskForAfterService;
use App\Extensions\Order\Operations\AfterServiceComplete;
use App\Extensions\Order\Operations\TurnBack;
use App\Extensions\Order\Operations\Complete;

use App\Models\ForeignOrder;

use App\Extensions\Order\Operations\Payment;
use App\Extensions\Order\Operations\Cancel;


use App\Repositories\Frontend\UserWithdrawOrderRepository;
use App\Repositories\Api\UserRechargeOrderRepository;

use Artisan;
use App\Models\UserAmountFlow;
use App\Models\UserWithdrawOrder;
use App\Models\Order as OrderModel;
use App\Models\UserReceivingUserControl;
use App\Models\Order;
use Log;

use App\Events\NotificationEvent;

class TestController extends Controller
{
    public function index(UserRechargeOrderRepository $repository)
    {
        return $this->testApiOrder();

        $order = Order::where('no', '2017122715401700000011')->first();

        dd($order->levelingConsult->first()->toArray());
        $this->encrypt();
        return $this->decrypt();

        event(new NotificationEvent('orderRefund', ['amount' => '500.00', 'user_id' => 3]));
        exit("1234");
    }

    public function testAsset()
    {
        Asset::handle(new Recharge(5000.1234, Recharge::TRADE_SUBTYPE_AUTO, '2017101' . rand(1000, 9999), '自动充值', Auth::user()->id, 888));
        Asset::handle(new Freeze(1850.4312, Freeze::TRADE_SUBTYPE_WITHDRAW, '2017101' . rand(1000, 9999), '提现冻结', Auth::user()->id, 888));
        Asset::handle(new Withdraw(550.4565, Withdraw::TRADE_SUBTYPE_MANUAL, '2017101' . rand(1000, 9999), '提现成功', Auth::user()->id, 888));
        Asset::handle(new Unfreeze(310.2342, Unfreeze::TRADE_SUBTYPE_WITHDRAW, '2017101' . rand(1000, 9999), '解冻成功', Auth::user()->id, 888));
        Asset::handle(new Consume(220.4903, Consume::TRADE_SUBTYPE_BROKERAGE, '2017101' . rand(1000, 9999), '消费手续费', Auth::user()->id, 888));
        Asset::handle(new Refund(150.2348, Refund::TRADE_SUBTYPE_BROKERAGE, '2017101' . rand(1000, 9999), '手续费退款', Auth::user()->id, 888));
        Asset::handle(new Expend(70.2329, Expend::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '下订单', Auth::user()->id, 888));
        Asset::handle(new Income(40.0928, Income::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '接单发货', Auth::user()->id, 888));

        // 对账
        $platformAsset = PlatformAsset::first();
        $external = "$platformAsset->total_recharge - $platformAsset->total_withdraw = ";
        $external .= $platformAsset->total_recharge - $platformAsset->total_withdraw;

        $interior = "$platformAsset->amount + $platformAsset->managed + $platformAsset->balance + $platformAsset->frozen = ";
        $interior .= $platformAsset->amount + $platformAsset->managed + $platformAsset->balance + $platformAsset->frozen;
        dump($external, $interior);
    }

    public function testDaily(PlatformAssetDailyRepository $platformAssetDailyRepository)
    {
        $platformAssetDailyRepository->scriptrun('2017-10-18', '2017-10-21');
    }

    public function testOrder()
    {
        // Order::handle(new Create(1, 'taobao-123', 1, 4, 0, 111, ['account' => 'buer2202@163.com', 'version' => '1.0', 'region' => '微信71区']));
        // Order::handle(new Payment('2017111015303300000002', 1));
        // Order::handle(new GrabClose('2017111017452700000007', 1));
        // Order::handle(new Receiving('2017111017452700000007', 1));
        // Order::handle(new Delivery('2017111015492400000005', 1));
        Order::handle(new DeliveryFailure('2017111017452700000007', 1, "我去你妹"));
        // Order::handle(new AskForAfterService('2017111015492400000005', 1, '啦啦啦'));
        // Order::handle(new AfterServiceComplete('2017111015492400000005', 888, 5, '退5块钱'));
        // Order::handle(new TurnBack('2017111015492400000005', 2));
        // Order::handle(new Complete('2017111015492400000005', 28));
        // Order::handle(new Cancel('2017111017411200000006', 1));

        // $arr = \App\Models\OrderHistory::orderBy('id', 'desc')->first();
        // dump(unserialize($arr->before), unserialize($arr->after));
    }

    public function command()
    {
        $exitCode = Artisan::call('migrate');
    }

    public function encrypt()
    {
        $private_key = '-----BEGIN PRIVATE KEY-----
MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAL5CB0BHCL81Ov31
0aZzFv9e6vmzFsvOhdywog57gnJ+QC1lj8ILQ+iBaeseQYD5C9XG1jfVb2k5gpqy
UoIV00ySLri3+V8xY8isGgKISXdyq9+P1aRNq2RS3t49wf4xyQewbgKr0HoH09eV
FgTf6rQeH2MFB326QYFcUnEvKCfBAgMBAAECgYEAuiinKaiXkWfHMgjduwzvmq3I
Isyt6HtKFZcq6hrFl7ualhDC6e3V42EFP04ab9S/VMw6fOU1HvNrrGwBOVGbraeS
K45csy30KEMl6ZOm7rBdqHm3M1xjStWHrfQcvrd8ZM6lJr+8bGveWRwUoTC2kOJY
wg0pYa6hR2VAuHIteLUCQQDeKuH6hBgoE9Z8UvaQVYdn0cpgEZn1eqgAH5YkhhBZ
7x/CIlmASizMiWjgvuA5PShCgdcpbPx64meWIdvhVRQnAkEA2zspikwJLrzjbiOX
UndPzFUlpBV6H7K2f9M5iS05+kBmjzKMXNwMsUb4pjmakUG491OkHWGe36aNkunY
uYfN1wJAWuQ4Z4E7UMos6dgXP51+NB9EKGGLFz8DFGnXx0GB1wlZeNcMvsuZ4GQn
ICt3GHPI0MzF9hC8ipmtv2JCzsE76QJBAJDKnkDsvxPTRRI1B3g7vMRjaBza4nGV
Atuhkdp7uFMDvbjN1c5utyNOkGKYoPFWyubuovGUy+1CfzaMo8rFWrkCQQC9ZNaT
ziowtzttdpQ12IhLcdcfeS1gLtvQ3QIokwb3wHgdhB5knDSTYz/upgr9GRddCv8W
Iuli3G2IJNYc9Cwu
-----END PRIVATE KEY-----';
        $str = json_encode(['order_id' => 2017121917434400000002]); // 长度12,要加密的串
        // 十六进制
        $hexIv = '00000000000000000000000000000000';

        $key = 'PhtVnNtqe4a5R1W5vhwnzBfZ'; // PhtVnNtqe4a5R1W5vhwnzBfZ

        // ******
        $a = openssl_private_encrypt($key, $encrypted, $private_key) ? bin2hex($encrypted) : null; 

// 42efbe94121f7583365f5bfc2cad4466f40163a6a9ff89880ef4f0d2f2217a950459c3b9972d5a1c3654fdb684b6b5b1608935c07420fbe5caf1c743c134308c379742c6c50e0c330dace4a4ef42ea84c04c392d3582248d6d9f19db396630e0da8d750f618db63d2e8c30c832ab92a3ff2bdf8df67a57925c9a9ffea040f8e0
// $encrypted = b"B´¥ö\x12\x1Fuâ6_[³,¡Df¶\x01cª® ëê\x0E¶­Ê‗!zò\x04Y├╣ù-Z\x1C6T²ÂäÂÁ▒`ë5└t ¹Õ╩±ÃC┴40î7ùBã┼\x0E\f3\r¼õñ´BÛä└L9-5é$ìmƒ\x19█9f0Ó┌ìu\x0FaìÂ=.î0╚2½Æú +▀ì÷zWÆ\Üƒ■á@°Ó"

        $hash = hash('sha256', $key, true); // b"7¿T\x1F þd\x04éàt;Yã█xùØ\x04IUHåój²¦ë╩¿¯Z" 十六进制

        //打开算法和模式对应的模块
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, ''); //mcrypt resource @472
        // 初始化加密所需的缓冲区
        // $this->hexToStr($hexIv) = \x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00
        $int = mcrypt_generic_init($td, $hash, $this->hexToStr($hexIv)); // 0
        //获得加密算法的分组大小
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC); // 16
        // 16 - 12 = 4
        $pad = $block - (strlen($str) % $block); // 4
        // 重复一个字符串（字符， 次数）
        $str .= str_repeat(chr($pad), $pad); // name:zhouhang\x03\x03\x03
        //加密数据
        $encrypted = mcrypt_generic($td, $str); //b"àþ<$1\x18\x19W\x06cH¼ä‘w\x05"
        //对加密模块进行清理工作
        mcrypt_generic_deinit($td);
        //关闭加密模块
        mcrypt_module_close($td);
        // 函数把包含数据的二进制字符串转换为十六进制值
        return bin2hex($encrypted); // f38377f61cdad196d12f4a236f24210ba035e742057459d99aeb08686483aec1db2e11636fb469b0bd282629a7559600
    }

    private function hexToStr($hex)
    {
        $string = '';
        // hexdec() 十六进制转为 十进制
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            // ask码对应的字符
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    public function decrypt()
    {
        $private_key = '-----BEGIN PRIVATE KEY-----
MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAL5CB0BHCL81Ov31
0aZzFv9e6vmzFsvOhdywog57gnJ+QC1lj8ILQ+iBaeseQYD5C9XG1jfVb2k5gpqy
UoIV00ySLri3+V8xY8isGgKISXdyq9+P1aRNq2RS3t49wf4xyQewbgKr0HoH09eV
FgTf6rQeH2MFB326QYFcUnEvKCfBAgMBAAECgYEAuiinKaiXkWfHMgjduwzvmq3I
Isyt6HtKFZcq6hrFl7ualhDC6e3V42EFP04ab9S/VMw6fOU1HvNrrGwBOVGbraeS
K45csy30KEMl6ZOm7rBdqHm3M1xjStWHrfQcvrd8ZM6lJr+8bGveWRwUoTC2kOJY
wg0pYa6hR2VAuHIteLUCQQDeKuH6hBgoE9Z8UvaQVYdn0cpgEZn1eqgAH5YkhhBZ
7x/CIlmASizMiWjgvuA5PShCgdcpbPx64meWIdvhVRQnAkEA2zspikwJLrzjbiOX
UndPzFUlpBV6H7K2f9M5iS05+kBmjzKMXNwMsUb4pjmakUG491OkHWGe36aNkunY
uYfN1wJAWuQ4Z4E7UMos6dgXP51+NB9EKGGLFz8DFGnXx0GB1wlZeNcMvsuZ4GQn
ICt3GHPI0MzF9hC8ipmtv2JCzsE76QJBAJDKnkDsvxPTRRI1B3g7vMRjaBza4nGV
Atuhkdp7uFMDvbjN1c5utyNOkGKYoPFWyubuovGUy+1CfzaMo8rFWrkCQQC9ZNaT
ziowtzttdpQ12IhLcdcfeS1gLtvQ3QIokwb3wHgdhB5knDSTYz/upgr9GRddCv8W
Iuli3G2IJNYc9Cwu
-----END PRIVATE KEY-----';
        $code = "5c992c11a96d8da0d51564cc9f26d74951f99066f4c0a60abd43df093860f751db5a3c189056f62906ab2ffd8c4a4e27";
        $hash = '42efbe94121f7583365f5bfc2cad4466f40163a6a9ff89880ef4f0d2f2217a950459c3b9972d5a1c3654fdb684b6b5b1608935c07420fbe5caf1c743c134308c379742c6c50e0c330dace4a4ef42ea84c04c392d3582248d6d9f19db396630e0da8d750f618db63d2e8c30c832ab92a3ff2bdf8df67a57925c9a9ffea040f8e0'; // 加密得到的值
        // dd($hash);
        // $decryptKey = (openssl_private_decrypt(pack("H*", $hash), $decrypted, $private_key)) ? $decrypted : null;//
        // dd($decryptKey);

        // 十六进制
        $hexIv = '00000000000000000000000000000000';
        // 打开算法和模式对应的模块
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, ''); // mcrypt resource @473
        // 初始化加密所需的缓冲区
        $int = mcrypt_generic_init($td, 'PhtVnNtqe4a5R1W5vhwnzBfZ', $this->hexToStr($hexIv)); // 0
        //解密数据 pack 打包成二级制
        $str = mdecrypt_generic($td, pack("H*", $code)); // b"ô¤ÝtQw5WYŽh´C\x00Ò`"
        //获得加密算法的分组大小
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        //对加密模块进行清理工作
        mcrypt_generic_deinit($td);
        //关闭加密模块
        mcrypt_module_close($td);
        // FfMzOFYV4RNItDbkLiqHDnQ4
        return $this->strIppAdding($str); 
    }

    /**
     * @param $string
     * @return bool|string
     */
    private function strIppAdding($string)
    {
        dd(str_random(24));
        // 返回字符的哥字符的ascii 码
        $sLast = ord(substr($string, -1)); // 96
        
        $slastc = chr($sLast); // "`"
        $pCheck = substr($string, -$sLast); // b"ô¤ÝtQw5WYŽh´C\x00Ò`"
        if (preg_match("/$slastc{" . $sLast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $sLast);
            return $string;
        } else {
            return false;
        }
    }

    public function testApiOrder()
    {
        $datas = \App\Services\Show91::getGames();

        $datas = json_decode($datas);

        $arr = [];
        foreach ($datas->games as $k => $data) {
            $arr[$k]['id'] = $data->id;
            $arr[$k]['game_name'] = $data->game_name;
        }
        dd($arr);
        dd($datas->games);
    }
}
