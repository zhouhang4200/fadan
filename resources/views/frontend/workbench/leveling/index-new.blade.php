@extends('frontend.layouts.app')

@section('title', '工作台 - 代练')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.4/css/fixedColumns.dataTables.min.css">
    <style>
        .layui-form-item .layui-inline {
            margin-bottom: 5px;
            margin-right: 5px;
        }
        .layui-form-mid {
            margin-right: 4px;
        }
        .layui-tab-title li {
            min-width: 60px;
            padding: 0 15px;
        }
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-button {
            width: 0;
            height: 0;
        }
        ::-webkit-scrollbar-button:start:decrement,
        ::-webkit-scrollbar-button:end:increment {
            display: block;
        }
        ::-webkit-scrollbar-button:vertical:start:increment,
        ::-webkit-scrollbar-button:vertical:end:decrement {
            display: none;
        }
        ::-webkit-scrollbar-track:vertical,
        ::-webkit-scrollbar-track:horizontal,
        ::-webkit-scrollbar-thumb:vertical,
        ::-webkit-scrollbar-thumb:horizontal,
        ::-webkit-scrollbar-track:vertical,
        ::-webkit-scrollbar-track:horizontal,
        ::-webkit-scrollbar-thumb:vertical,
        ::-webkit-scrollbar-thumb:horizontal {
            border-color: transparent;
            border-style: solid;
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
        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, .4);
            -webkit-box-shadow: inset 1px 1px 1px rgba(0, 0, 0, .25);
        }
        ::-webkit-scrollbar-thumb:active {
            background-color: rgba(0, 0, 0, .5);
            -webkit-box-shadow: inset 1px 1px 3px rgba(0, 0, 0, .35);
        }
        ::-webkit-scrollbar-track:vertical,
        ::-webkit-scrollbar-track:horizontal,
        ::-webkit-scrollbar-thumb:vertical,
        ::-webkit-scrollbar-thumb:horizontal {
            border-width: 0;
        }
        ::-webkit-scrollbar-track:hover {
            background-color: rgba(0, 0, 0, .05);
            -webkit-box-shadow: inset 1px 0 0 rgba(0, 0, 0, .1);
        }
        ::-webkit-scrollbar-track:active {
            background-color: rgba(0, 0, 0, .05);
            -webkit-box-shadow: inset 1px 0 0 rgba(0, 0, 0, .14), inset -1px -1px 0 rgba(0, 0, 0, .07);
        }
        .scrollbar-hover::-webkit-scrollbar,
        .scrollbar-hover::-webkit-scrollbar-button,
        .scrollbar-hover::-webkit-scrollbar-track,
        .scrollbar-hover::-webkit-scrollbar-thumb {
            visibility: hidden;
        }
        .scrollbar-hover:hover::-webkit-scrollbar,
        .scrollbar-hover:hover::-webkit-scrollbar-button,
        .scrollbar-hover:hover::-webkit-scrollbar-track,
        .scrollbar-hover:hover::-webkit-scrollbar-thumb {
            visibility: visible;
        }
        /*下拉菜单*/
        table.dataTable thead th, table.dataTable thead td {
            padding: 10px 18px;
            border: 1px solid #ddd;
            background: #e6e6e6;
        }
        .dropup,
        .dropdown {
            position: relative;
        }
        .dropdown-toggle:focus {
            outline: 0;
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 100px;
            padding: 5px 0;
            margin: 2px 0 0;
            list-style: none;
            font-size: 14px;
            text-align: left;
            background-color: #fff;
            border: 1px solid #ccc;
            border: 1px solid rgba(0, 0, 0, 0.15);
            -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
            background-clip: padding-box;
        }
        .dropdown-menu.pull-right {
            right: 0;
            left: auto;
        }
        .dropdown-menu .divider {
            height: 1px;
            margin: 9px 0;
            overflow: hidden;
            background-color: #e5e5e5;
        }
        .dropdown-menu > li > a {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: normal;
            line-height: 1.42857143;
            color: #333333;
            white-space: nowrap;
        }
        .dropdown-menu > li > a:hover,
        .dropdown-menu > li > a:focus {
            text-decoration: none;
            color: #262626;
            background-color: #f5f5f5;
        }
        .dropdown-menu > .active > a,
        .dropdown-menu > .active > a:hover,
        .dropdown-menu > .active > a:focus {
            color: #fff;
            text-decoration: none;
            outline: 0;
            background-color: #337ab7;
        }
        .dropdown-menu > .disabled > a,
        .dropdown-menu > .disabled > a:hover,
        .dropdown-menu > .disabled > a:focus {
            color: #777777;
        }
        .dropdown-menu > .disabled > a:hover,
        .dropdown-menu > .disabled > a:focus {
            text-decoration: none;
            background-color: transparent;
            background-image: none;
            filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
            cursor: not-allowed;
        }
        .open > .dropdown-menu {
            display: block;
        }
        .open > a {
            outline: 0;
        }
        .dropdown-header {
            display: block;
            padding: 3px 20px;
            font-size: 12px;
            line-height: 1.42857143;
            color: #777777;
            white-space: nowrap;
        }
        .dropdown-backdrop {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
            z-index: 990;
        }
        .pull-right > .dropdown-menu {
            right: 0;
            left: auto;
        }
        .dropup .caret,
        .navbar-fixed-bottom .dropdown .caret {
            border-top: 0;
            border-bottom: 4px dashed;
            border-bottom: 4px solid \9;
            content: "";
        }
        .dropup .dropdown-menu,
        .navbar-fixed-bottom .dropdown .dropdown-menu {
            top: auto;
            bottom: 100%;
            margin-bottom: 2px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">平台单号：</label>
                <div class="layui-input-inline">
                    <input type="text" name="no" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">外部单号：</label>
                <div class="layui-input-inline">
                    <input type="text" name="source_order_no" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">代练游戏：</label>
                <div class="layui-input-inline">
                    <select name="game_id" lay-search="">
                        <li value="">请选择游戏</li>
                        @foreach($game as  $key => $value)
                            <li value="{{ $key }}">{{ $value }}</li>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">号主旺旺：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="wang_wang" autocomplete="off" class="layui-input">
                </div>
            </div>

        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">发单客服：</label>
                <div class="layui-input-inline" style="">
                    <select name="customer_service_name" lay-search="">
                        <li value="">请选择或输入</li>
                        @forelse($employee as $item)
                            <li value="{{ $item->username }}">{{ $item->username }}</li>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">订单标签：</label>
                <div class="layui-input-inline" style="">
                    <select name="label">
                        <li value="">全部</li>
                        @foreach ($tags as $tag)
                            <li value="{{ $tag }}">{{ $tag }}</li>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">发布时间：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="start_date" autocomplete="off" class="layui-input" id="start-date" >
                </div>
                <div class="layui-input-inline" style="">
                    <input type="text" name="end_date" autocomplete="off" class="layui-input fsDate" id="end-date">
                </div>
                <button class="layui-btn layui-btn-normal " type="button" function="query" lay-submit="" lay-filter="search">查询</button>
                <button class="layui-btn layui-btn-normal " type="button" function="query" lay-submit="" lay-filter="export">导出</button>

            </div>
        </div>
    </form>

    <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="0">全部 <span  class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span></li>
            <li class="" lay-id="1">未接单</li>
            <li class="" lay-id="13">代练中</li>
            <li class="" lay-id="14">待验收
                <span class="layui-badge layui-bg-blue quantity-14 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 14, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 14, 3) }}</span>
            </li>
            <li class="" lay-id="15">撤销中
                <span class="layui-badge layui-bg-blue quantity-15 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 15, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 15, 3) }}</span>
            </li>
            <li class="" lay-id="16">仲裁中
                <span class="layui-badge layui-bg-blue quantity-16 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 16, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 16, 3) }}</span>
            </li>
            <li class="" lay-id="17">异常
                <span class="layui-badge layui-bg-blue quantity-17 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 17, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 17, 3) }}</span>
            </li>
            <li class="" lay-id="18">锁定
                <span class="layui-badge layui-bg-blue quantity-18 @if(orderStatusCount(auth()->user()->getPrimaryUserId(), 18, 3) == 0) layui-hide  @endif">{{ orderStatusCount(auth()->user()->getPrimaryUserId(), 18, 3) }}</span>
            </li>
            <li class="" lay-id="19">已撤销</li>
            <li class="" lay-id="20">已结算</li>
            <li class="" lay-id="21">已仲裁</li>
            <li class="" lay-id="22">已下架</li>
            <li class="" lay-id="23">强制撤销</li>
        </ul>
        <br>
        <table class="row-border stripe  cell-border order-column" id="tableDataGridExample">
            <thead>
            <tr>
                <th>订单号</th>
                <th width="65px">号主旺旺</th>
                <th width="100">客服备注</th>
                <th>代练标题</th>
                <th width="90">游戏/区/服</th>
                <th width="90">账号/密码</th>
                <th width="50">角色名称</th>
                <th width="50px">订单状态</th>
                <th>代练价格</th>
                <th>效率保证金</th>
                <th>安全保证金</th>
                <th>发单时间</th>
                <th>接单时间</th>
                <th>代练时间</th>
                <th>剩余时间</th>
                <th>打手呢称</th>
                <th>打手电话</th>
                <th>号主电话</th>
                <th>来源价格</th>
                <th>支付金额</th>
                <th>获得金额</th>
                <th >手续费</th>
                <th>利润</th>
                <th>发单客服</th>
                <th width="100px">操作</th>
            </tr>
            </thead>
            <tbody>
            @php $i= 1; $total  = $orders->total();   @endphp
            @forelse($orders as $item)
                @php $detail = $item->detail->pluck('field_value', 'field_name')->toArray();  @endphp
                <tr>
                    <td>
                        天猫：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no={{ $item['no'] }}">{{ $detail['source_order_no'] or $item->no  }}</a> <br/>
                        @if(isset($detail['third']) && $detail['third'])
                            {{ config('parent.platform')[$detail['third']] }}：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no={{ $item['no'] }}"> {{ $detail['third_order_no'] }} </a>
                        @endif

                    </td>
                    <td>
                        @if(isset($detail['client_wang_wang']))
                        <a href="http://www.taobao.com/webww/ww.php?ver=3&touid={{ $detail['client_wang_wang'] }}&siteid=cntaobao&status=1&charset=utf-8" class="btn btn-save buyer" target="_blank">联系旺旺号<img src="/frontend/images/ww.gif"></a></li>
                        @endif
                    </td>
                    <td>{{ $detail['customer_service_remark'] or '' }}</td>
                    <td>{{ $detail['game_leveling_title'] or '' }}</td>
                    <td>{{ $item->game_name }} <br/> {{ isset($detail['region']) ?  $detail['region'] . ' / ' . $detail['serve'] : ''  }}</td>
                    <td>{{ $detail['account'] or '' }} <br/> {{ $detail['password'] or '' }}</td>
                    <td>{{ $detail['role'] or '' }}</td>
                    <td>{{ isset(config('order.status_leveling')[$item->status]) ? config('order.status_leveling')[$item->status] : '' }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>{{ $item->no }}</td>
                    <td>
                        {{ $item->no }}
                    </td>

                    <td>
                        <div class="dropdown @if($i >= $total - 4) dropup @endif">
                            <button style="width:100px;" type="button" class="layui-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                订单操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('frontend.workbench.leveling.detail', ['no' => $item->no]) }}">详情</a></li>

                                @if (!$item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && $item->status == 1)
                                <li><a  class="operation" data-operation="receive" data-no="{{ $item->no }}" href="#">接单</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && $item->status == 22)
                                <li><a  class="operation" data-operation="onSale" data-no="{{ $item->no }}" href="#">上架</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && $item->status == 1)
                                <li><a  class="operation" data-operation="offSale" data-no="{{ $item->no }}"  href="#">下架</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && ($item->status == 14 || $item->status == 15 || $item->status == 16 || $item->status == 17 || $item->status == 18 || $item->status == 19 || $item->status == 20 || $item->status == 21))
                                <li><a href="{{ route('frontend.workbench.leveling.repeat', ['no' => $item->no]) }}">重发</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && ($item->status == 13 || $item->status == 14 || $item->status == 17))
                                <li><a  class="operation" data-operation="lock" data-no="{{ $item->no }}"  href="#">锁定</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && $item->status == 18)
                                <li><a  class="operation" data-operation="cancelLock" data-no="{{ $item->no }}"  href="#">取消锁定</a></li>
                                @endif

                                {{--@if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId())--}}
                                {{--@if ($item->leveling_consult->consult == 1 && $item->status == 15)--}}
                                {{--<li value="cancelRevoke" data-no="{{ $item->no }}" >取消撤销</li>--}}
                                {{--@elseif($item->leveling_consult->consult == 2 && ($item->status == 15 || $item->status == 16))--}}
                                {{--<li value="agreeRevoke" data-no="{{ $item->no }}" >同意撤销</li>--}}
                                {{--<li value="refuseRevoke" data-no="{{ $item->no }}" >不同意撤销</li>--}}
                                {{--@endif--}}
                                {{----}}
                                {{--@if($item->leveling_consult->consult == 2 && $item->status == 15)--}}
                                {{--<li value="cancelRevoke" data-no="{{ $item->no }}" >取消撤销</li>--}}
                                {{--@elseif($item->leveling_consult->consult == 1 && ($item->status == 15 || $item->status == 16))--}}
                                {{--<li value="agreeRevoke" data-no="{{ $item->no }}" >同意撤销</li>--}}
                                {{--<li value="refuseRevoke" data-no="{{ $item->no }}" >不同意撤销</li>--}}
                                {{--@endif--}}
                                {{--@endif--}}

                                @if ($item->status == 13 || $item->status == 14 || $item->status == 17 || $item->status == 18)
                                <li><a  class="operation" data-operation="revoke" data-no="{{ $item->no }}"  href="#">撤销</a></li>
                                @endif

                                @if ($item->status == 13 || $item->status == 14 || $item->status == 15)
                                <li><a  class="operation" data-operation="applyArbitration" data-no="{{ $item->no }}"  href="">申请仲裁</a></li>
                                @endif

                                {{--@if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId())--}}
                                    {{--@if($item->leveling_consult->complain == 1 && $item->status == 16)--}}
                                    {{--<li value="cancelArbitration" data-no="{{ $item->no }}" >取消仲裁</li>--}}
                                    {{--@endif--}}
                                   {{----}}
                                    {{--@if($item->leveling_consult->complain == 2 && $item->status == 16)--}}
                                    {{--<li value="cancelArbitration" data-no="{{ $item->no }}" >取消仲裁</li>--}}
                                    {{--@endif--}}
                                {{--@endif--}}

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && $item->status == 14)
                                <li><a  class="operation" data-operation="complete" data-no="{{ $item->no }}"   href="#">完成</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId())
                                <li><a  href="{{ route('frontend.workbench.leveling.detail', ['no' => $item->no, 'tab' => 1]) }}">留言</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId())
                                <li><a href="{{ route('frontend.workbench.leveling.detail', ['no' => $item->no, 'tab' => 2]) }}">操作记录</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && isset($detail['client_wang_wang']))
                                <li><a href="http://www.taobao.com/webww/ww.php?ver=3&touid={{ $detail['client_wang_wang'] }}&siteid=cntaobao&status=1&charset=utf-8" class="btn btn-save buyer" target="_blank">联系旺旺号</a></li>
                                @endif

                                @if ($item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && ($item->status == 1 || $item->status == 22))
                                <li><a  class="operation" data-operation="delete" data-no="{{ $item->no }}"  href="">撤单</a></li>
                                @endif

                                @if (!$item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && ($item->status == 13))
                                <li><a  class="operation" data-operation="applyComplete" data-no="{{ $item->no }}" href="">申请完成</a></li>
                                @endif

                                @if (!$item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && ($item->status == 14))
                                <li><a  class="operation" data-operation="cancelComplete" data-no="{{ $item->no }}"  href="">取消验收</a></li>
                                @endif

                                @if (!$item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && ($item->status == 13))
                                <li><a  class="operation" data-operation="abnormal" data-no="{{ $item->no }}"  href="#">异常</a></li>
                                @endif

                                @if (!$item->creator_primary_user_id == Auth::user()->getPrimaryUserId() && ($item->status == 17))
                                <li><a  class="operation" data-operation="cancelAbnormal" data-no="{{ $item->no }}"  href="#"></a>取消异常</li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @php $i++  @endphp
            @empty

            @endforelse
            </tbody>
        </table>
    </div>

    <div class="consult" style="display: none; padding:  0 20px">
        <div class="layui-tab-content">
            <span style="color:red;margin-right:15px;">双方友好协商撤单，若有分歧可以在订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。<br/></span>
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div style="width: 80%" id="info">
                    <div class="layui-form-item">
                        <label class="layui-form-label">*我愿意支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="amount" lay-verify="required|number" value="" autocomplete="off" placeholder="请输入代练费" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">我已支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="order_amount" id="order_amount" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">*需要对方赔付保证金</label>
                        <div class="layui-input-block">
                            <input type="text" name="deposit" lay-verify="required|number" value="" autocomplete="off" placeholder="请输入保证金" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已预付安全保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="safe" id="safe" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已预付效率保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="effect" id="effect" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">撤销理由</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入撤销理由" name="revoke_message" lay-verify="required" class="layui-textarea" style="width:400px"></textarea>
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
                            <textarea placeholder="请输入申请仲裁理由" name="complain_message" lay-verify="required" class="layui-textarea" style="width:90%;margin:auto;height:150px !important;"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">

                        <div class="layui-input-block" style="margin: 0 auto;text-align: center;">
                            <button class="layui-btn layui-btn-normal" id="submit" lay-submit lay-filter="complain">确认</button>
                            <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="send-message" style="display: none;padding: 20px">
        <form class="layui-form" action="" id="goods-add-form">
            <input type="hidden" name="type" value="">
            <div style="height: 20px;line-height: 20px;color:red;padding-bottom: 10px">短信费：0.1元/条</div>
            <div class="layui-form-item">
                <select name="service_id" lay-verify="" lay-filter="chose-sms-template">
                    <li value="">选择模版</li>
                    @forelse($smsTemplate as $item)
                        <li value="{{ $item->contents }}" data-template="">{{ $item->name }}</li>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="layui-form-item layui-form-text">
                <textarea placeholder="请输入要发送的内容" name="contents" lay-verify="required" class="layui-textarea" style="margin:auto;height:150px !important;"></textarea>
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="confirm-send-sms">确认</button>
                <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
            </div>
        </form>
    </div>

