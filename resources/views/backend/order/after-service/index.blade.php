@extends('backend.layouts.main')

@section('title', ' | 售后订单')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>售后订单</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="form-group col-xs-1">
                                <select class="layui-input" name="status" lay-search="">
                                    <option value="0">请选择状态</option>
                                    @foreach(config('order.after_service') as $key => $value)
                                        <option value="{{ $key }}" @if($key == $status) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-2">
                                <input type="text" class="layui-input" name="order_no"  placeholder="平台订单号" value="{{ $orderNo }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <input type="text" class="layui-input" name="order_creator_user_id"  placeholder="发单商户ID" value="{{ $orderCreatorUserId }}">
                            </div>

                            <div class="form-group col-xs-2">
                                <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
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
                    <table class="layui-table layui-form" lay-size="sm">
                        <thead>
                        <tr>
                            <th>平台订单号</th>
                            <th>发单方</th>
                            <th>接单方</th>
                            <th>订单总金额</th>
                            <th>申请退款金额</th>
                            <th>申请人</th>
                            <th>申请说明</th>
                            <th>申请时间</th>
                            <th>审核人</th>
                            <th>审核说明</th>
                            <th>审核时间</th>
                            <th>确认人</th>
                            <th>确认时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $item)
                            <tr>
                                <td>{{ $item->order_no }}</td>
                                <td>{{ $item->order_creator_user_id }}</td>
                                <td>{{ $item->order_gainer_user_id }}</td>
                                <td>{{ $item->original_amount }}</td>
                                <td>{{ $item->refund_amount }}</td>
                                <td>{{ $item->applyUser->name }}</td>
                                <td>{{ $item->apply_remark }}</td>
                                <td>{{ $item->apply_date }}</td>
                                <td>{{ $item->auditing_admin_user_id !=0 ? $item->auditingUser->name : ''  }}</td>
                                <td>{{ $item->auditing_remark }}</td>
                                <td>{{ $item->auditing_date }}</td>
                                <td>{{ $item->confirm_admin_user_id !=0 ? $item->confirmUser->name: '' }}</td>
                                <td>{{ $item->confirm_date }}</td>
                                <td>{{ config('order.after_service')[$item->status] }}</td>
                                <td>
                                    @if(Auth::user()->can('order.after-service.auditing') &&  $item->status == 1)
                                        <button class="layui-btn layui-btn-normal layui-btn-mini" data-no="{{ $item->order_no }}" lay-submit="" lay-filter="auditing">审核</button>
                                    @endif
                                    @if(Auth::user()->can('order.after-service.confirm') &&  $item->status == 2)
                                        <button class="layui-btn layui-btn-normal layui-btn-mini" data-no="{{ $item->order_no }}" lay-submit="" lay-filter="confrim">确认</button>
                                    @endif
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
                                    'order_no' => $orderNo,
                                    'status' => $status,
                                    'order_creator_user_id' => $orderCreatorUserId,
                                ])->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="auditing-box" style="display: none;width:300px;padding: 20px">
        <form class="layui-form" action="">
            <div class="row">
                <div class="form-group">
                    <div class="layui-input-inline">
                        <input type="radio" name="status" value="2" title="同意" checked>
                        <input type="radio" name="status" value="3" title="拒绝" >
                    </div>
                </div>
                <div class="form-group">
                    <textarea name="remark" placeholder="请输入审核说明" class="layui-textarea" rows="20" lay-verify="required"></textarea>
                </div>
                <div class="form-group">
                    <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="submit-auditing">确定</button>
                </div>
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

            // 修改游戏
            form.on('submit(auditing)', function (data) {
                $('.auditing-box > .layui-form').append('<input type="hidden" name="no" value="'+ data.elem.getAttribute('data-no')  +'"/>');
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '审核',
                    content: $('.auditing-box')
                });
                return false;
            });
            // 提交审核
            form.on('submit(submit-auditing)', function (data) {
                layer.confirm('您确认该操作吗？', function () {
                   $.post('{{ route('order.after-service.auditing') }}', {no:data.field.no, status:data.field.status, remark:data.field.remark}, function (result) {
                        layer.closeAll();
                        layer.msg(result.message);
                   } ,'json')
                });
                return false;
            });
            // 完成售后请求
            form.on('submit(confirm)', function (data) {
                layer.confirm('您确认该操作吗？', function () {
                    $.post('{{ route('order.after-service.confirm') }}', {no:data.elem.getAttribute('data-no')}, function (result) {
                        layer.closeAll();
                        layer.msg(result.message);
                    } ,'json')
                });
                return false;
            });
        });
    </script>
@endsection