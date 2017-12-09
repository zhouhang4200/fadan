@extends('backend.layouts.main')

@section('title', ' | 订单详情')

@section('css')
    <link rel="stylesheet" type="text/css" href="/backend/css/libs/nifty-component.css"/>
@endsection

@section('content')
    <div class="md-modal" id="refund-application">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">&times;</button>
                <h4 class="modal-title">申请退款 (订单可退金额: {{$content->amount}}) 元</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="buyer">
                        <div class="form-group">
                            <label>退款金额</label>
                            <input type="text" class="form-control refund-amount" name="refund-amount" placeholder="输入需退款金额" value="0">
                        </div>
                        <div class="form-group">
                            <label>说明</label>
                            <textarea class="form-control refund-remark" name="refund-remark" rows="5"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary refund-application-submit">确认提交</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class=""><a href="{{ route('order.platform.index') }}"><span>平台订单</span></a></li>
                <li class="active"><span>订单详情</span></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <div class="layui-tab layui-tab-brief" lay-filter="detail">
                            <ul class="layui-tab-title">
                                <li  class="layui-this"  lay-id="detail"><a href="{{ route('order.platform.content', ['id' => Route::input('id')])  }}">订单内容</a></li>
                                <li lay-id="authentication"><a href="{{ route('order.platform.record', ['id' => Route::input('id')])  }}">订单日志</a></li>
                                <li  class=""  lay-id="record"><a href="{{ route('punishes.record.show', ['id' => Route::input('id')]) }}">奖惩日志</a></li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show content">
                                    <div class="tab-pane active" id="tab-user">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="main-box clearfix" style="border: 1px solid #ddd">
                                                            <header class="main-box-header clearfix">
                                                                <div class="row" style="font-size: 15px">
                                                                    <div class="col-xs-3">状态：{{ config('order.status')[$content->status] }}</div>
                                                                    <div class="col-xs-4">平台订单号：{{ $content->no }} </div>
                                                                    <div class="col-xs-4">外部订单号：{{ $content->foreign_order_no }}</div>
                                                                </div>
                                                            </header>
                                                            <div style=" border-bottom: 1px solid #ddd"></div>
                                                            <div class="" style="padding: 15px;">
                                                                <div class="row">
                                                                    <div class="col-xs-5">
                                                                        <button class="md-trigger btn btn-primary" data-modal="refund-application">
                                                                            <i class="fa fa-plus-circle fa-lg"></i> 申请退款
                                                                        </button>
                                                                    </div>
                                                                    <div class="col-xs-7 layui-form">
                                                                        <input type="hidden" name="no" value="{{ $content->no }}">
                                                                        <div class="col-xs-3">
                                                                            <select name="type" lay-verify="required">
                                                                                <option value="">操作类型</option>
                                                                                <option value="cancel">取消转单</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-xs-6">
                                                                            <input type="text" name="reason" required  lay-verify="required" placeholder="请输入原因" autocomplete="off" class="layui-input">
                                                                        </div>
                                                                        <div class="col-xs-2">
                                                                            <button class="layui-btn  layui-btn-normal" lay-submit="" lay-filter="changeStatus">确认修改</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div style=" border-bottom: 1px solid #ddd"></div>
                                                            <div class="main-box-body clearfix">
                                                                <div class="invoice-summary row">
                                                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                                                        <div class="invoice-summary-item">
                                                                            <span>服务</span>
                                                                            <div>{{ $content->service_name }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                                                        <div class="invoice-summary-item">
                                                                            <span>游戏</span>
                                                                            <div>{{ $content->game_name }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                                                        <div class="invoice-summary-item">
                                                                            <span>商品</span>
                                                                            <div>{{ $content->goods_name }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                                                        <div class="invoice-summary-item">
                                                                            <span>成交价</span>
                                                                            <div>{{ $content->amount }}</div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div id="invoice-companies" class="row">
                                                                    <div class="col-sm-6 invoice-box">
                                                                        <div class="invoice-company">
                                                                            <h4>发单商户</h4>
                                                                            <ul class="fa-ul">
                                                                                <li><i class="fa-li fa fa-truck"></i>商家ID:
                                                                                    <span>{{ $content->creator_primary_user_id }}</span></li>
                                                                                <li><i class="fa-li fa fa-comment"></i>手机号:
                                                                                    <span>{{ $content->creator_primary_user_id }}</span></li>
                                                                                <li><i class="fa-li fa fa-tasks"></i>QQ号: <span>{{ $content->creator_primary_user_id }}</span>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6 invoice-box">
                                                                        <div class="invoice-company">
                                                                            <h4>接单商户</h4>
                                                                            <ul class="fa-ul">
                                                                                <li><i class="fa-li fa fa-truck"></i>商家ID:
                                                                                    <span>{{ $content->gainer_primary_user_id }}</span></li>
                                                                                <li><i class="fa-li fa fa-comment"></i>手机号:
                                                                                    <span>{{ $content->gainer_primary_user_id }}</span></li>
                                                                                <li><i class="fa-li fa fa-tasks"></i>QQ号:
                                                                                    <span>{{ $content->gainer_primary_user_id }}</span></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <h2></h2>
                                                                    <table class="table table-striped table-hover">
                                                                        <tbody>
                                                                        <tr>
                                                                            <td>服务</td>
                                                                            <td>{{ $content->service_name }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>游戏</td>
                                                                            <td>{{ $content->game_name }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>商品</td>
                                                                            <td>{{ $content->goods_name }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>原单价</td>
                                                                            <td>{{ $content->original_price }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>原总价</td>
                                                                            <td>{{ $content->original_amount }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>集市单价</td>
                                                                            <td>{{ $content->price }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>集市总价</td>
                                                                            <td>{{ $content->amount }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>数量</td>
                                                                            <td>{{ $content->quantity }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>备注</td>
                                                                            <td>{{ $content->remark }}</td>
                                                                        </tr>
                                                                        @forelse($content->detail as $item)
                                                                            @if($item->field_name != 'quantity')
                                                                                <tr>
                                                                                    <td class="text-left" width="20%">
                                                                                        {{ $item->field_display_name }}
                                                                                    </td>
                                                                                    <td class="text-left" style="font-size: 14px">
                                                                                        {{ $item->field_value }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                        @empty

                                                                        @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item record"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="md-overlay"></div>
@endsection

@section('js')

<script>
    layui.use(['layer', 'element', 'form'], function () {
        var element = layui.element, form = layui.form;

        // 修改订单状态
        form.on('submit(changeStatus)', function (data) {
            $.post('{{ route('order.platform.change-status') }}', {data:data.field}, function (result) {
                layer.msg(result.message);
            }, 'json');
            return false;
        });

        // 打开退款弹窗
        $('.layui-tab-content').on('click', '#refund', function () {
            layer.open({
                type: 1,
                shade: 1,
                title: '申请退款',
                content: $('.refund')
            });
        });
        // 确认退款申请
        $('.refund-application-submit').click(function () {
            var amount = "{{ $content->amount }}";
            var refundAmount = $('.refund-amount').val();
            var refundRemark = $('.refund-remark').val();

            if (eval(amount) < eval(refundAmount)) {
                layer.msg('退款金额不能大于订单总金额');
                return false;
            }
            if (!refundAmount) {
                refundAmount = 0;
            }
            if (refundRemark.length == 0) {
                layer.msg('请输入退款说明');
                return false;
            }
            var noteMessage = '给发单方退款：'  + refundAmount + '元<br/>接单方将收到：' + (amount - refundAmount) + '元';

            layer.confirm(noteMessage, {icon: 3, title: '需要确认'}, function (index) {
                layer.close(index);
                $.ajax({
                    url: '{{ route('order.after-service.apply') }}',
                    type: 'post',
                    dataType: 'json',
                    data: {no: "{{ $content->no }}", amount:refundAmount, remark:refundRemark},
                    success: function (result) {
                        layer.alert(result.message, function () {
                            layer.closeAll();
                            $("#refund-application").niftyModal("hide");
                        });
                    }
                });
            });
        });
    });
</script>
@endsection