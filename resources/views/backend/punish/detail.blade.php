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
                                <li  @if(Route::currentRouteName() == 'order.platform.record') class="layui-this"  @endif lay-id="authentication"><a href="{{ route('order.platform.record', ['id' => Route::input('id')])  }}">订单日志</a></li>
                                <li  @if(Route::currentRouteName() == 'punishes.record.show') class="layui-this"  @endif lay-id="show"><a href="{{ route('punishes.record.show', ['id' => Route::input('id')]) }}">奖惩日志</a></li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item content">
                                </div>
                                <div class="layui-tab-item  layui-show  show">
                                    <table class="layui-table" lay-size="sm">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>单号</th>
                                            <th>关联订单号</th>
                                            <th>操作管理员</th>
                                            <th>类型</th>
                                            <th>操作描述</th>
                                            <th>操作时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($punishRecords as $punishRecord)
                                            <tr>
                                                <td>{{ $punishRecord->id }}</td>
                                                <td>{{ $punishRecord->punish_or_reward_no }}</td>
                                                <td>{{ $punishRecord->order_no }}</td>
                                                <td>{{ $punishRecord->admin_user_name ?? '系统' }}</td>
                                                <td>
                                                    @if($punishRecord->operate_style == 'created_at')
                                                        创建
                                                    @elseif($punishRecord->operate_style == 'deleted_at')
                                                        撤销
                                                    @else
                                                        更新
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($punishRecord->operate_style == 'created_at')
                                                        管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】，创建了奖惩记录,对应 奖惩列表 里面的序号 【{{ $punishRecord->punish_or_reward_id }}】
                                                    @elseif($punishRecord->operate_style == 'deleted_at')
                                                        管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】 ，撤销了奖惩记录,对应 奖惩列表 里面的序号 【{{ $punishRecord->punish_or_reward_id }}】
                                                    @else
                                                        @if ($punishRecord->operate_style == 'confirm')
                                                            管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】 ，修改了奖惩记录,将 【{{  $punishRecord->operate_style }}】 从原来的状态 【{{ config('punish.confirm')[$punishRecord->before_value] }}】 更新为 【{{ config('punish.confirm')[$punishRecord->after_value] }}】
                                                        @elseif($punishRecord->operate_style == 'status')
                                                            管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】 ，修改了奖惩记录将 【{{  $punishRecord->operate_style }}】 从原来的状态 【{{ config('punish.status')[$punishRecord->before_value] }}】 更新为 【{{ config('punish.status')[$punishRecord->after_value] }}】
                                                        @else
                                                            管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】 ，修改了奖惩记录,将 【{{  $punishRecord->operate_style }}】 从原来的状态 【{{ $punishRecord->before_value }}】 更新为 【{{ $punishRecord->after_value }}】
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $punishRecord->created_at }}</td>
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

        });
    </script>
@endsection