<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Auth, DB, Log;
use App\Models\User;
use App\Models\Order;
use Illuminate\Console\Command;
use App\Models\UserOrderDetail;

class WriteUserOrderDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'write:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'write user order details';

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

                static::data($user);
            }
        } catch (Exception $e) {

            Log::error('写入数据失败', ['table' => 'user_order_details']);
        }
    }

    protected static function data($masterUser)
    {
        try {
            $start = Carbon::now()->subDays(1)->startOfDay()->toDateTimeString();

            $end = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

            $alls = Order::where(function ($query) use ($masterUser) {
                    $query->where('creator_primary_user_id', $masterUser)
                        ->orWhere('gainer_primary_user_id', $masterUser);
                })
                ->whereBetween('created_at', [$start, $end])
                ->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
                ->first();

            $game = Order::where(function ($query) use ($masterUser) {
                    $query->where('creator_primary_user_id', $masterUser)
                        ->orWhere('gainer_primary_user_id', $masterUser);
                })
                ->whereBetween('created_at', [$start, $end])
                ->select(DB::raw('count(game_id) as count, game_name'))
                ->groupBy('game_id')
                ->latest('count')
                ->first();

            $datas = Order::where(function ($query) use ($masterUser) {
                    $query->where('creator_primary_user_id', $masterUser)
                        ->orWhere('gainer_primary_user_id', $masterUser);
                })
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(['source'])
                ->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
                ->get();

            $resourceGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where (creator_primary_user_id = '$masterUser' or gainer_primary_user_id = '$masterUser') and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

            // 接单
            $allReceives = Order::where('gainer_primary_user_id', $masterUser)
                ->whereBetween('created_at', [$start, $end])
                ->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
                ->first();

            $dataReceives = Order::where('gainer_primary_user_id', $masterUser)
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(['source'])
                ->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
                ->get();

            $receiveGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where gainer_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

            // 发单
            $allSends = Order::where('creator_primary_user_id', $masterUser)
                ->whereBetween('created_at', [$start, $end])
                ->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
                ->first();

            $dataSends = Order::where('creator_primary_user_id', $masterUser)
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(['source'])
                ->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
                ->get();

            $sendGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where creator_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

            // 接单加发单总数据, 不区分渠道
            $dataTotal = [];

            $dataTotal['user_id']            =  $masterUser;
            $dataTotal['source']             =  0;
            $dataTotal['type']               =  3;
            $dataTotal['total']              =  $alls->orderCount;
            $dataTotal['waite_user_receive'] =  $alls->waite;
            $dataTotal['distributing']       =  $alls->fenpei;
            $dataTotal['received']           =  $alls->jiedan;
            $dataTotal['sended']             =  $alls->fahuo;
            $dataTotal['failed']             =  $alls->shibai;
            $dataTotal['after_saling']       =  $alls->shouhou;
            $dataTotal['after_saled']        =  $alls->shouhouwancheng;
            $dataTotal['successed']          =  $alls->dingdanwancheng;
            $dataTotal['canceled']           =  $alls->quxiao;
            $dataTotal['waite_pay']          =  $alls->weifukuan;
            $dataTotal['most_game_name']     =  $game->game_name;
            $dataTotal['most_game_amount']   =  $game->count;
            $dataTotal['time']               =  $start;
            $dataTotal['created_at']         =  Carbon::now()->toDateTimeString();
            $dataTotal['updated_at']         =  Carbon::now()->toDateTimeString();

            // 接单加发单总数据，区分渠道
            $sourceTotal = [];

            foreach ($datas as $k => $data) { 
                $sourceTotal[$k]['user_id']            =  $masterUser;
                $sourceTotal[$k]['source']             =  $data->source;
                $sourceTotal[$k]['type']               =  3;
                $sourceTotal[$k]['total']              =  $data->orderCount;
                $sourceTotal[$k]['waite_user_receive'] =  $data->waite;
                $sourceTotal[$k]['distributing']       =  $data->fenpei;
                $sourceTotal[$k]['received']           =  $data->jiedan;
                $sourceTotal[$k]['sended']             =  $data->fahuo;
                $sourceTotal[$k]['failed']             =  $data->shibai;
                $sourceTotal[$k]['after_saling']       =  $data->shouhou;
                $sourceTotal[$k]['after_saled']        =  $data->shouhouwancheng;
                $sourceTotal[$k]['successed']          =  $data->dingdanwancheng;
                $sourceTotal[$k]['canceled']           =  $data->quxiao;
                $sourceTotal[$k]['waite_pay']          =  $data->weifukuan;
                $sourceTotal[$k]['most_game_name']     =  $resourceGame[$k]->game_name;
                $sourceTotal[$k]['most_game_amount']   =  $resourceGame[$k]->max;
                $sourceTotal[$k]['time']               =  $start;
                $sourceTotal[$k]['created_at']         =  Carbon::now()->toDateTimeString();
                $sourceTotal[$k]['updated_at']         =  Carbon::now()->toDateTimeString();
            }

            // 接单总数据，区分渠道
            $receiveTotal = [];

            foreach ($dataReceives as $k => $dataReceive) { 
                $receiveTotal[$k]['user_id']            =  $masterUser;
                $receiveTotal[$k]['source']             =  $dataReceive->source;
                $receiveTotal[$k]['type']               =  1;
                $receiveTotal[$k]['total']              =  $dataReceive->orderCount;
                $receiveTotal[$k]['waite_user_receive'] =  $dataReceive->waite;
                $receiveTotal[$k]['distributing']       =  $dataReceive->fenpei;
                $receiveTotal[$k]['received']           =  $dataReceive->jiedan;
                $receiveTotal[$k]['sended']             =  $dataReceive->fahuo;
                $receiveTotal[$k]['failed']             =  $dataReceive->shibai;
                $receiveTotal[$k]['after_saling']       =  $dataReceive->shouhou;
                $receiveTotal[$k]['after_saled']        =  $dataReceive->shouhouwancheng;
                $receiveTotal[$k]['successed']          =  $dataReceive->dingdanwancheng;
                $receiveTotal[$k]['canceled']           =  $dataReceive->quxiao;
                $receiveTotal[$k]['waite_pay']          =  $dataReceive->weifukuan;
                $receiveTotal[$k]['most_game_name']     =  $receiveGame[$k]->game_name;
                $receiveTotal[$k]['most_game_amount']   =  $receiveGame[$k]->max;
                $receiveTotal[$k]['time']               =  $start;
                $receiveTotal[$k]['created_at']         =  Carbon::now()->toDateTimeString();
                $receiveTotal[$k]['updated_at']         =  Carbon::now()->toDateTimeString();
            }

            // 发单总数据，区分渠道
            $sendTotal = [];

            foreach ($dataSends as $k => $dataSend) { 
                $sendTotal[$k]['user_id']            =  $masterUser;
                $sendTotal[$k]['source']             =  $dataSend->source;
                $sendTotal[$k]['type']               =  2;
                $sendTotal[$k]['total']              =  $dataSend->orderCount;
                $sendTotal[$k]['waite_user_receive'] =  $dataSend->waite;
                $sendTotal[$k]['distributing']       =  $dataSend->fenpei;
                $sendTotal[$k]['received']           =  $dataSend->jiedan;
                $sendTotal[$k]['sended']             =  $dataSend->fahuo;
                $sendTotal[$k]['failed']             =  $dataSend->shibai;
                $sendTotal[$k]['after_saling']       =  $dataSend->shouhou;
                $sendTotal[$k]['after_saled']        =  $dataSend->shouhouwancheng;
                $sendTotal[$k]['successed']          =  $dataSend->dingdanwancheng;
                $sendTotal[$k]['canceled']           =  $dataSend->quxiao;
                $sendTotal[$k]['waite_pay']          =  $dataSend->weifukuan;
                $sendTotal[$k]['most_game_name']     =  $sendGame[$k]->game_name;
                $sendTotal[$k]['most_game_amount']   =  $sendGame[$k]->max;
                $sendTotal[$k]['time']               =  $start;
                $sendTotal[$k]['created_at']         =  Carbon::now()->toDateTimeString();
                $sendTotal[$k]['updated_at']         =  Carbon::now()->toDateTimeString();
            }

            $has = UserOrderDetail::where('time', $start)->where('user_id', $masterUser)->first();

            if (! $has) {        
                // 不分渠道，接发单，所有状态
                UserOrderDetail::insert($dataTotal);
                // 分渠道，接发单，所有状态
                UserOrderDetail::insert($sourceTotal);
                // 接单，分渠道，所有状态
                UserOrderDetail::insert($receiveTotal);
                // 发单，分渠道，所有状态
                UserOrderDetail::insert($sendTotal);
            } else {
                Log::info('请勿重复写入数据!', ['table' => 'user_order_details']);
            }

        } catch (Exception $e) {

            Log::error('写入数据失败!', ['table' => 'user_order_details']);
        }
        
    }
}
