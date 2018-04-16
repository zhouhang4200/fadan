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
            padding-right:0 !important;
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
            min-width: 55px;
        }
        .w-150 {
            width: 150px;
        }
        .w-100 {
            width: 100px;
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
                    <select name="customer_service_name" lay-search="">
                        <option value="">请选择或输入</option>
                        <option value="1">买家付完款</option>
                        <option value="2">交易成功</option>
                        <option value="3">买家发起退款</option>
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
                <label class="layui-form-mid">号主旺旺：</label>
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
                            <option value="{{ $item->username }}" @if($item->username == $customerServiceName) selected @endif>{{ $item->username }}</option>
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
                            <option value="{{ $key }}" @if($key == $platform)  selected @endif>{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">发布时间：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="start_date" autocomplete="off" class="layui-input" id="start-date" value="{{ $startDate }}">
                </div>
                <div class="layui-input-inline" style="">
                    <input type="text" name="end_date" autocomplete="off" class="layui-input fsDate" id="end-date" value="{{ $endDate }}">
                </div>
                <button class="layui-btn layui-btn-normal " type="submit" function="query" lay-submit="">查询</button>
                <button class="layui-btn layui-btn-normal " type="submit" function="query" lay-submit="">导出</button>

            </div>
        </div>
    </form>

    <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
        <ul class="layui-tab-title">
            <li class="@if($status == 0) layui-this @endif" lay-id="0">全部 <span
                        class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span>
            </li>
            <li class="@if($status == 1) layui-this @endif" lay-id="1">未接单</li>
            <li class="@if($status == 13) layui-this @endif" lay-id="13">代练中</li>
            <li class="@if($status == 14) layui-this @endif" lay-id="14">待验收
                <span class="layui-badge layui-bg-blue quantity-14 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 14, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 14, 3) }}</span>
            </li>
            <li class="@if($status == 15) layui-this @endif" lay-id="15">撤销中
                <span class="layui-badge layui-bg-blue quantity-15 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 15, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 15, 3) }}</span>
            </li>
            <li class="@if($status == 16) layui-this @endif" lay-id="16">仲裁中
                <span class="layui-badge layui-bg-blue quantity-16 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 16, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 16, 3) }}</span>
            </li>
            <li class="@if($status == 17) layui-this @endif" lay-id="17">异常
                <span class="layui-badge layui-bg-blue quantity-17 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 17, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 17, 3) }}</span>
            </li>
            <li class="@if($status == 18) layui-this @endif" lay-id="18">锁定
                <span class="layui-badge layui-bg-blue quantity-18 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 18, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 18, 3) }}</span>
            </li>
            <li class="@if($status == 19) layui-this @endif" lay-id="19">已撤销</li>
            <li class="@if($status == 20) layui-this @endif" lay-id="20">已结算</li>
            <li class="@if($status == 21) layui-this @endif" lay-id="21">已仲裁</li>
            <li class="@if($status == 22) layui-this @endif" lay-id="22">已下架</li>
            <li class="@if($status == 23) layui-this @endif" lay-id="23">强制撤销</li>
        </ul>
    </div>

    <div class="fixed-table-box row-col-fixed">
        <!-- 表头 start -->
        <div class="fixed-table_header-wraper">
            <table class="fixed-table_header" cellspacing="0" cellpadding="0" border="0">
                <thead>
                <tr>
                    <td  data-fixed="true"><div class="table-cell" style="width: 220px">订单号</div></td>
                    <th><div class="table-cell w-150" style="line-height: 40px">号主旺旺</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">客服备注</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">代练标题</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">游戏/区/服</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">账号/密码</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">角色名称</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">订单状态</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">代练价格</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">效率保证金</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">安全保证金</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">发单时间</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">接单时间</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">代练时间</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">剩余时间</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">打手呢称</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">打手电话</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">号主电话</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">来源价格</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">支付金额</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">获得金额</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">手续费</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">利润</div></th>
                    <th><div class="table-cell w-150" style="line-height: 40px">发单客服</div></th>
                    <th class="w-100" data-fixed="true" data-direction="right"><div class="table-cell w-150" style="line-height: 40px">操作</div></th>
                </tr>
                </thead>
            </table>
        </div>
        <!-- 表头 end -->
        
        <!-- 表格内容 start -->
        <div class="fixed-table_body-wraper data-table-content">
            <table class="fixed-table_body  layui-form" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                @forelse($orders as $item)
                    @php 
                        $detail = $item->detail->pluck('field_value', 'field_name')->toArray();  

                        $paymentAmount = '';
                        $getAmount= '';
                        $poundage = '';
                        $profit= '';
                        $amount = 0;
                        if (!in_array($item->status, [19, 20, 21])){
                            $paymentAmount = '';
                            $getAmount= '';
                            $poundage = '';
                            $profit= '';
                        } else {
                            try {
                               // 支付金额
                                if ($item->status == 21) {
                                    $amount = $item->leveling_consult->api_amount;
                                } else {
                                    $amount = $item->leveling_consult->amount;
                                }
                            } catch (ErrorException $exception) {
                                myLog('ex', [$exception->getMessage()]);
                            }

                            // 支付金额
                            $paymentAmount = $amount !=0 ?  $amount + 0:  $item->amount + 0;
        
                            $paymentAmount = (float)$paymentAmount + 0;
                            $getAmount= (float)$getAmount + 0;
                            $poundage = (float)$poundage + 0;
                            // 利润
                            $profit = ((float)$detail['source_price'] - $paymentAmount + $getAmount - $poundage) + 0;
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
                    
                    @endphp
                    <tr>
                        <td>
                            <div class="table-cell" style="width: 220px">
                                天猫：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no={{ $item['no'] }}">{{ $detail['source_order_no'] or $item->no  }}</a> <br/>
                                @if(isset($detail['third']) && $detail['third'])
                                    {{ config('parent.platform')[$detail['third']]['name'] }}：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no={{ $item['no'] }}"> {{ $detail['third_order_no'] }} </a>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="table-cell w-150">
                            @if(isset($detail['client_wang_wang']))
                                <a href="http://www.taobao.com/webww/ww.php?ver=3&touid={{ $detail['client_wang_wang'] }}&siteid=cntaobao&status=1&charset=utf-8" class="btn btn-save buyer" target="_blank">联系旺旺号<img src="/frontend/images/ww.gif"></a></li>
                            @endif
                            </div>
                        </td>
                        <td><div class="table-cell w-150"> {{ $detail['customer_service_remark'] or '' }}</div></td>
                        <td><div class="table-cell w-150"> {{ $detail['game_leveling_title'] or '' }}</div></td>
                        <td><div class="table-cell w-150"> {{ $item->game_name }} <br/> {{ isset($detail['region']) ?  $detail['region'] . '/' . $detail['serve'] : ''  }}</div></td>
                        <td><div class="table-cell w-150"> {{ $detail['account'] or '' }} <br/> {{ $detail['password'] or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['role'] or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ isset(config('order.status_leveling')[$item->status]) ? config('order.status_leveling')[$item->status] : '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $item->amount }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['efficiency_deposit'] or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['security_deposit'] or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $item->created_at  }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['receiving_time'] or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $levelingTime }}</div></td>
                        <td><div class="table-cell w-150">{{ $leftTime }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['hatchet_man_name']   or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['hatchet_man_phone']   or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['client_phone']   or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['original_amount']   or '' }}</div></td>
                        <td><div class="table-cell w-150">{{ $paymentAmount }}</div></td>
                        <td><div class="table-cell w-150">{{ $getAmount  }}</div></td>
                        <td><div class="table-cell w-150">{{ $poundage  }}</div></td>
                        <td><div class="table-cell w-150">{{ $profit }}</div></td>
                        <td><div class="table-cell w-150">{{ $detail['customer_service_name'] or '' }}</div></td>
                        <td>
                            <div class="table-cell w-150" style="overflow:visible;padding-right: 15px">
                                <select  lay-filter="order-operation">
                                        <option value="">请选择操作</option>
                                        <option value="detail" data-no="{{ $item->no }}" >详情</option>

                                        @if(auth()->user()->getPrimaryUserId() != $item->creator_primary_user_id  && $item->status == 1)
                                            <option value="receive" data-no="{{ $item->no }}" >接单</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 22)
                                            <option value="onSale" data-no="{{ $item->no }}" >上架</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 1)
                                            <option value="offSale" data-no="{{ $item->no }}" >下架</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 14 || $item->status == 15 || $item->status == 16 || $item->status == 17 || $item->status == 18 || $item->status == 19 || $item->status == 20 || $item->status == 21))
                                            <option value="repeat" data-no="{{ $item->no }}" >重发</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13 || $item->status == 14 || $item->status == 17))
                                            <option value="lock" data-no="{{ $item->no }}" >锁定</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 18)
                                            <option value="cancelLock" data-no="{{ $item->no }}" >取消锁定</option>
                                        @endif

                                        {{--@if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id)--}}
                                        {{--@if(d.consult == 1 && $item->status == 15)--}}
                                        {{--<option value="cancelRevoke" data-no="{{ $item->no }}" >取消撤销</option>--}}
                                        {{--@elseif(d.consult == 2 && ($item->status == 15 || $item->status == 16))--}}
                                        {{--<option value="agreeRevoke" data-no="{{ $item->no }}" >同意撤销</option>--}}
                                        {{--<option value="refuseRevoke" data-no="{{ $item->no }}" >不同意撤销</option>--}}
                                        {{--@endif--}}
                                        {{--@else--}}
                                        {{--@if(d.consult == 2 && $item->status == 15)--}}
                                        {{--<option value="cancelRevoke" data-no="{{ $item->no }}" >取消撤销</option>--}}
                                        {{--@elseif(d.consult == 1 && ($item->status == 15 || $item->status == 16))--}}
                                        {{--<option value="agreeRevoke" data-no="{{ $item->no }}" >同意撤销</option>--}}
                                        {{--<option value="refuseRevoke" data-no="{{ $item->no }}" >不同意撤销</option>--}}
                                        {{--@endif--}}
                                        {{--@endif--}}

                                        @if($item->status == 13 || $item->status == 14 || $item->status == 17 || $item->status == 18)
                                            <option value="revoke" data-no="{{ $item->no }}" >撤销</option>
                                        @endif

                                        @if($item->status == 13 || $item->status == 14 || $item->status == 15)
                                            <option value="applyArbitration" data-no="{{ $item->no }}" >申请仲裁</option>
                                        @endif

                                        {{--@if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id)--}}
                                        {{--@if(d.complain == 1 && $item->status == 16)--}}
                                        {{--<option value="cancelArbitration" data-no="{{ $item->no }}" >取消仲裁</option>--}}
                                        {{--@endif--}}
                                        {{--@else--}}
                                        {{--@if(d.complain == 2 && $item->status == 16)--}}
                                        {{--<option value="cancelArbitration" data-no="{{ $item->no }}" >取消仲裁</option>--}}
                                        {{--@endif--}}
                                        {{--@endif--}}

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 14)
                                            <option value="complete" data-no="{{ $item->no }}" >完成</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id)
                                            <option value="message" data-no="{{ $item->no }}" >留言</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id)
                                            <option value="operationRecord" data-no="{{ $item->no }}" >操作记录</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && isset($detail['client_wang_wang']))
                                            <option value="wangWang" data-no="{{ $item->no }}"  data-wang-wang="{{ $detail['client_wang_wang'] }}">联系旺旺号</option>
                                        @endif

                                        @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 1 || $item->status == 22))
                                            <option value="delete" data-no="{{ $item->no }}" >撤单</option>
                                        @endif

                                        @if(!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13))
                                            <option value="applyComplete" data-no="{{ $item->no }}" >申请完成</option>
                                        @endif

                                        @if(!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 14))
                                            <option value="cancelComplete" data-no="{{ $item->no }}" >取消验收</option>
                                        @endif
                                        @if(!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13))
                                            <option value="abnormal" data-no="{{ $item->no }}" >异常</option>
                                        @endif
                                        @if(!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 17))
                                            <option value="cancelAbnormal" data-no="{{ $item->no }}" >取消异常</option>
                                        @endif
                                    </select>
                            </div>
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </div>
        <!-- 表格内容 end -->
        <!-- 固定列 start -->
        <div class="fixed-table_fixed fixed-table_fixed-left">
            <div class="fixed-table_header-wraper">
                <table class="fixed-table_header" cellspacing="0" cellpadding="0" border="0">
                    <thead>
                    <tr>
                        <th class="w-150"><div class="table-cell" style="width: 220px;line-height: 40px">订单号</div></th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="fixed-table_body-wraper data-table-fixed-content">
                <table class="fixed-table_body" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                    @forelse($orders as $item)
                    @php $detail = $item->detail->pluck('field_value', 'field_name')->toArray();  @endphp
                    <tr>
                        <td>
                            <div class="table-cell" style="width: 220px">
                                天猫：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no={{ $item['no'] }}">{{ $detail['source_order_no'] or $item->no  }}</a> <br/>
                                @if(isset($detail['third']) && $detail['third'])
                                    {{ config('parent.platform')[$detail['third']] }}：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no={{ $item['no'] }}"> {{ $detail['third_order_no'] }} </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="fixed-table_fixed fixed-table_fixed-right">
            <div class="fixed-table_header-wraper">
                <table class="fixed-table_header" cellspacing="0" cellpadding="0" border="0">
                    <thead>
                    <tr>
                        <th class="w-100"><div class="table-cell w-150"  style="overflow:visible;padding-right: 15px;line-height: 40px">操作</div></th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="fixed-table_body-wraper data-table-fixed-content">
                <table class="fixed-table_body layui-form" cellspacing="0" cellpadding="0" border="0">
                    <tbody>

                    @forelse($orders as $item)
                        @php $detail = $item->detail->pluck('field_value', 'field_name')->toArray();  @endphp
                        <tr>
                            <td class="w-100">

                                <div class="table-cell w-150" style="overflow:visible;padding-right: 15px">
                                    <select  lay-filter="order-operation">
                                            <option value="">请选择操作</option>
                                            <option value="detail" data-no="{{ $item->no }}" >详情</option>

                                            @if(auth()->user()->getPrimaryUserId() != $item->creator_primary_user_id  && $item->status == 1)
                                            <option value="receive" data-no="{{ $item->no }}" >接单</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 22)
                                            <option value="onSale" data-no="{{ $item->no }}" >上架</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 1)
                                            <option value="offSale" data-no="{{ $item->no }}" >下架</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 14 || $item->status == 15 || $item->status == 16 || $item->status == 17 || $item->status == 18 || $item->status == 19 || $item->status == 20 || $item->status == 21))
                                            <option value="repeat" data-no="{{ $item->no }}" >重发</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13 || $item->status == 14 || $item->status == 17))
                                            <option value="lock" data-no="{{ $item->no }}" >锁定</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 18)
                                            <option value="cancelLock" data-no="{{ $item->no }}" >取消锁定</option>
                                            @endif

                                            {{--@if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id)--}}
                                                {{--@if(d.consult == 1 && $item->status == 15)--}}
                                                {{--<option value="cancelRevoke" data-no="{{ $item->no }}" >取消撤销</option>--}}
                                                {{--@elseif(d.consult == 2 && ($item->status == 15 || $item->status == 16))--}}
                                                {{--<option value="agreeRevoke" data-no="{{ $item->no }}" >同意撤销</option>--}}
                                                {{--<option value="refuseRevoke" data-no="{{ $item->no }}" >不同意撤销</option>--}}
                                                {{--@endif--}}
                                            {{--@else--}}
                                                {{--@if(d.consult == 2 && $item->status == 15)--}}
                                                {{--<option value="cancelRevoke" data-no="{{ $item->no }}" >取消撤销</option>--}}
                                                {{--@elseif(d.consult == 1 && ($item->status == 15 || $item->status == 16))--}}
                                                {{--<option value="agreeRevoke" data-no="{{ $item->no }}" >同意撤销</option>--}}
                                                {{--<option value="refuseRevoke" data-no="{{ $item->no }}" >不同意撤销</option>--}}
                                               {{--@endif--}}
                                            {{--@endif--}}

                                            @if($item->status == 13 || $item->status == 14 || $item->status == 17 || $item->status == 18)
                                            <option value="revoke" data-no="{{ $item->no }}" >撤销</option>
                                            @endif

                                            @if($item->status == 13 || $item->status == 14 || $item->status == 15)
                                            <option value="applyArbitration" data-no="{{ $item->no }}" >申请仲裁</option>
                                            @endif

                                            {{--@if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id)--}}
                                                {{--@if(d.complain == 1 && $item->status == 16)--}}
                                                {{--<option value="cancelArbitration" data-no="{{ $item->no }}" >取消仲裁</option>--}}
                                                {{--@endif--}}
                                             {{--@else--}}
                                                {{--@if(d.complain == 2 && $item->status == 16)--}}
                                                {{--<option value="cancelArbitration" data-no="{{ $item->no }}" >取消仲裁</option>--}}
                                                {{--@endif--}}
                                            {{--@endif--}}

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && $item->status == 14)
                                            <option value="complete" data-no="{{ $item->no }}" >完成</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id)
                                            <option value="message" data-no="{{ $item->no }}" >留言</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id)
                                            <option value="operationRecord" data-no="{{ $item->no }}" >操作记录</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && isset($detail['client_wang_wang']))
                                            <option value="wangWang" data-no="{{ $item->no }}"  data-wang-wang="{{ $detail['client_wang_wang'] }}">联系旺旺号</option>
                                            @endif

                                            @if(auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 1 || $item->status == 22))
                                            <option value="delete" data-no="{{ $item->no }}" >撤单</option>
                                            @endif

                                            @if(!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13))
                                            <option value="applyComplete" data-no="{{ $item->no }}" >申请完成</option>
                                            @endif

                                            @if(!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 14))
                                            <option value="cancelComplete" data-no="{{ $item->no }}" >取消验收</option>
                                            @endif
                                            @if(!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 13))
                                            <option value="abnormal" data-no="{{ $item->no }}" >异常</option>
                                            @endif
                                            @if(!auth()->user()->getPrimaryUserId() == $item->creator_primary_user_id && ($item->status == 17))
                                            <option value="cancelAbnormal" data-no="{{ $item->no }}" >取消异常</option>
                                            @endif
                                        </select>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse

                    </tbody>
                </table>
            </div>
        </div>
        <!-- 固定列 end -->
    </div>

    {{ $orders->links() }}

    <div class="consult" style="display: none; padding:  0 20px">
        <div class="layui-tab-content">
            <span style="color:red;margin-right:15px;">双方友好协商撤单，若有分歧可以在订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。<br/></span>
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div style="width: 80%" id="info">
                    <div class="layui-form-item">
                        <label class="layui-form-label">*我愿意支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="amount" lay-verify="required|number" value="" autocomplete="off"
                                   placeholder="请输入代练费" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">我已支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="order_amount" id="order_amount" lay-verify="" value=""
                                   autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">*需要对方赔付保证金</label>
                        <div class="layui-input-block">
                            <input type="text" name="deposit" lay-verify="required|number" value="" autocomplete="off"
                                   placeholder="请输入保证金" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已预付安全保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="safe" id="safe" lay-verify="" value="" autocomplete="off"
                                   placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已预付效率保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="effect" id="effect" lay-verify="" value="" autocomplete="off"
                                   placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">撤销理由</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入撤销理由" name="revoke_message" lay-verify="required"
                                      class="layui-textarea" style="width:400px"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="consult">立即提交</button>
                            <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="complain" style="display: none; padding: 10px 10px 0 10px">
        <div class="layui-tab-content">
            <form class="layui-form">
                <input type="hidden" id="order_no" name="order_no">
                <div>
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin:0px">
                            <textarea placeholder="请输入申请仲裁理由" name="complain_message" lay-verify="required"
                                      class="layui-textarea"
                                      style="width:90%;margin:auto;height:150px !important;"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">

                        <div class="layui-input-block" style="margin: 0 auto;text-align: center;">
                            <button class="layui-btn layui-btn-normal" id="submit" lay-submit lay-filter="complain">确认
                            </button>
                            <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{--<div class="send-message" style="display: none;padding: 20px">--}}
        {{--<form class="layui-form" action="" id="goods-add-form">--}}
            {{--<input type="hidden" name="type" value="">--}}
            {{--<div style="height: 20px;line-height: 20px;color:red;padding-bottom: 10px">短信费：0.1元/条</div>--}}
            {{--<div class="layui-form-item">--}}
                {{--<select name="service_id" lay-verify="" lay-filter="chose-sms-template">--}}
                    {{--<li value="">选择模版</li>--}}
                    {{--@forelse($smsTemplate as $item)--}}
                        {{--<li value="{{ $item->contents }}" data-template="">{{ $item->name }}</li>--}}
                    {{--@empty--}}
                    {{--@endforelse--}}
                {{--</select>--}}
            {{--</div>--}}
            {{--<div class="layui-form-item layui-form-text">--}}
                {{--<textarea placeholder="请输入要发送的内容" name="contents" lay-verify="required" class="layui-textarea"--}}
                          {{--style="margin:auto;height:150px !important;"></textarea>--}}
            {{--</div>--}}

            {{--<div class="layui-form-item">--}}
                {{--<button class="layui-btn layui-btn-normal" lay-submit lay-filter="confirm-send-sms">确认</button>--}}
                {{--<span cancel class="layui-btn  layui-btn-normal cancel">取消</span>--}}
            {{--</div>--}}
        {{--</form>--}}
    {{--</div>--}}
