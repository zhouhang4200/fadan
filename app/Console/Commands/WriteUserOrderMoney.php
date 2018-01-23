<?php

namespace App\Console\Commands;

use DB, Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\UserOrderMoney;
use Illuminate\Console\Command;

/**
 * 统计新老订单集市等等每天发单和接单金额
 */
class WriteUserOrderMoney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'write:user-order-moneys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'insert datas to user_order_moneys';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $users = User::where('parent_id', 0)->pluck('id');
            $start = Carbon::now()->subDays(1)->startOfDay()->toDateTimeString();
            $end = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

            foreach ($users as $user) {
                $has = Order::where(function ($query) use ($user) {
                        $query->where('creator_primary_user_id', $user)
                        ->orWhere('gainer_primary_user_id', $user);
                    })
                    ->whereBetween('created_at', [$start, $end])
                    ->first();

                if (! $has) {
                    continue;
                }
                static::write($user);
            }
        } catch (Exception $e) {
            Log::error('写入数据失败', ['table' => 'user_order_moneys']);
        }
    }

    protected static function write($masterUser)
    {
        try {
            $start = Carbon::now()->subDays(1)->startOfDay()->toDateTimeString();
            $end = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();
            // 不分渠道 , 接单和发单
            $statusMoney = Order::where(function ($query) use ($masterUser) {
                        $query->where('creator_primary_user_id', $masterUser)
                            ->orWhere('gainer_primary_user_id', $masterUser);
                    })
                    ->whereBetween('created_at', [$start, $end])
                    ->select(DB::raw('sum(case when status = 1 then amount else 0 end) as waite, sum(case when status = 2 then amount else 0 end) as fenpei, sum(case when status = 3 then amount else 0 end) as jiedan, sum(case when status = 4 then amount else 0 end) as fahuo, sum(case when status = 5 then amount else 0 end) as shibai, sum(case when status = 6 then amount else 0 end) as shouhou, sum(case when status = 7 then amount else 0 end) as shouhouwancheng, sum(case when status = 8 then amount else 0 end) as dingdanwancheng, sum(case when status = 10 then amount else 0 end) as quxiao, sum(case when status = 11 then amount else 0 end) as weifukuan, sum(amount) as total'))
                    ->first();

            // 分渠道 , 接单和发单
            $sourceMoneys = Order::where(function ($query) use ($masterUser) {
                    $query->where('creator_primary_user_id', $masterUser)
                        ->orWhere('gainer_primary_user_id', $masterUser);
                })
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(['source'])
                ->select(DB::raw('sum(case when status = 1 then amount else 0 end) as waite, sum(case when status = 2 then amount else 0 end) as fenpei, sum(case when status = 3 then amount else 0 end) as jiedan, sum(case when status = 4 then amount else 0 end) as fahuo, sum(case when status = 5 then amount else 0 end) as shibai, sum(case when status = 6 then amount else 0 end) as shouhou, sum(case when status = 7 then amount else 0 end) as shouhouwancheng, sum(case when status = 8 then amount else 0 end) as dingdanwancheng, sum(case when status = 10 then amount else 0 end) as quxiao, sum(case when status = 11 then amount else 0 end) as weifukuan, source, sum(amount) as total'))
                ->get();

            // 分渠道， 发单
            $sendMoneys = Order::where('creator_primary_user_id', $masterUser)
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(['source'])
                ->select(DB::raw('sum(case when status = 1 then amount else 0 end) as waite, sum(case when status = 2 then amount else 0 end) as fenpei, sum(case when status = 3 then amount else 0 end) as jiedan, sum(case when status = 4 then amount else 0 end) as fahuo, sum(case when status = 5 then amount else 0 end) as shibai, sum(case when status = 6 then amount else 0 end) as shouhou, sum(case when status = 7 then amount else 0 end) as shouhouwancheng, sum(case when status = 8 then amount else 0 end) as dingdanwancheng, sum(case when status = 10 then amount else 0 end) as quxiao, sum(case when status = 11 then amount else 0 end) as weifukuan, source, sum(amount) as total'))
                ->get();

            // 分渠道 ，接单
            $receiveMoneys = Order::where('gainer_primary_user_id', $masterUser)
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(['source'])
                ->select(DB::raw('sum(case when status = 1 then amount else 0 end) as waite, sum(case when status = 2 then amount else 0 end) as fenpei, sum(case when status = 3 then amount else 0 end) as jiedan, sum(case when status = 4 then amount else 0 end) as fahuo, sum(case when status = 5 then amount else 0 end) as shibai, sum(case when status = 6 then amount else 0 end) as shouhou, sum(case when status = 7 then amount else 0 end) as shouhouwancheng, sum(case when status = 8 then amount else 0 end) as dingdanwancheng, sum(case when status = 10 then amount else 0 end) as quxiao, sum(case when status = 11 then amount else 0 end) as weifukuan, source, sum(amount) as total'))
                ->get();

            // 不分渠道 , 接单和发单
            $dataTotal = [];

            $dataTotal['user_id']            =  $masterUser;
            $dataTotal['source']             =  0;
            $dataTotal['type']               =  3;
            $dataTotal['total']              =  $statusMoney->total;
            $dataTotal['waite_user_receive'] =  $statusMoney->waite;
            $dataTotal['distributing']       =  $statusMoney->fenpei;
            $dataTotal['received']           =  $statusMoney->jiedan;
            $dataTotal['sended']             =  $statusMoney->fahuo;
            $dataTotal['failed']             =  $statusMoney->shibai;
            $dataTotal['after_saling']       =  $statusMoney->shouhou;
            $dataTotal['after_saled']        =  $statusMoney->shouhouwancheng;
            $dataTotal['successed']          =  $statusMoney->dingdanwancheng;
            $dataTotal['canceled']           =  $statusMoney->quxiao;
            $dataTotal['waite_pay']          =  $statusMoney->weifukuan;
            $dataTotal['time']               =  $start;
            $dataTotal['created_at']         =  Carbon::now()->toDateTimeString();
            $dataTotal['updated_at']         =  Carbon::now()->toDateTimeString();
            // 分渠道 , 接单和发单
            $sourceTotal = [];

            foreach ($sourceMoneys as $k => $sourceMoney) { 
                $sourceTotal[$k]['user_id']            =  $masterUser;
                $sourceTotal[$k]['source']             =  $sourceMoney->source;
                $sourceTotal[$k]['type']               =  3;
                $sourceTotal[$k]['total']              =  $sourceMoney->total;
                $sourceTotal[$k]['waite_user_receive'] =  $sourceMoney->waite;
                $sourceTotal[$k]['distributing']       =  $sourceMoney->fenpei;
                $sourceTotal[$k]['received']           =  $sourceMoney->jiedan;
                $sourceTotal[$k]['sended']             =  $sourceMoney->fahuo;
                $sourceTotal[$k]['failed']             =  $sourceMoney->shibai;
                $sourceTotal[$k]['after_saling']       =  $sourceMoney->shouhou;
                $sourceTotal[$k]['after_saled']        =  $sourceMoney->shouhouwancheng;
                $sourceTotal[$k]['successed']          =  $sourceMoney->dingdanwancheng;
                $sourceTotal[$k]['canceled']           =  $sourceMoney->quxiao;
                $sourceTotal[$k]['waite_pay']          =  $sourceMoney->weifukuan;
                $sourceTotal[$k]['time']               =  $start;
                $sourceTotal[$k]['created_at']         =  Carbon::now()->toDateTimeString();
                $sourceTotal[$k]['updated_at']         =  Carbon::now()->toDateTimeString();
            }
             // 分渠道 ，接单
            $receiveTotal = [];

            foreach ($receiveMoneys as $k => $receiveMoney) { 
                $receiveTotal[$k]['user_id']            =  $masterUser;
                $receiveTotal[$k]['source']             =  $receiveMoney->source;
                $receiveTotal[$k]['type']               =  1;
                $receiveTotal[$k]['total'] =  $receiveMoney->total;
                $receiveTotal[$k]['waite_user_receive'] =  $receiveMoney->waite;
                $receiveTotal[$k]['distributing']       =  $receiveMoney->fenpei;
                $receiveTotal[$k]['received']           =  $receiveMoney->jiedan;
                $receiveTotal[$k]['sended']             =  $receiveMoney->fahuo;
                $receiveTotal[$k]['failed']             =  $receiveMoney->shibai;
                $receiveTotal[$k]['after_saling']       =  $receiveMoney->shouhou;
                $receiveTotal[$k]['after_saled']        =  $receiveMoney->shouhouwancheng;
                $receiveTotal[$k]['successed']          =  $receiveMoney->dingdanwancheng;
                $receiveTotal[$k]['canceled']           =  $receiveMoney->quxiao;
                $receiveTotal[$k]['waite_pay']          =  $receiveMoney->weifukuan;
                $receiveTotal[$k]['time']               =  $start;
                $receiveTotal[$k]['created_at']         =  Carbon::now()->toDateTimeString();
                $receiveTotal[$k]['updated_at']         =  Carbon::now()->toDateTimeString();
            }
            // 分渠道， 发单
            $sendTotal = [];

            foreach ($sendMoneys as $k => $sendMoney) { 
                $sendTotal[$k]['user_id']            =  $masterUser;
                $sendTotal[$k]['source']             =  $sendMoney->source;
                $sendTotal[$k]['type']               =  2;
                $sendTotal[$k]['total']              =  $sendMoney->total;
                $sendTotal[$k]['waite_user_receive'] =  $sendMoney->waite;
                $sendTotal[$k]['distributing']       =  $sendMoney->fenpei;
                $sendTotal[$k]['received']           =  $sendMoney->jiedan;
                $sendTotal[$k]['sended']             =  $sendMoney->fahuo;
                $sendTotal[$k]['failed']             =  $sendMoney->shibai;
                $sendTotal[$k]['after_saling']       =  $sendMoney->shouhou;
                $sendTotal[$k]['after_saled']        =  $sendMoney->shouhouwancheng;
                $sendTotal[$k]['successed']          =  $sendMoney->dingdanwancheng;
                $sendTotal[$k]['canceled']           =  $sendMoney->quxiao;
                $sendTotal[$k]['waite_pay']          =  $sendMoney->weifukuan;
                $sendTotal[$k]['time']               =  $start;
                $sendTotal[$k]['created_at']         =  Carbon::now()->toDateTimeString();
                $sendTotal[$k]['updated_at']         =  Carbon::now()->toDateTimeString();
            }

            $has = UserOrderMoney::where('time', $start)->where('user_id', $masterUser)->first();

            if (! $has) {        
                // 不分渠道，接发单，所有状态
                UserOrderMoney::insert($dataTotal);
                // 分渠道，接发单，所有状态
                UserOrderMoney::insert($sourceTotal);
                // 接单，分渠道，所有状态
                UserOrderMoney::insert($receiveTotal);
                // 发单，分渠道，所有状态
                UserOrderMoney::insert($sendTotal);
            } else {
                Log::info('请勿重复写入数据!', ['table' => 'user_order_moneys']);
            }
        } catch (Exception $e) {
            Log::error('写入数据失败!', ['table' => 'user_order_moneys']);
        }
    }
}
