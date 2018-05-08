@extends('frontend.v1.layouts.app')

@section('title', '工作台 - 代练')

@section('css')
    <link rel="stylesheet" href="/frontend/css/fixed-table.css">
    <style>

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
        th  .layui-table-cell {
            height: 30px !important;
            line-height: 30px !important;
        }
        .main .right {
            /*min-height: 100px;*/
        }
        .layui-table-cell {
            height: 40px !important;
            line-height: 20px !important;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')
    <div class="layui-card-header" style="padding-top: 20px;">
        <div class="layui-row layui-col-space5">
            <form class="layui-form" action="">
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="text-align: left;padding: 9px 0">订单单号</label>
                        <div class="layui-input-block" style="margin-left: 90px;">
                            <input type="text" name="no" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">玩家旺旺</label>
                        <div class="layui-input-block">
                            <input type="text" name="wang_wang" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">天猫状态</label>
                        <div class="layui-input-block">
                            <select name="taobao_status" lay-search="">
                                <option value="">请选择</option>
                                <option value="1">买家付完款</option>
                                <option value="2">交易成功</option>
                                <option value="3">买家发起退款</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">代练游戏</label>
                        <div class="layui-input-block">
                            <select name="game_id" lay-search="">
                                <option value="">请选择游戏</option>
                                @foreach($game as  $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label"  style="text-align: left;padding: 9px 0">发单客服</label>
                        <div class="layui-input-block" style="margin-left: 90px;">
                            <select name="customer_service_name" lay-search="">
                                @forelse($employee as $item)
                                    <option value="{{ $item->username }}">{{ $item->username }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">代练平台</label>
                        <div class="layui-input-block">
                            <select name="platform">
                                <option value="">全部</option>
                                @foreach (config('partner.platform') as $key => $value)
                                    <option value="{{ $key }}">{{ $value['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md6">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">活动日期</label>
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test-laydate-start" name="start_date" placeholder="开始日期">
                            </div>
                            <div class="layui-form-mid">
                                -
                            </div>
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test-laydate-end" name="end_date" placeholder="结束日期">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin-left: 0px;">
                            <button class="qs-btn" lay-submit="" lay-filter="search">搜索</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="layui-card-body">

        <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="0">全部 <span  class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span></li>
                <li class="" lay-id="1">未接单</li>
                <li class="" lay-id="13">代练中</li>
                <li class="" lay-id="14">待验收
                    <span class="qs-badge quantity-14 layui-hide">1</span>
                </li>
                <li class="" lay-id="15">撤销中
                    <span class="  quantity-15 layui-hide"></span>
                </li>
                <li class="" lay-id="16">仲裁中
                    <span class="quantity-16 layui-hide"></span>
                </li>
                <li class="" lay-id="17">异常
                    <span class="quantity-17 layui-hide"></span>
                </li>
                <li class="" lay-id="18">锁定
                    <span class="quantity-18 layui-hide"></span>
                </li>
                <li class="" lay-id="19">已撤销</li>
                <li class="" lay-id="20">已结算</li>
                <li class="" lay-id="21">已仲裁</li>
                <li class="" lay-id="22">已下架</li>
                <li class="" lay-id="23">强制撤销</li>
            </ul>
            <div class="layui-tab-content"></div>
        </div>
        <div id="order-list">
        </div>
    </div>

    <?php
    $orderData = [];

    foreach ($orders as $item) {

        $detail = $item->detail->pluck('field_value', 'field_name')->toArray();

        // 订单号
        $no = '天猫：<a style="color:#1f93ff" href="' . route('frontend.workbench.leveling.detail', ['no' => $item->no]) . '">' . $item->no . "</a> <br/>";
        if (isset($detail['third']) && $detail['third'] && isset(config('partner.platform')[(int)$detail['third']])) {
            $no .= config('partner.platform')[(int)$detail['third']]['name'] . '：<a style="color:#1f93ff"  href="' . route('frontend.workbench.leveling.detail', ['no' => $item->no]) . '">' . $item->no . '</a>';
        }
        // 玩家旺旺与店名
        $sellerNick = isset($detail['seller_nick']) ? $detail['seller_nick'] : '';
        if (isset($detail['client_wang_wang'])) {
                $sellerNick = '<a style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid=' . $detail['client_wang_wang']  . '&siteid=cntaobao&status=1&charset=utf-8"
                   class="btn btn-save buyer" target="_blank"><img src="/frontend/images/ww.gif" width="20px">'  .$detail['client_wang_wang']. '</a><br/> ' . $sellerNick;
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
            } catch (Exception $exception) {

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
    <script type="text/html" id="noTemplate">
        天猫：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}"> @{{ d.source_order_no }}</a> <br/>
        @{{# if(d.third_name) { }}  @{{ d.third_name }}：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}"> @{{  d.third_order_no }} </a>  @{{#  } }}
    </script>
    <script type="text/html" id="wwTemplate">
        @{{# if(d.third_name) { }}
        <a  style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid=@{{ d.client_wang_wang }}&siteid=cntaobao&status=1&charset=utf-8"  target="_blank" title="@{{ d.client_wang_wang }}"> @{{ d.client_wang_wang }}</a><img
                src="/frontend/images/ww.png" alt="">
        @{{#  } }}
    </script>
    <script type="text/html" id="gameTemplate">
        @{{ d.game_name }} <br/>
        @{{ d.region }} / @{{ d.serve }}
    </script>
    <script type="text/html" id="accountPasswordTemplate">
        @{{ d.account }} <br/>
        @{{ d.password }}
    </script>
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form, layer = layui.layer, element = layui.element, laydate = layui.laydate, table = layui.table;

            // 当前tab 所在位置
            var status = 0;
            var delivery = 0;

            laydate.render({elem: '#start-date'});
            laydate.render({elem: '#end-date'});

            element.on('tab(order-list)', function () {
{{--                window.location.href = '{{ route('frontend.workbench.leveling.test') }}/?status=' + this.getAttribute('lay-id');--}}
            });

            $('.cancel').click(function () {
                layer.closeAll();
            });

            // 搜索
            form.on('submit(search)', function (data) {
                //执行重载
                table.reload('order-list', {
                    where: data.field
                });
                return false;
            });
            // 加载数据
            table.render({
                elem: '#order-list',
                url: '{{ route('frontend.workbench.leveling.order-list') }}',
                method: 'post',
                cols: [[
                    {field: 'no', title: '订单号', width: 220, templet: '#noTemplate'},
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
                height: 'full-420',
                page: {
                    layout: [ 'count', 'prev', 'page', 'next', 'skip'],
                    groups: 10,
                    prev: '上一页',
                    next: '下一页'
                }
            });
        });
    </script>
@endsection