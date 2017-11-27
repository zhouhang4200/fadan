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
                <h4 class="modal-title">申请退款 (订单可退金额:) 元</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="form-group">
                        <label>退款给</label>
                        <select class="form-control who">
                            <option value="0">双方</option>
                            <option value="1">买家</option>
                            <option value="2">卖家</option>
                        </select>
                    </div>
                    <div class="buyer">
                        <div class="form-group">
                            <label>给买家(退款金额)</label>
                            <input type="text" class="form-control buyer-money" name="refund-money"
                                   placeholder="输入需退款金额">
                        </div>
                        <div class="form-group">
                            <label>给买家(备注)</label>
                            <textarea class="form-control buyer-remark" name="refund-remark" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="seller no">
                        <div class="form-group">
                            <label>给卖家(退款金额)</label>
                            <input type="text" class="form-control seller-money" name="refund-money"
                                   placeholder="输入需退款金额">
                        </div>
                        <div class="form-group">
                            <label>给卖家(备注)</label>
                            <textarea class="form-control seller-remark" name="refund-remark" rows="5"></textarea>
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
                                <li  lay-id="detail"><a href="{{ route('order.platform.content', ['id' => Route::input('id')])  }}">订单内容</a></li>
                                <li  class="layui-this"  lay-id="authentication"><a href="{{ route('order.platform.record', ['id' => Route::input('id')])  }}">订单日志</a></li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item content">
                                </div>
                                <div class="layui-tab-item  layui-show  record">
                                    <table class="layui-table" lay-size="sm">
                                        <thead>
                                        <tr>
                                            <th>订单号</th>
                                            <th>操作商户</th>
                                            <th>操作管理员</th>
                                            <th>类型</th>
                                            <th>操作描述</th>
                                            <th>操作时间</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($record->history as $item)
                                            <tr>
                                                <td>{{ $item->order_no }}</td>
                                                <td>{{ ($item->user_id == 0 || is_null($item->user_id)) ? '系统' : $item->user->getPrimaryUserId() }}</td>
                                                <td>{{ $item->admin_user_id }}</td>
                                                <td>{{ config('order.operation_type')[$item->type] }}</td>
                                                <td>{{ $item->description }}</td>
                                                <td>{{ $item->created_at }}</td>
                                            </tr>
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
@endsection

@section('js')
    <script src="/backend/js/modalEffects.js"></script>
    <script src="/backend/js/jquery.modalEffects.js"></script>
    <script>
        layui.use(['layer', 'element', 'form'], function () {
            var element = layui.element, form = layui.form;


            // 添加商品弹窗
            $('.layui-tab-content').on('click', '#refund', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '申请退款',
                    content: $('.refund')
                });
            });

        });
    </script>
@endsection