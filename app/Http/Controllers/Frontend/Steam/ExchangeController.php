<?php

namespace App\Http\Controllers\Frontend\Steam;

use App\Http\Controllers\Frontend\Steam\Custom\Helper;
use App\Http\Controllers\Controller;
use App\Models\SteamCdkey;
use App\Models\SteamCdkeyLibrary;
use App\Models\SteamOrder;
use App\Models\SteamAccount;
use App\Models\User;
use App\Models\UserAsset;
use App\Http\Controllers\Frontend\Steam\Services\ExchangeApi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Redis;

class ExchangeController extends Controller
{

    public function index()
    {
        return view('frontend.steam.exchange.index');
    }

    /**
     * CDKey信息视图
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function info(Request $request)
    {
        $cdkeyLibrary = SteamCdkeyLibrary::with([
            'cdkey' => function ($query) {
                $query->with('goodses');
            }
        ])->where('cdk', $request->cdkey)->first();
        if (!$cdkeyLibrary) {
            return back()->withInput()->with('msg', 'CDK不存在');
        }

        if($cdkeyLibrary->cdkey->goodses->is_show == 0){ // 0商品is_show等于0下架了
            return back()->withInput()->with('msg', '此CDK已下架');
        }

        if ($cdkeyLibrary->status == 2) {
            return back()->withInput()->with('msg', '此CDK被冻结');
        }

        $order = SteamOrder::where('cdk', $request->cdkey)->orderBy('created_at', 'desc')->first();
        $effectiveTime = Carbon::parse($cdkeyLibrary->effective_time);
        $effectiveTimeInt = (new Carbon())->diffInSeconds($effectiveTime, false);
        if ($effectiveTimeInt < 0) {
            if ($cdkeyLibrary->status == 1) {
                $cdkeyLibrary->status = 3;
                $cdkeyLibrary->save();
            }
        }

        return view('frontend.steam.exchange.info', compact('cdkeyLibrary', 'order', 'orderError'));
    }

    /**
     * 登录视图
     * @param Request $request
     * @return $this
     */
    public function login(Request $request)
    {
        $cdkey = SteamCdkeyLibrary::with([
            'cdkey' => function ($query) {
                $query->with('goodses');
            }
        ])->where('cdk', $request->cdk)->first();
        return view('frontend.steam.exchange.login', compact('cdkey'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loginExchange(Request $request)
    {
        Helper::log('steam-update-balance', ['id' => 1213]);
        $requestData = $request->input('data');
        $account = $requestData['username'];
        $password = $requestData['password'];
        $cdk = $requestData['cdk'];
        $lo_value = $requestData['lo_value'];
        $m_token = $requestData['phoneNum'];
        $mail = $requestData['mailCode'];
        $code = $requestData['imgCode'];

        //限流
        $rand = rand(1, 10);
        $flag = Redis::get('currentLimit:login-key' . $rand);
        if (!$flag) {
            Redis::set('currentLimit:login-key' . $rand, 1);
            Redis::expire('currentLimit:login-key' . $rand, 1);
        } else {
            return $this->response(10, '购买人数太多，请稍候再试！');
        }

        $cdkeyLibrary = SteamCdkeyLibrary::with(['cdkey' => function ($query) {
            $query->with('goodses');
        }
        ])->where('cdk', $cdk)->first();


        if (!$cdkeyLibrary) {
            return $this->response(10, '此CDK不存在！');
        }

        if($cdkeyLibrary->cdkey->goodses->is_show == 0){ // 0商品is_show等于0下架了
            return $this->response(10, '此Cdk已下架！');
        }

        if ($cdkeyLibrary->status == 2) { // 2 冻结
            return $this->response(10, '此CDK被冻结！');
        }

        if ($cdkeyLibrary->status == 0 ) { // 0 已使用
            return $this->response(10, '此CDK已使用！');
        }

        if ($cdkeyLibrary->status == 4 ) { // 4 处理中
            return $this->response(10, '此CDK在处理中！');
        }

        $user = UserAsset::where('user_id', $cdkeyLibrary->cdkey->user_id)->first();

        if ($user->balance <= 0) { //判断商户金额是否小于0
            return $this->response(10, '商户余额不足');
        }

        //判断当前时间是不是已经到期了
        $effectiveTime = Carbon::parse($cdkeyLibrary->effective_time);
        $effectiveTimeInt = (new Carbon())->diffInSeconds($effectiveTime, false);
        if ($effectiveTimeInt < 0) { //判断数据库到期时间跟当前时间比较
            if($cdkeyLibrary->status == 1){
                $cdkeyLibrary->status = 3; // 3 已过期
                $cdkeyLibrary->save();
            }
            return $this->response(10, '此CDK已过期！');
        }

        //获取服务器IP
        $result = $this->getSip($account);
        if($result['status'] == -1){
            $this->response(10, $result['data']);
        }else{
            $sip =$result['data'];
        }


        if (empty($m_token) && empty($mail) && empty($code)) {
            $resultEncryption = ExchangeApi::accEncryption($account, $password);
            $sign = ExchangeApi::getSign($account, $password);
            $data = (new ExchangeApi($sip))->report($resultEncryption, $sign);
        } else if (!empty($m_token)) {
            $resultEncryption = ExchangeApi::accEncryption('', '', $lo_value, $m_token);
            $sign = ExchangeApi::getSign(null, null, $lo_value, $m_token);
            $data = (new ExchangeApi($sip))->report($resultEncryption, $sign);
        } else if (!empty($mail)) {
            $resultEncryption = ExchangeApi::accEncryption('', '', $lo_value, $mail);
            $sign = ExchangeApi::getSign(null, null, $lo_value, $mail);
            $data = (new ExchangeApi($sip))->report($resultEncryption, $sign);
        } else if (!empty($code)) {
            $resultEncryption = ExchangeApi::accEncryption('', '', $lo_value, $code);
            $sign = ExchangeApi::getSign(null, null, $lo_value, $code);
            $data = (new ExchangeApi($sip))->report($resultEncryption, $sign);
        }

        // 需要口令
        if (isset($data->type) && $data->type == "1") {
            return $this->response(1, $data->msg, ['lo_value' => $data->data]);
        }
        // 需要邮箱验证码
        if (isset($data->type) && $data->type == "2") {
            return $this->response(2, $data->msg, ['lo_value' => $data->data]);
        }
        // 需要验证码
        if (isset($data->type) && $data->type == "3") {
                Helper::log('steam-code', ['sid' => $sip, 'error' => $account]);
            return $this->response(3, '请输入图形验证码', [
                'lo_value' => $data->data,
                'code_img' => '/exchange/code?data=' . urlencode($data->picCodeUrl),
            ]);
        }

        if (isset($data->code) && $data->code == "error") {
                Helper::log('steam-interface-error', ['sid' => $sip, 'error' => $data->msg]);
            return $this->response(4, $data->msg);
        }

        if (isset($data->Code) && $data->Code == 1003) {
            return $this->response(5, $data->Message);
        }
        $steamId = $data->steamLogin;
        $authToken = $data->steamLoginSecure;
        SteamAccount::where('account', $account)->delete();
        Helper::log('steam-order-password', ['sid' => $sip, 'steam_id' => $steamId,'aoth_token'=>$authToken]);
        return $this->order($cdkeyLibrary,$cdk, $steamId ,$authToken , $account, $password, $sip);


    }

    /**
     * 下单
     * @param $cdk
     * @param $steamId
     * @param $authToken
     * @param $account
     * @param $password
     * @param $sip
     * @return string
     */
    public function order($cdkeyLibrary,$cdk, $steamId ,$authToken, $account, $password, $sip)
    {

        try {
            $order = SteamOrder::create([
                'no' => generateOrderNo(),
                'goods_id' => $cdkeyLibrary->cdkey->goods_id,
                'user_id' => $cdkeyLibrary->cdkey->user_id,
                'steam_id' => $steamId,
                'auth_token' => $authToken,
                'cdk' => $cdk,
                'status' => 5,
                'recharge_account' => $account,
                'recharge_password' => $password,
                'consume_money' => '',
                'price' => $cdkeyLibrary->cdkey->goodses->price,
                'sid' => $sip,
                'goods_name' => $cdkeyLibrary->cdkey->goodses->name,
                'game_name' => $cdkeyLibrary->cdkey->goodses->game_name,
                'subid' => $cdkeyLibrary->cdkey->goodses->subid,
                'game_url' => $cdkeyLibrary->cdkey->goodses->game_url,
            ]);
            if ($order) {
                \Redis::rpush("steam:order:order_no", $order->no); // 订单号放入队列
                Helper::log('steam-order-success', ['订单号' => $order->no, 'cdk' => $cdk]);
                SteamCdkeyLibrary::where('cdk', $cdk)->update(['status' => 4]);
                return $this->response(7, '下单成功');
            }
        } catch (\Exception $e) {
            Helper::log('steam-order-error', ['错误信息' => $e->getMessage()]);
            return $this->response(9, $e->getMessage());
        }

    }

    public function code(Request $request)
    {
        $imgs = base64_decode($request->data);
        return \Response::make($imgs)->header('Content-Type', 'image/png');
    }

    protected function response($status = 0, $message = null, $data = [])
    {
        return json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }


    public function getSip($account)
    {
        //获取服务器ip
        $data = [];
        $ip = ExchangeApi::getServerIp();
        if ($ip->code != 1) {
            $data['status'] = -1;
            $data['data'] = $ip->msg;
            return $data;
        }

        $steamAccount = SteamAccount::where('account', $account)->first();
        if (!$steamAccount) {
            SteamAccount::insert([
                'account' => $account,
                'sid' => $ip->sid,
            ]);
            $sip = $ip->sid;
        }else{
            $sip = $steamAccount->sid;
        }
        $data['status'] = 1;
        $data['data'] = $sip;
        return $data;
    }


}