@endsection

<!--START 底部-->
@section('js')
    <script src="/frontend/js/fixed-table.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form,
                    layer = layui.layer,
                    element = layui.element,
                    laydate = layui.laydate,
                    table = layui.table;
            // 当前tab 所在位置
            var status = 0;
            var urgentOrder = 0;

            laydate.render({elem: '#start-date'});
            laydate.render({elem: '#end-date'});

            $('.cancel').click(function () {
                layer.closeAll();
            });

            var userId = "{{ Auth::id() }}";

            // 对订单操作
            form.on('select(order-operation)', function (data) {
                var orderNo = $(data.elem).find("option:selected").attr("data-no");
                var orderAmount = $(data.elem).find("option:selected").attr("data-amount");
                var orderSafe = $(data.elem).find("option:selected").attr("data-safe");
                var orderEffect = $(data.elem).find("option:selected").attr("data-effect");

                if (!orderAmount) {
                    orderAmount = 0;
                }
                if (!orderSafe) {
                    orderSafe = 0;
                }
                if (!orderEffect) {
                    orderEffect = 0;
                }
                $('#order_amount').val(orderAmount);
                $('#safe').val(orderSafe);
                $('#effect').val(orderEffect);

                if (!data.value) {
                    return false;
                }
                if (data.value == 'detail') {
                    window.open('{{ route('frontend.workbench.leveling.detail') }}?no='  + orderNo);
                }
                // 留言
                if (data.value == 'message') {
                    window.open('{{ route('frontend.workbench.leveling.detail') }}' + '?no='  + orderNo + '&tab=1');
                }
                // 操作记录
                if (data.value == 'operationRecord') {
                    window.open('{{ route('frontend.workbench.leveling.detail') }}' + '?no='  + orderNo + '&tab=2');
                }
                // 重发
                if (data.value == 'repeat') {
                    var no = $(data.elem).find("option:selected").attr("data-no");
                    window.open('{{ route('frontend.workbench.leveling.repeat') }}' + '/'  + orderNo);
                }
                // 联系旺旺
                if (data.value == 'wangWang') {
                    var wangWang = $(data.elem).find("option:selected").attr("data-wang-wang");
                    window.open('http://www.taobao.com/webww/ww.php?ver=3&touid=' + wangWang  +  '&siteid=cntaobao&status=1&charset=utf-8" class="btn btn-save buyer" target="_blank" title="' + wangWang);
                    return false;
                }
                if (data.value == 'sendSms') {
                    $('.send-message  .layui-form').append('<input type="hidden" name="no" value="' + orderNo + '"/>');
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '发送短信',
                        area: ['500px'],
                        content: $('.send-message')
                    });
                    return false
                }
                if (data.value == 'revoke') {
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '申请撤销',
                        area: ['650px', '550px'],
                        content: $('.consult')
                    });
                    form.on('submit(consult)', function(data){
                        $.post("{{ route('frontend.workbench.leveling.consult') }}", {
                            orderNo:orderNo,
                            data:data.field
                        }, function (result) {
                            if (result.status == 1) {
                                layer.closeAll();
                                layer.alert(result.message);
                            } else {
                                layer.alert(result.message);
                            }
                            reload();
                        });
                        return false;
                    });

                } else if (data.value == 'applyArbitration') {
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '申请仲裁',
                        area: ['500px', '280px'],
                        content: $('.complain')
                    });
                    $('#order_no').val(orderNo);
                    form.on('submit(complain)', function(data){
                        $.post("{{ route('frontend.workbench.leveling.complain') }}", {
                            orderNo:orderNo,
                            data:data.field
                        }, function (result) {
                            if (result.status == 1) {
                                layer.alert(result.message);
                            } else {
                                layer.alert(result.message);
                            }
                            reload();
                        });
                        layer.closeAll();
                        return false;
                    });

                } else if (data.value == 'delete') {
                    layer.confirm('确认删除吗？', {icon: 3, title:'提示'}, function(index){
                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo:orderNo,
                            userId:userId,
                            keyWord:data.value
                        }, function (result) {
                            if (result.status == 1) {
                                layer.alert(result.message);
                            } else {
                                layer.alert(result.message);
                            }
                            reload();
                        });

                        layer.close(index);
                    });
                } else if(data.value == 'complete') {
                    layer.confirm("确定完成订单？<br/> <input type='checkbox'> 同时提交淘宝/天猫订单发货", {icon: 3, title:'提示'}, function(index){
                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo:orderNo,
                            userId:userId,
                            keyWord:data.value
                        }, function (result) {
                            if (result.status == 1) {
                                layer.alert(result.message);
                            } else {
                                layer.alert(result.message);
                            }
                            reload();
                        });
                        layer.close(index);
                    });
                } else if(data.value ==  'agreeRevoke') {
                    layer.confirm('确定同意撤销吗？', {icon: 3, title:'提示'}, function(index){
                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo:orderNo,
                            userId:userId,
                            keyWord:data.value
                        }, function (result) {
                            if (result.status == 1) {
                                layer.alert(result.message, function () {
                                    layer.closeAll();
                                });
                            } else {
                                layer.alert(result.message, function () {
                                    layer.closeAll();
                                });
                            }
                            reload();
                        });
                        layer.close(index);
                    });
                }  else {
                    $.post("{{ route('frontend.workbench.leveling.status') }}", {
                        orderNo:orderNo,
                        userId:userId,
                        keyWord:data.value
                    }, function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message, function () {
                                layer.closeAll();
                            });

                        } else {
                            layer.alert(result.message, function () {
                                layer.closeAll();
                            });
                        }
                        reload();
                    });
                }
            });

            // 导出
            form.on('submit(export)', function (data) {
                var fields = data.field;
                var datas = '?no=' + fields.no + '&source_order_no=' + fields.source_order_no + '&gameId=' + fields.game_id + '&wangWang=' + fields.wang_wang + '&startDate=' + fields.start_date + '&endDate=' + fields.end_date + '&status=' + status + '&urgentOrder=' + urgentOrder;
                window.location.href = "{{ route('frontend.workbench.leveling.excel') }}" + datas;
            });

            var hasData = "{{ session('message') }}";
            if (hasData) {
                layer.alert(hasData, function () {
                    layer.closeAll();
                });
            }

            function setDateTableHeight() {
                var windowsH = $(window).height() - 350;
                var contentH = $('.data-table-content').height();
                var h = 0;
                if (contentH > windowsH) {
                    h = windowsH;
                } else {
                    h = contentH;
                }
                $(".data-table-content").css('height', h + 'px');
                $(".data-table-fixed-content").css('height', h - 20 + 'px');
                $(".fixed-table-box").fixedTable();
                layui.from.render();
            }
            $(window).resize(function() {
                setDateTableHeight();
            });
            $(document).ready(function () {
                setDateTableHeight();
            });
        });
    </script>
@endsection