@endsection

<!--START 底部-->
@section('js')
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            var h = $("body").height() - 800 + 'px';
            var table = $('#tableDataGridExample').DataTable({
                scrollY: h,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                ordering: false,
                info: false,
                searching: false,
                fixedColumns: {
                    leftColumns: 1,
                    rightColumns: 1
                }
            });
        });


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

            $('.cancel').click(function(){
                layer.closeAll();
            });

            var userId = "{{ Auth::id() }}";

            // 对订单操作
            $('.operation').click(function () {
                var operation = $(this).data('operation');
                var orderNo = $(this).data("data-no");
                var orderAmount = $(this).data("data-amount");
                var orderSafe = $(this).data("data-safe");
                var orderEffect = $(this).data("data-effect");

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

                if (!operation) {
                    return false;
                }

                if (operation == 'revoke') {
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

                } else if (operation == 'applyArbitration') {
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

                } else if (operation == 'delete') {
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
                } else if(operation == 'complete') {
                    layer.confirm('确定完成订单？', {icon: 3, title:'提示'}, function(index){
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
                } else if(operation ==  'agreeRevoke') {
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
                } else {
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
                return false;
            });

            form.on('select(order-operation)', function (data) {

            });

            // 导出
            form.on('submit(export)', function (data) {
                var fields = data.field;
                var datas = '?no=' + fields.no+'&source_order_no='+fields.source_order_no+'&gameId='+fields.game_id+'&wangWang='+fields.wang_wang+'&startDate='+fields.start_date+'&endDate='+fields.end_date+'&status='+status+'&urgentOrder='+urgentOrder;
                window.location.href="{{ route('frontend.workbench.leveling.excel') }}"+datas;
            });
            
            var hasData = "{{ session('message') }}";
            if (hasData) {
                layer.alert(hasData, function () {
                    layer.closeAll();
                });
            }
        });
    </script>
@endsection
