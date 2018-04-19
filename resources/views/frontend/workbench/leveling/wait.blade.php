@extends('frontend.layouts.app')

@section('title', '工作台 - 代练 - 待发订单')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-laypage-skip input {
            height: 27px !important;
        }
        .laytable-cell-1-0, .laytable-cell-1-5, .laytable-cell-1-7{
            height: 40px !important;
        }
        th:nth-child(1) > div, th:nth-child(6) > div, th:nth-child(8) > div {
            line-height: 40px !important;
        }
        .laytable-cell-1-13{
            height: 40px !important;
            line-height: 40px !important;
        }
        .layui-laypage-em {
            background-color: #1E9FFF !important;
        }
        .layui-form-select .layui-input {
            padding-right:0 !important;
        }
        .layui-table-cell {
            overflow: inherit;
        }
        .layui-form-item .layui-inline {
            margin-bottom: 5px;
            margin-right: 5px;
        }
        .layui-form-mid {
            margin-right: 4px;
        }
        #info .layui-form-item .layui-input-block{
            margin-left: 200px;
        }
        #info .layui-form-item .layui-form-label{
           width: 160px;
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
                <label class="layui-form-mid">订单号：</label>
                <div class="layui-input-inline">
                    <input type="text" name="tid" autocomplete="off" class="layui-input" value="{{ $tid }}">
                </div>
            </div>

            <div class="layui-inline">
                <label class="layui-form-mid">旺旺号：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="buyer_nick" autocomplete="off" class="layui-input" value="{{ $buyerNick }}">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">下单时间：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="start_date" autocomplete="off" class="layui-input" id="start-date" value="{{ $startDate }}">
                </div>
                <div class="layui-input-inline" style="">
                    <input type="text" name="end_date" autocomplete="off" class="layui-input fsDate" id="end-date" value=" {{ $endDate }}">
                </div>
                <button class="layui-btn layui-btn-normal " type="submit" function="query" lay-submit="" lay-filter="search">查询</button>
            </div>
        </div>

    </form>

    <div class="layui-tab layui-tab-brief" lay-filter="order">
        <ul class="layui-tab-title">
            <li class="@if($status == 99) layui-this  @endif" lay-id="99">全部 @if($totalCount)<span class="layui-badge">{{ $totalCount }}</span>@endif</li>
            <li class="@if($status == 0) layui-this  @endif" lay-id="0">待处理 @if($unDisposeCount)<span class="layui-badge">{{ $unDisposeCount }}</span>@endif</li>
            <li class="@if($status == 1) layui-this  @endif" lay-id="1">已处理 @if($disposeCount)<span class="layui-badge">{{ $disposeCount }}</span>@endif</li>
            <li class="@if($status == 2) layui-this  @endif" lay-id="2">已隐藏 @if($hideCount)<span class="layui-badge">{{ $hideCount }}</span>@endif</li>
        </ul>
        <div class="layui-tab-content">
            <table class="layui-table" lay-size="sm">
                <thead>
                <tr>
                    <th>店铺</th>
                    <th>订单号</th>
                    <th>绑定游戏</th>
                    <th>买家旺旺</th>
                    <th>购买单价</th>
                    <th>购买数量</th>
                    <th>实付金额</th>
                    <th>下单时间</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $item)
                    <tr data-no="{{ $item->tid }}">
                        <td>{{ $item->seller_nick }}</td>
                        <td>{{ $item->tid }}</td>
                        <td>{{ $item->game_name }}</td>
                        <td><a style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid={{ $item->buyer_nick }}&siteid=cntaobao&status=1&charset=utf-8"  target="_blank" title="{{ $item->buyer_nick }}"><img
                                        src="/frontend/images/ww.gif" alt="" width="20px"> {{ $item->buyer_nick }}</a></td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->num }}</td>
                        <td>{{ $item->payment }}</td>
                        <td>{{ $item->created }}</td>
                        <td>
                            @if($item->handle_status == 0)
                                <a href="{{ route('frontend.workbench.leveling.create', ['tid' => $item->tid, 'game_id' => $item->game_id]) }}" class="layui-btn layui-btn-normal">发布</a>
                                <button href="{{ route('frontend.workbench.leveling.wait-update', ['id' => $item->id, 'status' => 2]) }}" class="layui-btn update">隐藏</button>
                            @elseif($item->handle_status == 2)
                                <button href="{{ route('frontend.workbench.leveling.wait-update', ['id' => $item->id, 'status' => 0]) }}" class="layui-btn update">显示</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11">没有数据</td></tr>
                @endforelse
                </tbody>
            </table>
            {!! $orders->appends(['tid' => $tid, 'buyer_nick' => $buyerNick, 'start_date' => $startDate, 'status' => $status ])->render() !!}
        </div>
    </div>

@endsection

<!--START 底部-->
@section('js')
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form,
                layer = layui.layer,
                layTpl = layui.laytpl,
                element = layui.element,
                laydate = layui.laydate;

            laydate.render({elem: '#start-date'});
            laydate.render({elem: '#end-date'});

            element.on('tab(order)', function(){
                window.location.href='{{ route('frontend.workbench.leveling.wait') }}?status=' + this.getAttribute('lay-id');
            });

            $('.update').on('click', function () {
                $.post($(this).attr('href'), {ad:1}, function () {
                    window.location.href='{{ route('frontend.workbench.leveling.wait') }}?status=' + '{{ $status }}';
                });
            });
        });
    </script>
@endsection