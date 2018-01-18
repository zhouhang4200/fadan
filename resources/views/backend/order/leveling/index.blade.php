@extends('backend.layouts.main')

@section('title', ' | 代练订单报警')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>代练订单报警</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="layui-form-item">
                                <label class="layui-form-label">接单平台</label>
                                <div class="form-group col-xs-2">
                                    <select name="station" lay-filter="">                
                                        <option value="">请输入岗位名称</option>
                                        @forelse(config('order.third') as $key => $thirdItem)
                                            <option value="{{ $key }}" {{ $key == $third ? 'selected' : '' }} >{{ $thirdItem }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <label class="layui-form-label">发布时间</label>
                                <div class="form-group col-xs-1">
                                    <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                                </div>
                                <div class="form-group col-xs-1">
                                    <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ $endDate }}">
                                </div>
                                <div class="form-group col-xs-2">
                                    <button type="submit" class="layui-btn layui-btn-normal ">查询</button>
                                    <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small">导出</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <form class="layui-form" action="">
                    <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>订单号</th>
                                <th>千手状态</th>
                                <th>外部状态</th>
                                <th>接单平台</th>
                                <th>发布时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($paginateOrderNotices as $paginateOrderNotice)
                                <tr>
                                    <td>{{ $paginateOrderNotice->order_no }}</td>
                                    <td>{{ config('order.status_leveling')[$paginateOrderNotice->status] }}</td>
                                    <td>{{ config('order.show91')[$paginateOrderNotice->third_status] }}</td>
                                    <td>{{ config('order.third')[$paginateOrderNotice->third] }}</td>
                                    <td>{{ $paginateOrderNotice->create_order_time }}</td>
                                    <td>
                                        <div class="form-group col-xs-4" style="margin: 10px 0 10px 0">
                                            <select  style="background-color: #1E9FFF" name="status" lay-filter="change_status" data-amount="{{ $paginateOrderNotice->order->amount }}" data-safe="{{ $paginateOrderNotice->order->orderDetails()->where('field_name', 'security_deposit')->value('field_value') }}"
                                            data-effect="{{ $paginateOrderNotice->order->orderDetails()->where('field_name', 'efficiency_deposit')->value('field_value') }}" lay-data="{{ $paginateOrderNotice->order_no }}">                
                                                <option value="">修改状态</option>
                                                @forelse($ourStatus as $key => $status)
                                                    <option value="{{ $key }}" id="status{{ $key }}" data-status="{{ $status }}" >{{ $status }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $paginateOrderNotice->id }}">删除</button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        </form>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $paginateOrderNotices->total() }}　本页显示：{{ $paginateOrderNotices->count() }}
                        </div>
                            <div class="col-xs-9">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="consult" style="display: none; padding:  0 20px">
        <div class="layui-tab-content">
            <span style="color:red;margin-right:15px;">双方友好协商撤单，若有分歧可以再订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。<br/></span>
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
                        <label class="layui-form-label">发起人</label>
                        <div class="layui-input-block">
                            <input type="radio" name="who" value="1" title="发单">
                            <input type="radio" name="who" value="2" title="接单">
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
                        <label class="layui-form-label">发起人</label>
                        <div class="layui-input-block">
                            <input type="radio" name="who" value="1" title="发单">
                            <input type="radio" name="who" value="2" title="接单">
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
       
        form.on('select(change_status)', function(data){
            var orderAmount = data.elem.getAttribute("data-amount");
            var orderSafe = data.elem.getAttribute("data-safe");
            var orderEffect = data.elem.getAttribute("data-effect");
            var changeStatus = $("#status"+data.value).attr("data-status");

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
            var orderNo = data.elem.getAttribute('lay-data');
            var status = data.value;

            if (data.value == 15 || data.value == 19 || data.value == 21) {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '申请撤销',
                    area: ['650px', '550px'],
                    content: $('.consult')
                });
                form.on('submit(consult)', function(data){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('order.leveling.change-status') }}",
                        data:{orderNo:orderNo, status:status, data:data.field},
                        success: function (data) {
                            if (data.status) {
                                layer.msg(data.message, {icon: 6, time:1000});
                            } else {
                                layer.msg('手动修改失败', {icon: 5, time:1500}); 
                            }
                        }
                    });
                    layer.closeAll();
                    return false;
                    window.location.href="{{ route('order.leveling.index') }}";
                });
            } else if (data.value == 16) {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '申请仲裁',
                    area: ['500px', '280px'],
                    content: $('.complain')
                });
                form.on('submit(complain)', function(data){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('order.leveling.change-status') }}",
                        data:{orderNo:orderNo, status:status, data:data.field},
                        success: function (data) {
                            if (data.status) {
                                layer.msg(data.message, {icon: 6, time:1000});
                            } else {
                                layer.msg('手动修改失败', {icon: 5, time:1500}); 
                            }
                        }
                    });
                    layer.closeAll();
                    return false;
                    window.location.href="{{ route('order.leveling.index') }}";
                });
            } else {
                layer.confirm('确认修改订单状态为【'+changeStatus+'】吗？', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('order.leveling.change-status') }}",
                        data:{orderNo:orderNo, status:status},
                        success: function (data) {
                            if (data.status) {
                                layer.msg(data.message, {icon: 6, time:1000});
                            } else {
                                layer.msg('手动修改失败', {icon: 5, time:1500}); 
                            }
                        }
                    });
                    layer.close(index);
                    window.location.href="{{ route('order.leveling.index') }}";
                });
            }
        });

        form.on('submit(delete)', function (data) {
            var orderId = data.elem.getAttribute("data-id");
            layer.confirm('确认删除订单吗？', {icon: 3, title:'提示'}, function(index){
                $.ajax({
                    type: 'DELETE',
                    url: "{{ route('order.leveling.destroy') }}",
                    data:{orderId:orderId},
                    success: function (data) {
                        if (data.status) {
                            layer.msg(data.message, {icon: 6, time:1000});
                        } else {
                            layer.msg(data.message, {icon: 5, time:1500}); 
                        }
                    }
                });
                layer.close(index);
                window.location.href="{{ route('order.leveling.index') }}";
            });
            return false;
        });
    });
</script>
@endsection