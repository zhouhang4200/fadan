@extends('frontend.layouts.app')

@section('title', '工作台 - 代练')

@section('css')
    <link rel="stylesheet" href="/frontend/css/fixed-table.css">
    <style>
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track:vertical::-webkit-scrollbar-track:horizontal {
            background-color: #fff;
            -webkit-background-clip: padding-box;
            background-clip: padding-box;
        }

        ::-webkit-scrollbar-thumb {
            min-height: 28px;
            padding-top: 100;
            background-color: rgba(0, 0, 0, .2);
            -webkit-background-clip: padding-box;
            background-clip: padding-box;
            border-radius: 5px;
            -webkit-box-shadow: inset 1px 1px 0 rgba(0, 0, 0, .1), inset 0 -1px 0 rgba(0, 0, 0, .07);
        }

        .layui-laypage-em {
            background-color: #ff7a00 !important;
        }

        .layui-form-select .layui-input {
            padding-right: 0 !important;
        }

        .layui-table-fixed-r .layui-table-cell {
            overflow: inherit;
        }

        .layui-form-item .layui-inline {
            margin-bottom: 5px;
            margin-right: 5px;
        }

        .layui-form-mid {
            margin-right: 4px;
        }

        .layui-tab-title li {
            min-width: 42px;
        }

        .w-150 {
            width: 150px;
        }

        .w-100 {
            width: 100px;
        }

        .opt-btn {
            color: #1f93ff;
            padding: 0 2px;
            border: none;
            cursor: pointer;
        }

        .layui-form-item {
            margin-bottom: 0
        }

        .pagination > .active span {
            color: #fff;
            background: #ff7a00;
            border: 1px solid #ff7a00;
        }

        th  .layui-table-cell {
            height: 30px !important;
            line-height: 30px !important;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search" method="get">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">&nbsp;&nbsp;&nbsp; 订单号：</label>
                <div class="layui-input-inline">
                    <input type="text" name="no" autocomplete="off" class="layui-input" value="{{ $no }}">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">天猫状态：</label>
                <div class="layui-input-inline" style="">
                    <select name="taobao_status" lay-search="">
                        <option value="">请选择状态</option>
                        <option value="1" @if($taobaoStatus == 1) selected @endif>买家付完款</option>
                        <option value="2" @if($taobaoStatus == 2) selected @endif>交易成功</option>
                        <option value="3" @if($taobaoStatus == 3) selected @endif>买家发起退款</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">代练游戏：</label>
                <div class="layui-input-inline">
                    <select name="game_id" lay-search="">
                        <option value="">请选择游戏</option>
                        @foreach($game as  $key => $value)
                            <option value="{{ $key }}" @if($gameId == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">玩家旺旺：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="wang_wang" autocomplete="off" class="layui-input" value="{{ $wangWang }}">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">发单客服：</label>
                <div class="layui-input-inline" style="">
                    <select name="customer_service_name" lay-search="">
                        <option value="">请选择或输入</option>
                        @forelse($employee as $item)
                            <option data-opt="{{ $item->username }}"
                                    @if($item->username == $customerServiceName) selected @endif>{{ $item->username }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">接单平台：</label>
                <div class="layui-input-inline" style="">
                    <select name="platform">
                        <option value="">全部</option>
                        @foreach (config('partner.platform') as $key => $value)
                            <option data-opt="{{ $key }}"
                                    @if($key == $platform)  selected @endif>{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">发布时间：</label>
                <div class="layui-input-inline" style="width: 150px;">
                    <input type="text" name="start_date" autocomplete="off" class="layui-input" id="start-date"
                           value="{{ $startDate }}">
                </div>
                <div class="layui-input-inline" style="width: 152px;">
                    <input type="text" name="end_date" autocomplete="off" class="layui-input fsDate" id="end-date"
                           value="{{ $endDate }}">
                </div>
                <button class="layui-btn layui-btn-normal " type="submit" function="query" lay-submit="">查询</button>
                <button class="layui-btn layui-btn-normal " type="submit" function="query" lay-submit="">导出</button>

            </div>
        </div>
    </form>

    <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
        <ul class="layui-tab-title">
            <li class="@if($status == 0) layui-this @endif" lay-id="0">全部
                @if($allStatusCount) <span class="layui-badge layui-bg-blue">{{ $allStatusCount }}</span>@endif
            </li>
            <li class="@if($status == 1) layui-this @endif" lay-id="1">
                未接单
                @if(isset($statusCount[1])) <span class="layui-badge layui-bg-blue">{{ $statusCount[1] }}</span>@endif
            </li>
            <li class="@if($status == 13) layui-this @endif" lay-id="13">
                代练中
                @if(isset($statusCount[13])) <span class="layui-badge layui-bg-blue">{{ $statusCount[13] }}</span>@endif
            </li>
            <li class="@if($status == 14) layui-this @endif" lay-id="14">待验收
                @if(isset($statusCount[14])) <span class="layui-badge layui-bg-blue">{{ $statusCount[14] }}</span>@endif
            </li>
            <li class="@if($status == 15) layui-this @endif" lay-id="15">撤销中
                @if(isset($statusCount[15])) <span class="layui-badge layui-bg-blue">{{ $statusCount[15] }}</span>@endif
            </li>
            <li class="@if($status == 16) layui-this @endif" lay-id="16">仲裁中
                @if(isset($statusCount[16])) <span class="layui-badge layui-bg-blue">{{ $statusCount[16] }}</span>@endif
            </li>
            <li class="@if($status == 17) layui-this @endif" lay-id="17">异常
                @if(isset($statusCount[17])) <span class="layui-badge layui-bg-blue">{{ $statusCount[17] }}</span>@endif
            </li>
            <li class="@if($status == 18) layui-this @endif" lay-id="18">锁定
                @if(isset($statusCount[18])) <span class="layui-badge layui-bg-blue">{{ $statusCount[18] }}</span>@endif
            </li>
            <li class="@if($status == 19) layui-this @endif" lay-id="19">已撤销
                @if(isset($statusCount[19])) <span class="layui-badge layui-bg-blue">{{ $statusCount[19] }}</span>@endif
            </li>
            <li class="@if($status == 20) layui-this @endif" lay-id="20">已结算
                @if(isset($statusCount[20])) <span class="layui-badge layui-bg-blue">{{ $statusCount[20] }}</span>@endif
            </li>
            <li class="@if($status == 21) layui-this @endif" lay-id="21">已仲裁
                @if(isset($statusCount[21])) <span class="layui-badge layui-bg-blue">{{ $statusCount[21] }}</span>@endif
            </li>
            <li class="@if($status == 22) layui-this @endif" lay-id="22">已下架
                @if(isset($statusCount[22])) <span class="layui-badge layui-bg-blue">{{ $statusCount[22] }}</span>@endif
            </li>
            <li class="@if($status == 23) layui-this @endif" lay-id="23">强制撤销
                @if(isset($statusCount[23])) <span class="layui-badge layui-bg-blue">{{ $statusCount[23] }}</span>@endif
            </li>
        </ul>
    </div>

    <div class="" id="demo">
    </div>
    <div class="page">
        {!! $orders->appends([
           'orders' => $orders,
           'game' => $game,
           'employee' => $employee,
           'tags' => $tags,
           'no' => $no,
           'customerServiceName' => $customerServiceName,
           'gameId' => $gameId,
           'status' => $status,
           'taobaoStatus' => $taobaoStatus,
           'wangWang' => $wangWang,
           'platform' => $platform,
           'startDate' => $startDate,
           'endDate' => $endDate,
           'statusCount' => $statusCount,
           'allStatusCount' => $allStatusCount,
       ])->render() !!}
    </div>

    <?php
    $orderData = [];

    foreach ($orders as $item) {

        $detail = $item->detail->pluck('field_value', 'field_name')->toArray();

        // 订单号
        $no = '天猫：<a style="color:#1f93ff" href="' . route('frontend.workbench.leveling.detail', ['no' => $item->no]) . '">' . $item->no . "</a> <br/>";
        if (isset($detail['third']) && $detail['third']) {
            $no .= config('partner.platform')[(int)$detail['third']]['name'] . '：<a style="color:#1f93ff"  href="' . route('frontend.workbench.leveling.detail', ['no' => $item->no]) . '">' . $item->no . '</a>';
        }
        // 玩家旺旺与店名
        $sellerNick = '';
        if (isset($detail['client_wang_wang'])) {
                $sellerNick = '<a style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid=' . $detail['client_wang_wang']  . '&siteid=cntaobao&status=1&charset=utf-8"
                   class="btn btn-save buyer" target="_blank"><img src="/frontend/images/ww.gif" width="20px">'  .$detail['client_wang_wang']. '</a><br/> ' . isset($detail['seller_nick']) ? $detail['seller_nick'] : '';
        }

        $paymentAmount = ''; // 支付金额
        $getAmount = ''; // 获得金额
        $poundage = ''; // 手续费
        $profit = ''; // 利润
        $amount = 0;
        $securityDeposit = $detail['security_deposit'] ?? '';
        $efficiencyDeposit = $detail['efficiency_deposit'] ?? '';

        if (!in_array($item->status, [19, 20, 21])) {
            $paymentAmount = '';
            $getAmount = '';
            $poundage = '';
            $profit = '';
        } else {
            try {
                // 支付金额
                if ($item->status == 21) {
                    $amount = $item->leveling_consult->api_amount;
                } else {
                    $amount = $item->leveling_consult->amount;
                }
            } catch (ErrorException $exception) {

            }

            // 支付金额
            $paymentAmount = $amount != 0 ? $amount + 0 : $item->amount + 0;

            $paymentAmount = (float)$paymentAmount + 0;
            $getAmount = (float)(float)$detail['get_amount'] + 0;
            $poundage = (float)$poundage + 0;
            // 利润
            $profit = ((float)$detail['source_price'] - $paymentAmount + (float)$detail['get_amount'] - $poundage) + 0;
        }

        $days = $detail['game_leveling_day'] ?? 0;
        $hours = $detail['game_leveling_hour'] ?? 0;
        $levelingTime = $days . '天' . $hours . '小时'; // 代练时间

        // 如果存在接单时间
        if (isset($detail['receiving_time']) && !empty($detail['receiving_time'])) {
            // 计算到期的时间戳
            $expirationTimestamp = strtotime($detail['receiving_time']) + $days * 86400 + $hours * 3600;
            // 计算剩余时间
            $leftSecond = $expirationTimestamp - time();
            $leftTime = Sec2Time($leftSecond); // 剩余时间
        } else {
            $leftTime = '';
        }

        // 区服
        $region = '';
        if (isset($detail['region'])) {
            $region = '<br/>' . $detail['region'] . '/' . $detail['serve'];
        }
        // 密码
        $password = '';
        if (isset($detail['password'])) {
            $password = '<br/>' . substr_replace($detail['password'], '****', '-1', '4');
        }

        // 操作按钮
        $button = '';
        $btnCount = 0;
        if (auth()->user()->getPrimaryUserId() != $item->creator_primary_user_id && $item->status == 1) {
            $button .= '<a class="opt-btn" data-opt="receive" data-no="' . $item->no . '">接单</a>';
            $btnCount++;
        }

        if (auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 22) {
            $button .= '<a class="opt-btn" data-opt="onSale" data-no="' . $item->no . '">上架</a>';
            $btnCount++;
        }

        if (auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 1) {
            $button .= '<a class="opt-btn" data-opt="offSale" data-no="' . $item->no . '">下架</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && (in_array($item->status, [14, 15, 16, 17, 18, 19, 20, 21, 23]))) {
            $button .= '<a class="opt-btn" data-opt="repeat" data-no="' . $item->no . '">重发</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13 || $item->status == 14 || $item->status == 17)) {

            $button .= '<a class="opt-btn" data-opt="lock" data-no="' . $item->no . '">锁定</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 18) {


            $button .= '<a class="opt-btn" data-opt="cancelLock" data-no="' . $item->no . '">取消锁定</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (isset($item->leveling_consult->consult) && auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id) {
            if ($item->leveling_consult->consult == 1 && $item->status == 15) {

                $button .= '<a class="opt-btn" data-opt="cancelRevoke" data-no="' . $item->no . '">取消撤销</a>';
                $btnCount++;
                if ($btnCount == 3) {
                    $button .= '<br/>';
                }
            } else if ($item->leveling_consult->consult == 2 && ($item->status == 15)) {
                $button .= '<a class="opt-btn" data-opt="agreeRevoke" data-no="' . $item->no . '">同意撤销</a>';
                $button .= '<a class="opt-btn" data-opt="refuseRevoke" data-no="' . $item->no . '">不同意撤销</a>';
                $btnCount = $btnCount + 2;
                if ($btnCount == 3) {
                    $button .= '<br/>';
                }
            }

        } elseif (isset($item->leveling_consult->consult)) {

            if ($item->leveling_consult->consult == 2 && $item->status == 15) {

                $button .= '<a class="opt-btn" data-opt="cancelRevoke" data-no="' . $item->no . '" data-safe=' . $detail["security_deposit"]  ?? '' . 'data-effect="' . $detail["efficiency_deposit"] ?? '' . '" data-amount="' . $item->amount . '">取消撤销</a>';
                $btnCount++;
                if ($btnCount == 3) {
                    $button .= '<br/>';
                }

            } elseif ($item->leveling_consult->consult == 1 && $item->status == 15) {

                $button .= '<a class="opt-btn" data-opt="agreeRevoke" data-no="' . $item->no . '">同意撤销</a>';
                $button .= '<a class="opt-btn" data-opt="refuseRevoke" data-no="' . $item->no . '">不同意撤销</a>';
                $btnCount = $btnCount + 2;
                if ($btnCount == 3) {
                    $button .= '<br/>';
                }

            }
        }

        if ($item->status == 13 || $item->status == 14 || $item->status == 17 || $item->status == 18) {
            $button .= '<a class="opt-btn" data-opt="revoke" data-no="' . $item->no . '" data-safe="' . $securityDeposit  . '"data-effect="' . $efficiencyDeposit  . '" data-amount="' . $item->amount . '">协商撤销</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if ($item->status == 13 || $item->status == 14 || $item->status == 15) {
            $button .= '<a class="opt-btn" data-opt="applyArbitration" data-no="' . $item->no . '">仲裁</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }

        }

        if (isset($item->leveling_consult->complain) && auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id) {
            if ($item->leveling_consult->complain == 1 && $item->status == 16) {
                $button .= '<a class="opt-btn" data-opt="cancelArbitration" data-no="' . $item->no . '">取消仲裁</a>';
                $btnCount++;
                if ($btnCount == 3) {
                    $button .= '<br/>';
                }
            }
        } elseif (isset($item->leveling_consult->complain)) {
            if ($item->leveling_consult->complain == 2 && $item->status == 16) {

                $button .= '<a class="opt-btn" data-opt="cancelArbitration" data-no="' . $item->no . '">取消仲裁</a>';
                $btnCount++;
                if ($btnCount == 3) {
                    $button .= '<br/>';
                }
            }
        }

        if (auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 14) {
            $button .= '<a class="opt-btn" data-opt="complete" data-no="' . $item->no . '">完成</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id) {
            $button .= '<a class="opt-btn" data-opt="message" data-no="' . $item->no . '">留言</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 1 || $item->status == 22)) {
            $button .= '<a class="opt-btn" data-opt="delete" data-no="' . $item->no . '">撤单</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13)) {
            $button .= '<a class="opt-btn" data-opt="applyComplete" data-no="' . $item->no . '">申请完成</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 14)) {
            $button .= '<a class="opt-btn" data-opt="cancelComplete" data-no="' . $item->no . '">取消验收</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13)) {
            $button .= '<a class="opt-btn" data-opt="abnormal" data-no="' . $item->no . '">异常</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        if (!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 17)) {
            $button .= '<a class="opt-btn" data-opt="cancelAbnormal" data-no="' . $item->no . '">取消异常</a>';
            $btnCount++;
            if ($btnCount == 3) {
                $button .= '<br/>';
            }
        }

        // 最后返回的数据
        $orderData[] = [
                'no' => $no,
                'status' => isset(config('order.status_leveling')[$item->status]) ? config('order.status_leveling')[$item->status] : '',
                'seller_nick' => $sellerNick,
                'customer_service_remark' => $detail['customer_service_remark'] ?? '',
                'game_leveling_title' => isset($detail['game_leveling_title']) ? htmlspecialchars($detail['game_leveling_title']) : '',
                'game_name' => $item->game_name . $region,
                'account_password' => $detail['account'] ?? '' . $password,
                'role' => $detail['role'] ?? '',
                'amount' => bcmul($item->amount, 1, 2),
                'efficiency_deposit' => $securityDeposit,
                'security_deposit' => $securityDeposit,
                'created_at' => $item->created_at,
                'receiving_time' => $detail['receiving_time'] ?? '',
                'leveling_time' => $levelingTime,
                'left_time' => $leftTime,
                'hatchet_man_qq' => $detail['hatchet_man_qq'] ?? '',
                'hatchet_man_phone' => $detail['hatchet_man_phone'] ?? '',
                'client_phone' => $detail['client_phone'] ?? '',
                'source_price' => $detail['source_price'] ?? '',
                'payment_amount' => $paymentAmount,
                'get_amount' => $getAmount,
                'poundage' => $poundage,
                'customer_service_name' => $detail['customer_service_name'] ?? '',
                'button' => $button,
        ];
    }
    $jsonOrderList = json_encode($orderData, JSON_HEX_TAG);
    ?>
@endsection

<!--START 底部-->
@section('js')
    <script>
        $('.right').height($(window).height() - 80);
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form, layer = layui.layer, element = layui.element, laydate = layui.laydate, table = layui.table;

            // 当前tab 所在位置
            var status = 0;
            var urgentOrder = 0;
            var delivery = 0;

            laydate.render({elem: '#start-date'});
            laydate.render({elem: '#end-date'});

            element.on('tab(order-list)', function () {
                window.location.href = '{{ route('frontend.workbench.leveling.test') }}/?status=' + this.getAttribute('lay-id');
            });

            $('.cancel').click(function () {
                layer.closeAll();
            });

            table.render({
                elem: '#demo',
                cols: [[
                    {field: 'no', title: '订单号', width: 220},
                    {field: 'status', title: '订单状态', width: 80},
                    {field: 'seller_nick', title: '玩家旺旺', minWidth: 150},
                    {field: 'customer_service_remark', title: '客服备注', minWidth: 160},
                    {field: 'game_leveling_title', title: '代练标题', width: 80},
                    {field: 'game_name', title: '游戏/区/服', width: 100},
                    {field: 'account_password', title: '账号/密码', width: 100},
                    {field: 'role', title: '角色名称', width: 100},
                    {field: 'amount', title: '代练价格', width: 100},
                    {field: 'efficiency_deposit', title: '效率保证金', width: 100},
                    {field: 'security_deposit', title: '安全保证金', width: 100},
                    {field: 'created_at', title: '发单时间', width: 100},
                    {field: 'receiving_time', title: '接单时间', width: 100},
                    {field: 'leveling_time', title: '代练时间', width: 100},
                    {field: 'left_time', title: '剩余时间', width: 100},
                    {field: 'hatchet_man_qq', title: '打手QQ', width: 100},
                    {field: 'hatchet_man_phone', title: '打手电话', width: 100},
                    {field: 'city', title: '号主电话', width: 100},
                    {field: 'source_price', title: '来源价格', width: 100},
                    {field: 'payment_amount', title: '支付金额', width: 100},
                    {field: 'get_amount', title: '获得金额', width: 100},
                    {field: 'poundage', title: '手续费', width: 100},
                    {field: 'city', title: '利润', width: 100},
                    {field: 'customer_service_name', title: '发单客服', width: 100},
                    {field: 'button', title: '操作', width: 140, sort: true, fixed: 'right'}
                ]],
                height: 'full-480',
                data: <?= $jsonOrderList ?>,
//                even: true,
                size: 'sm' //小尺寸的表格
            });
        });
    </script>
@endsection