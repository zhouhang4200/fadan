@extends('backend.layouts.main')

@section('title', ' | 订单列表')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>平台订单</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="form-group col-xs-2">
                                <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ $endDate }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <select  name="status"  lay-search="">
                                    <option value="0">订单状态</option>
                                    @foreach(config('order.status') as $key => $value)
                                        <option value="{{ $key }}" @if($key == $status) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-2">
                                <select class="layui-input" name="service_id" lay-search="">
                                    <option value="0">请选择服务</option>
                                    @foreach($services as $key => $value)
                                        <option value="{{ $key }}" @if($key == $serviceId) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-2">
                                <select class="layui-input" name="game_id" lay-search="">
                                    <option value="0">请选择游戏</option>
                                    @foreach($games as $key => $value)
                                        <option value="{{ $key }}" @if($key == $gameId) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-1">
                                <input type="text" class="layui-input" name="creator_primary_user_id"  placeholder="发单用户" value="">
                            </div>
                            <div class="form-group col-xs-1">
                                <input type="text" class="layui-input" name="gainer_primary_user_id"  placeholder="接单用户" value="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-3">
                                <input type="text" class="layui-input" name="no"  placeholder="千手订单号" value="{{ $no }}">
                            </div>
                            <div class="form-group col-xs-3">
                                <input type="text" class="layui-input" name="foreign_order_no"  placeholder="外部订单号" value="{{ $foreignOrderNo }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
                                <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal" >导出</a>
                            </div>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $orders->total() }}　本页显示：{{$orders->count()}}
                        </div>
                        <div class="col-xs-9">
                        </div>
                    </div>
                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th width="19%">订单号</th>
                            <th>来源</th>
                            <th>状态</th>
                            <th>商品</th>
                            <th>服务</th>
                            <th>游戏</th>
                            <th>原单价</th>
                            <th>原总额</th>
                            <th>数量</th>
                            <th>单价</th>
                            <th>总额</th>
                            <th>发单</th>
                            <th>接单</th>
                            <th>下单时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>千手：{{ $order->no }} <br> 外部：{{ $order->foreign_order_no }}</td>
                                <td>{{ config('order.source')[$order->source] }}</td>
                                <td>{{ config('order.status')[$order->status] }}</td>
                                <td>{{ $order->goods_name }}</td>
                                <td>{{ $order->service_name }}</td>
                                <td>{{ $order->game_name }}</td>
                                <td>{{ $order->original_price }}</td>
                                <td>{{ $order->original_amount }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ $order->price }}</td>
                                <td>{{ $order->amount }}</td>
                                <td> {{ $order->creator_primary_user_id }}</td>
                                <td>{{ $order->gainerUser->nickname ?? $order->gainer_primary_user_id }}</td>
                                <td>{{ $order->created_at }}</td>
                                <td>
                                    <a type="button" class="layui-btn layui-btn-normal layui-btn-mini" target="_blank" href="{{ route('order.platform.content', ['id' => $order->id]) }}">详情</a>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $orders->total() }}　本页显示：{{$orders->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $orders->appends([
                                      'status' => $status,
                                      'source' => $source,
                                      'start_date' => $startDate,
                                      'end_date' => $endDate,
                                      'service_id' => $serviceId,
                                      'game_id' => $gameId,
                                      'creator_primary_user_id' => $creatorPrimaryUserId,
                                      'gainer_primary_user_id' => $gainerPrimaryUserId,
                                      'no' => $no,
                                      'foreign_order_no' => $foreignOrderNo,
                                  ])->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="recharge" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">

            <div class="layui-form-item">
                <label class="layui-form-label">ID</label>
                <div class="layui-input-block">
                    <input type="text" name="id" autocomplete="off" class="layui-input layui-disabled" readonly value="">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">商户名</label>
                <div class="layui-input-block">
                    <input type="text" name="name" autocomplete="off" class="layui-input layui-disabled" readonly value="">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">金额</label>
                <div class="layui-input-block">
                    <input type="text" name="amount" autocomplete="off" placeholder="请输入加款金额" class="layui-input" lay-verify="required|number">
                </div>
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="recharge">确定</button>
            </div>
        </form>
    </div>

@endsection

@section('js')
<script>
    //Demo
    layui.use(['form', 'laytpl', 'element', 'laydate'], function(){
        var form = layui.form, layer = layui.layer, laydate = layui.laydate;

        //日期
        laydate.render({
            elem: '#startDate'
        });
        laydate.render({
            elem: '#endDate'
        });

    });
</script>
@endsection