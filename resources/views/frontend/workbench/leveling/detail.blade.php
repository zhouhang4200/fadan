@extends('frontend.layouts.app')

@section('title', '工作台 - 代练 - 订单详情')

@section('css')
<link href="{{ asset('/css/index.css') }}" rel="stylesheet">
<style>
.wrapper {
    width: 1600px;
}
.main .right {
    width: 1430px;
}
.layui-input-block{
    margin-left: 50px;
}
.form-group {
    margin-bottom: 7px;
}

.layui-form-mid {
    text-align: right;
}

.site-title {
    margin: 10px 0 20px;
}
.site-title fieldset {
    border: none;
    padding: 0;
    border-top: 1px solid #eee;
}
.site-title fieldset legend {
    font-size: 17px;
    font-weight: 300;
}
.layui-form-checkbox[lay-skin=primary] {
    height: 6px !important;
}
#info .layui-form-item .layui-input-block{
    margin-left: 200px;
}
#info .layui-form-item .layui-form-label{
    width: 160px;
}

/* 留言 */
.message_box {
  display: flex;
}
.message_box .im {
  width: 430px;
  border-right: 1px solid #ccc;
  height: 800px;
}
.message_box .im .chat_window {
  width: 80%;
  height: 500px;
  margin: auto;
  margin-top: 20px;
  border: 1px solid #ccc;
  overflow: auto;
  padding: 10px 20px 10px 20px;
  box-sizing: border-box;
}
.message_box .im .chat_window .customer_message {
  margin-top: 10px;
}
.message_box .im .chat_window .customer_message .message .message_time {
  text-align: center;
}
.message_box .im .chat_window .customer_message .message .portrait {
  width: 30px;
  height: 30px;
  background-color: yellow;
  border-radius: 50%;
  display: inline-block;
  overflow: hidden;
  float: left;
  margin-top: 24px;
  margin-right: 20px;
}
.message_box .im .chat_window .customer_message .message .portrait img {
  width: 100%;
  height: 100%;
}
.message_box .im .chat_window .customer_message .message .content {
  width: 80%;
  min-height: 35px;
  border: 1px solid #E3E3E3;
  border-radius: 3px;
  display: inline-block;
  margin-top: 20px;
  line-height: 20px;
  text-indent: 20px;
  padding: 5px 10px;
  background-color: #fff;
  box-sizing: border-box;
  position: relative;
}
.message_box .im .chat_window .customer_message .message .content::before,
.message_box .im .chat_window .customer_message .message .content:after {
  border: solid transparent;
  content: ' ';
  height: 0;
  top: 10px;
  left: -16px;
  position: absolute;
  width: 0;
  border-width: 8px;
  border-right-color: #fff;
}
.message_box .im .chat_window .customer_message .message .content::before {
  left: -17px;
  border-right-color: #E3E3E3;
}
.message_box .im .chat_window .kf_message {
  margin-top: 10px;
}
.message_box .im .chat_window .kf_message .message .message_time {
  text-align: center;
}
.message_box .im .chat_window .kf_message .message .portrait {
  width: 30px;
  height: 30px;
  background-color: yellow;
  border-radius: 50%;
  display: inline-block;
  overflow: hidden;
  float: right;
  margin-top: 20px;
  margin-left: 20px;
}
.message_box .im .chat_window .kf_message .message .portrait img {
  width: 100%;
  height: 100%;
}
.message_box .im .chat_window .kf_message .message .content {
  width: 80%;
  min-height: 35px;
  border: 1px solid #E3E3E3;
  border-radius: 3px;
  display: inline-block;
  float: right;
  padding: 5px 10px;
  box-sizing: border-box;
  margin-top: 20px;
  line-height: 20px;
  text-indent: 20px;
  background-color: #8DFA69;
  position: relative;
  margin-bottom: 20px;
}
.message_box .im .chat_window .kf_message .message .content:after {
  border: solid transparent;
  content: ' ';
  height: 0;
  top: 7px;
  right: -20px;
  position: absolute;
  width: 0;
  border-width: 10px;
  border-left-color: #8DFA69;
}
.message_box .im .chat_bar {
  width: 80%;
  height: 100px;
  margin: auto;
  padding: 15px;
  box-sizing: border-box;
  background-color: #E3E3E3;
}
.message_box .information {
  flex: 3;
  height: 500px;
}

/* 留言结束 */
</style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')
    <div class="layui-tab layui-tab-brief" lay-filter="myFilter">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="detail">详情</li>
            <li lay-id="leave-message">留言/截图</li>
            <li lay-id="history">操作记录</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="" style="text-align: right;">
                    @if($detail['status'] != 24)

                        @if ($detail['master'] && $detail['status'] == 22)
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="onSale" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">上架</button>
                        @endif

                        @if ($detail['master'] && $detail['status'] == 1)
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="offSale" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">下架</button>
                        @endif

                        @if ($detail['master'] && in_array($detail['status'], [14, 15, 16, 17, 18, 19, 20, 21]))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="repeat" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">重发</button>
                        @endif

                        @if ($detail['master'] && isset($detail['urgent_order']) && $detail['urgent_order'] != 1)
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="urgent" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">加急</button>
                        @endif

                        @if ($detail['master'] && isset($detail['urgent_order']) && $detail['urgent_order'] == 1)
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="unUrgent" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消加急</button>
                        @endif

                        @if ($detail['master'] && in_array($detail['status'], [13, 14,  17]))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="lock" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">锁定</button>
                        @endif

                        @if ($detail['master'] && $detail['status'] == 18)
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="cancelLock" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消锁定</button>
                        @endif

                        @if ($detail['master'])
                            @if ($detail['consult'] == 1 && $detail['status'] == 15)
                                <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="cancelRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消撤销</button>
                            @elseif ($detail['consult'] == 2 && ($detail['status'] == 15 || $detail['status'] == 16))
                                <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="agreeRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">同意撤销</button>
                                <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="refuseRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">不同意撤销</button>
                            @endif
                        @else
                            @if ($detail['consult'] == 2 && $detail['status'] == 15)
                                <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="cancelRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消撤销</button>
                            @elseif ($detail['consult'] == 1 && ($detail['status'] == 15 || $detail['status'] == 16))
                                <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="agreeRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">同意撤销</button>
                                <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="refuseRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">不同意撤销</button>
                            @endif
                        @endif

                        @if (in_array($detail['status'], [13, 14, 17, 18]))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="revoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">撤销</button>
                        @endif

                        @if (in_array($detail['status'], [13,14,15]))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="applyArbitration" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">申请仲裁</button>
                        @endif

                        @if ($detail['master'])
                            @if ($detail['complain'] == 1 && $detail['status'] == 16)
                                <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="cancelArbitration" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消仲裁</button>
                            @endif
                        @else
                            @if ($detail['complain'] == 2 && $detail['status'] == 16)
                                <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="cancelArbitration" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消仲裁</button>
                            @endif
                        @endif

                        @if ($detail['master'] && $detail['status'] == 14)
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="complete" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">完成</button>
                        @endif

                        @if ($detail['master'])
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="sendSms" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">发短信</button>
                        @endif

                        @if ($detail['master'] && isset($detail['client_wang_wang']) &&  $detail['client_wang_wang'])
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="wangWang" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}" data-wang-wang="{{ $detail['client_wang_wang'] }}">联系旺旺号</button>
                        @endif

                        @if ($detail['master'] && ($detail['status'] == 1 || $detail['status'] == 22))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="delete" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">删除</button>
                        @endif

                        @if (!$detail['master'] && ($detail['status'] == 13))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="applyComplete" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">申请完成</button>
                        @endif

                        @if (!$detail['master'] && ($detail['status'] == 14))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="cancelComplete" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消验收</button>
                        @endif

                        @if (!$detail['master'] && ($detail['status'] == 13))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="abnormal" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">异常</button>
                        @endif

                        @if (!$detail['master'] && ($detail['status'] == 17))
                            <button lay-submit=""   lay-filter="operation" class="layui-btn layui-btn-normal"  data-operation="cancelAbnormal" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消异常</button>
                        @endif
                    @endif
                </div>
                <div class="layui-row  layui-col-space20">
                    <div class="layui-col-md6">
                        <div class="site-title">
                            <fieldset><legend><a name="hr">订单信息</a></legend></fieldset>
                        </div>
                        <form class="layui-form" action="">
                            <input type="hidden" name="no" value="{{ $detail['no'] }}">
                            <div class="layui-row form-group">
                                <div class="layui-col-md6">
                                    <div class="layui-col-md3 layui-form-mid">*游戏</div>
                                    <div class="layui-col-md8">
                                        <select name="game_id" lay-verify="required" lay-search="" @if(!in_array($detail['status'], [1, 23]))  disabled="disabled"  @endif lay-filter="game">
                                            @foreach($game as $key => $value)
                                                <option value="{{ $key }}" @if($value == $detail['game_name']) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="template">
                                <div class="layui-row form-group">
                                    <?php $row = 0 ?>
                                    @forelse($template as $item)

                                        @if($row == 0)
                                            <?php  $row = $item->display_form ?>
                                        @endif

                                        <div class="layui-col-md6">
                                            <div class="layui-col-md3 layui-form-mid"> @if ($item->field_required == 1) <span style="color: orangered;">*</span> @endif
                                                {{ $item->field_display_name  }}
                                            </div>
                                            <div class="layui-col-md8">

                                                <!--订单状态为 没有接单 已下架时可以编辑该属性-->
                                                @if($item->field_type == 1 && in_array($detail['status'], [1, 23]))
                                                    <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                                @elseif($item->field_type == 1)

                                                    @if(in_array($detail['status'], [13, 17]) && in_array($item->field_name, ['game_leveling_amount', 'password', 'game_leveling_day' , 'game_leveling_hour']))
                                                        <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                                    @elseif(in_array($detail['status'], [14]) && $item->field_name == 'game_leveling_amount')
                                                        <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                                    @elseif(in_array($detail['status'], [18]) && $item->field_name == 'password')
                                                        <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                                    @elseif(in_array($item->field_name, ['order_source', 'source_order_no', 'source_price', 'client_name', 'client_phone', 'client_qq', 'client_wang_wang', 'game_leveling_require_day', 'game_leveling_require_hour']))
                                                        <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input" lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                                    @else
                                                        <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input layui-disabled" lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}"  readonly="readonly">
                                                    @endif

                                                @endif

                                                @if($item->field_type == 2 && in_array($detail['status'], [1, 23]) || $item->field_name == 'label')
                                                    <select name="{{ $item->field_name }}"  lay-search="" lay-verify="@if ($item->field_required == 1) required @endif" lay-filter="change-select" data-id="{{ $item->id }}" id="select-parent-{{ $item->field_parent_id }}">
                                                        <option value=""></option>
                                                        @if(count($item->user_values) > 0)

                                                            @foreach($item->user_values as $v)
                                                                <option data-id="{{ $v->id  }}"  value="{{ $v->field_value }}" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] ==  $v->field_value) selected  @endif>{{ $v->field_value }}</option>
                                                            @endforeach

                                                        @else

                                                            @if(count($item->values) > 0)
                                                                @foreach($item->values as $v)
                                                                    <option data-id="{{ $v->id  }}"  value="{{ $v->field_value }}" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] ==  $v->field_value) selected  @endif>{{ $v->field_value }}</option>
                                                                @endforeach
                                                            @endif

                                                        @endif
                                                    </select>
                                                @elseif($item->field_type == 2)
                                                    <select name="{{ $item->field_name }}"  lay-search="" lay-verify="@if ($item->field_required == 1) required @endif" class="layui-disabled" disabled>
                                                        <option value=""></option>
                                                        @if(count($item->user_values) > 0)

                                                            @foreach($item->user_values as $v)
                                                                <option data-id="{{ $v->id  }}"  value="{{ $v->field_value }}" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] ==  $v->field_value) selected  @endif>{{ $v->field_value }}</option>
                                                            @endforeach

                                                        @else

                                                            @if(count($item->values) > 0)
                                                                @foreach($item->values as $v)
                                                                    <option data-id="{{ $v->id  }}"  value="{{ $v->field_value }}" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] ==  $v->field_value) selected  @endif>{{ $v->field_value }}</option>
                                                                @endforeach
                                                            @endif

                                                        @endif
                                                    </select>
                                                @endif

                                                @if($item->field_type == 3 && in_array($detail['status'], [1, 23]))

                                                @endif

                                                @if($item->field_type == 4 && in_array($detail['status'], [1, 23]) || $item->field_name == 'cstomer_service_remark')
                                                    <textarea name="{{ $item->field_name }}"  class="layui-textarea"  lay-verify="@if($item->field_required == 1) required @endif">{{ $detail[$item->field_name] ?? '' }}</textarea>
                                                @elseif($item->field_type == 4)
                                                    <textarea name="{{ $item->field_name }}" class="layui-textarea"  lay-verify="@if($item->field_required == 1) required @endif"  class="layui-disabled" disabled>{{ $detail[$item->field_name] ?? '' }}</textarea>
                                                @endif

                                                @if($item->field_type == 5 && in_array($detail['status'], [1, 23]) || $item->field_name == 'urgent_order')
                                                    <input type="checkbox" name="{{ $item->field_name }}" lay-skin="primary"  lay-verify="@if($item->field_required == 1) require @endif" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] == 1) checked @endif>
                                                @elseif($item->field_type == 5)
                                                    <input type="checkbox" name="{{ $item->field_name }}" lay-skin="primary"  lay-verify="@if($item->field_required == 1) require @endif" class="layui-disabled" disabled @if(isset($detail[$item->field_name]) && $detail[$item->field_name] == 1) checked @endif>
                                                @endif

                                            </div>
                                        </div>

                                        <?php $row--; ?>

                                        @if($row == 0)
                                </div>
                                <div class="layui-row form-group">
                                    @endif

                                    @empty

                                    @endforelse
                                </div>
                            </div>

                            <div class="layui-col-md-offset2">
                                <div class="layui-btn layui-btn-normal  layui-col-md2" lay-submit="" lay-filter="save-update">确定</div>
                            </div>
                        </form>
                    </div>

                    <div class="layui-col-md3">
                        <div class="site-title">
                            <fieldset><legend><a name="hr">订单数据</a></legend></fieldset>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">平台单号：</div>
                            <div class="layui-col-md8">{{ $detail['no']  }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">订单状态：</div>
                            <div class="layui-col-md8">{{ config('order.status_leveling')[$detail['status']] }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">支付金额：</div>
                            <div class="layui-col-md8">{{ $detail['payment_amount']?? ''  }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">获得金额：</div>
                            <div class="layui-col-md8">{{ $detail['get_amount']?? '' }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">手续费：</div>
                            <div class="layui-col-md8">{{ $detail['poundage'] ?? '' }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">利润：</div>
                            <div class="layui-col-md8">{{ $detail['profit'] ?? ''  }}</div>
                        </div>

                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">剩余代练时间：</div>
                            <div class="layui-col-md8">{{ $detail['left_time'] ?? ''  }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">发布时间：</div>
                            <div class="layui-col-md8"> {{ $detail['created_at'] ?? '' }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">接单时间：</div>
                            <div class="layui-col-md8">{{ $detail['receiving_time'] ?? ''  }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">提验时间：</div>
                            <div class="layui-col-md8">{{ $detail['check_time'] ?? ''  }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">结算时间：</div>
                            <div class="layui-col-md8">{{ $detail['checkout_time'] ?? ''  }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">发单客服：</div>
                            <div class="layui-col-md8">{{ $detail['cstomer_service_name'] ?? ''  }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">撤销说明：</div>
                            <div class="layui-col-md8">{{ $detail['consult_desc'] ?? '' }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">仲裁说明：</div>
                            <div class="layui-col-md8">{{ $detail['complain_desc'] ?? '' }}</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">仲裁结果：</div>
                            <div class="layui-col-md8">{{ $detail['complain_result'] ?? '' }}</div>
                        </div>
                    </div>

                    <div class="layui-col-md3">
                        <div class="site-title">
                            <fieldset><legend><a name="hr">订单来源</a></legend></fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-tab-item" id="leave-message">
                <div class="message_box">
                    <div class="im">
                        <div class="chat_window"></div>
                        <div class="chat_bar">
                            <form class="layui-form" id="form-send-message">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline" style="margin-left:0;width:70%;" style=" position: relative;">
                                        <input type="text" name="show91-message" required lay-verify="required" placeholder="请输入留言" autocomplete="off" class="layui-input"
                                            style="height:70px;border-radius:0;line-height:0;">
                                        <button type="button" class="layui-btn layui-btn-normal" lay-submit lay-filter="send-message" style="position:absolute;right:-87px;top:0px;">发送</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="information">
                        <div class="layui-form" action="">
                            <div class="layui-form-item">
                                <label class="layui-form-label">上传截图</label>
                                <div class="layui-input-inline">
                                    <button type="button" class="layui-btn" id="upload-image">
                                        <i class="layui-icon">&#xe67c;</i>上传图片
                                    </button>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">截图说明</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="image_description" placeholder="请输入简短文字" class="layui-input" style="width: 828px">
                                </div>
                            </div>
                        </div>

                        <div style="margin-left: 20px; overflow-y: scroll;height: 713px" id="leave-image"></div>
                    </div>
                </div>
            </div>
            <div class="layui-tab-item" id="history"></div>
        </div>
    </div>
    <div class="consult" style="display: none; padding:  0 20px">
        <div class="layui-tab-content">
            <span style="color:red;margin-right:15px;">双方友好协商撤单，若有分歧可以再订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。<br/></span>
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div style="width: 100%" id="info">
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
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div >
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin:0px">
                            <textarea placeholder="请输入申请仲裁理由" name="complain_message" lay-verify="required" class="layui-textarea" style="width:90%;margin:auto;height:150px !important;"></textarea>

                        </div>
                    </div>
                    <div class="layui-form-item">

                        <div class="layui-input-block" style="margin: 0 auto;text-align: center;">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="complain">确认</button>
                            <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="send-message" style="display: none; padding: 10px 10px 0 10px">
        <div class="layui-tab-content">
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div>
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin:0">
                            <textarea placeholder="请输入要发送的内容" name="contents" lay-verify="required" class="layui-textarea" style="width:90%;margin:auto;height:150px !important;"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin: 0 auto;text-align: center;">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="confirm-send-sms">确认</button>
                            <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

<!--START 底部-->
@section('js')
<script id="goodsTemplate" type="text/html">
        <input type="hidden" name="id" value="@{{ d.id }}">
        <div class="layui-row form-group">
            @{{# var row = 0;}}
            @{{#  layui.each(d.template, function(index, item){ }}

            @{{#  if(row == 0) { row = item.display_form;  }  }}

            <div class="layui-col-md6">
                <div class="layui-col-md3 layui-form-mid">
                    @{{# if (item.field_required == 1) {  }}<span style="color: orangered;">*</span>@{{# }  }} @{{ item.field_display_name  }}
                </div>
                <div class="layui-col-md8">

                    @{{# if(item.field_type == 1) {  }}
                    <input type="text" name="@{{ item.field_name }}"  autocomplete="off" class="layui-input" lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}|@{{ item.verify_rule }}" display-name="@{{item.field_display_name}}">
                    @{{# } }}

                    @{{# if(item.field_type == 2) {  }}
                    <select name="@{{ item.field_name }}"  lay-search="" lay-verify="@{{# if (item.field_required == 1) { }}required@{{# } }}"  display-name="@{{item.field_display_name}}" lay-filter="change-select" data-id="@{{ item.id }}" id="select-parent-@{{ item.field_parent_id }}">
                        <option value=""></option>
                        @{{#  if(item.user_values.length > 0){ }}
                        @{{#  layui.each(item.user_values, function(i, v){ }}
                        <option data-id="@{{ v.id }}" value="@{{ v.field_value }}">@{{ v.field_value }}</option>
                        @{{#  }); }}
                        @{{#  } else { }}
                        @{{#  if(item.values.length > 0){ }}
                        @{{#  layui.each(item.values, function(i, v){ }}
                        <option value="@{{ v.field_value }}">@{{ v.field_value }}</option>
                        @{{#  }); }}
                        @{{#  }  }}
                        @{{#  }  }}
                    </select>
                    @{{# } }}

                    @{{# if(item.field_type == 3) {  }}
                    @{{# } }}

                    @{{# if(item.field_type == 4) {  }}
                    <textarea name="@{{ item.field_name }}" placeholder="请输入内容" class="layui-textarea"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}"  display-name="@{{item.field_display_name}}"></textarea>
                    @{{# } }}

                    @{{# if(item.field_type == 5) {  }}
                    <input type="checkbox" name="@{{ item.field_name }}" lay-skin="primary"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# }  }}"  display-name="@{{item.field_display_name}}">
                    @{{# } }}

                    @{{# if(item.help_text != '无') {  }}
                    <a href="#" class="tooltip">
                        <i class="iconfont icon-wenhao" id="recharge"></i>
                        <span>@{{ item.help_text }}</span>
                    </a>
                    @{{# }  }}

                </div>

            </div>

            @{{#  row--; }}

            @{{# if(row == 0) { }}
        </div>
        <div class="layui-row form-group">
            @{{# }  }}

            @{{# })  }}
        </div>

    </script>
<script>
    layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element', 'upload'], function(){
        var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
        var upload = layui.upload;

        $('.cancel').click(function(){
            layer.closeAll();
        });

        form.on('checkbox', function(data){
            if (data.elem.checked) {
                $(data.elem).val(1);
            } else {
                $(data.elem).remove();
                $('.layui-form').append('<input type="hidden" name="' + $(data.elem).attr("name") + '" value="0"/>');
            }
        });
        // 模版预览 下拉框值
        form.on('select(change-select)', function(data){
            var subordinate = "#select-parent-" + data.elem.getAttribute('data-id');
            var choseId = $(data.elem).find("option:selected").attr("data-id");
            if($(subordinate).length > 0){
                $.post('{{ route('frontend.workbench.get-select-child') }}', {parent_id:choseId}, function (result) {
                    $(subordinate).html(result);
                    $(result).each(function (index, value) {
                        $(subordinate).append('<option value="' + value.field_value + '">' + value.field_value + '</option>');
                    });
                    layui.form.render();
                }, 'json');
            }
            return false;
        });

        form.on('submit(operation)', function () {
            var operation = this.getAttribute('data-operation');

            var orderNo = this.getAttribute("data-no");
            var orderAmount = this.getAttribute("data-amount");
            var orderSafe = this.getAttribute("data-safe");
            var orderEffect = this.getAttribute("data-effect");

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

            if (operation == 'wangWang') {
                var wangWang = this.getAttribute("data-wang-wang");
                window.open('http://www.taobao.com/webww/ww.php?ver=3&touid=' + wangWang  +  '&siteid=cntaobao&status=1&charset=utf-8" class="btn btn-save buyer" target="_blank" title="' + wangWang);
            }
            if (operation == 'sendSms') {
                $('.send-message  .layui-form').append('<input type="hidden" name="no" value="' + orderNo + '"/>');
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '发送短信',
                    area: ['500px', '280px'],
                    content: $('.send-message')
                });
                return false;
            }
            // 重发
            if (operation == 'repeat') {
                window.open('{{ route('frontend.workbench.leveling.repeat') }}' + '/'  + orderNo);
            }
            if (operation == 'revoke') {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '撤销',
                    area: ['650px', '550px'],
                    content: $('.consult')
                });

                form.on('submit(consult)', function(data){
                    $.post("{{ route('frontend.workbench.leveling.consult') }}", {
                        orderNo:orderNo,
                        data:data.field
                    }, function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message, function () {
                                window.location.reload()
                            });
                        } else {
                            layer.alert(result.message, function (index) {
                                layer.close(index);
                            });
                        }
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
                form.on('submit(complain)', function(data){
                    $.post("{{ route('frontend.workbench.leveling.complain') }}", {
                        orderNo:orderNo,
                        data:data.field
                    }, function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message, function (index) {
                                window.location.reload()
                            });

                        } else {
                            layer.alert(result.message,function (index) {
                                layer.close(index);
                            });
                        }
                    });
                    return false;
                });

            } else if (operation == 'delete') {
                layer.confirm('确认删除吗？', {icon: 3, title:'提示'}, function(index){
                    $.post("{{ route('frontend.workbench.leveling.status') }}", {
                        orderNo:orderNo,
                        keyWord:operation
                    }, function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message, function () {
                                window.location.reload()
                            });
                        } else {
                            layer.alert(result.message, function () {
                                window.location.reload()
                            });
                        }
                    });
                });
            } else if(operation == 'complete') {
                layer.confirm('确定完成订单？', {icon: 3, title:'提示'}, function(index){
                    $.post("{{ route('frontend.workbench.leveling.status') }}", {
                        orderNo:orderNo,
                        keyWord:operation
                    }, function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message,function () {
                                window.location.reload()
                            });
                        } else {
                            layer.alert(result.message, function () {
                                window.location.reload()
                            });
                        }
                    });
                    layer.close(index);
                });
            } else if (operation == 'agreeRevoke') {
                layer.confirm('确定同意撤销吗？', {icon: 3, title:'提示'}, function(index){
                    $.post("{{ route('frontend.workbench.leveling.status') }}", {
                        orderNo:orderNo,
                        keyWord:operation
                    }, function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message,function () {
                                window.location.reload()
                            });
                        } else {
                            layer.alert(result.message, function () {
                                window.location.reload()
                            });
                        }
                    });
                    layer.close(index);
                });
            } else {
                $.post("{{ route('frontend.workbench.leveling.status') }}", {
                    orderNo:orderNo,
                    keyWord:operation
                }, function (result) {
                    if (result.status == 1) {
                        layer.alert(result.message, function () {
                            window.location.reload()
                        });

                    } else {
                        layer.alert(result.message, function () {
                            window.location.reload()
                        });
                    }
                });
            }
        });
        // 修改
        form.on('submit(save-update)', function (data) {

            if(data.field.game_leveling_day == 0 && data.field.game_leveling_hour == 0) {
                layer.msg('代练时间不能都为0');
                return false;
            }

            $.post('{{ route('frontend.workbench.leveling.update') }}', {data: data.field}, function (result) {
                if (result.status == 1) {
                    layer.open({
                        content: '修改成功!',
                        btn: ['继续发布', '订单列表', '待发订单'],
                        btn1: function(index, layero){
                            location.reload();
                        },
                        btn2: function(index, layero){
                            window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                        },
                        btn3: function(index, layero){
                            window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                        }
                    });
                } else {
                    layer.msg(result.message);
                }
            }, 'json');
            return false;
        });
        // 监听Tab切换
        element.on('tab(myFilter)', function(){
            switch (this.getAttribute('lay-id')) {
                case 'history':
                    loadHistory();
                    break;
                case 'leave-message':
                    // 加载订单留言
                    loadMessage();
                    // 加载订单截图
                    loadImage();
                    break;
                default:
                    break;
            }
        });
        // 切换游戏时加截新的模版
        form.on('select(game)', function (data) {
            loadTemplate(data.value)
        });
        // 加载模板
        function loadTemplate(id) {
            var getTpl = goodsTemplate.innerHTML, view = $('#template');
            $.post('{{ route('frontend.workbench.leveling.get-template') }}', {game_id:id, no:'{{ Request::input('no') }}'}, function (result) {
                layTpl(getTpl).render(result.content, function(html){
                    view.html(html);
                    layui.form.render();
                });
                if (result.content.value.length != 0) {

                    $.each(result.content.value, function (name, value) {
                        // 获取表单dom
                        var $formDom = $('#template').find('[name="' + name + '"]');
                        // 填充表单
                        switch ($formDom.prop('type')) {
                            case 'select-one':
                                $formDom.find('option').each(function () {
                                    if ($(this).text() == value) {
                                        $formDom.val($(this).val());
                                        return false;
                                    }
                                });
                                break;
                            case 'checkbox':
                                if (value == 1) {
                                    $formDom.prop('checked', true);
                                } else {
                                    $formDom.prop('checked', false);
                                }
                                break;
                            default:
                                $formDom.val(value);
                                break;
                        }
                    });
                }

            }, 'json');
        }

        // 阻止默认事件
        $('#form-send-message').submit(function () {
            return false;
        });

        // 发送留言
        form.on('submit(send-message)', function (data) {
            var $button = $(this);
            $button.attr('disabled', true).text('发送中...');

            $.post("{{ route('frontend.workbench.leveling.send-message') }}", {
                'oid': "{{ $detail['third_order_no'] }}",
                'mess': $('[name="show91-message"]').val()
            }, function (data) {
                $button.attr('disabled', false).text('发送');
                $('[name="show91-message"]').val('');

                if (data.status === 1) {
                    loadMessage();
                } else {
                    layer.msg(data.message);
                    return false;
                }
            }, 'json');
        });

        // 发送短信
        form.on('submit(confirm-send-sms)', function (data) {
            $.post('{{ route('frontend.workbench.leveling.send-sms') }}', {no:data.field.no, contents:data.field.contents},function (result) {
                layer.closeAll();
                layer.msg(result.message);
            }, 'json');
            return false;
        });
        // 截图上传
        //执行实例
        var uploadInst = upload.render({
            elem: '#upload-image',
            url: "{{ route('frontend.workbench.leveling.upload-image') }}",
            accept: 'images',
            field: 'image',
            data: {
                order_no: "{{ $detail['no'] }}"
            },
            before: function (obj) {
                this.data.description = $('[name="image_description"]').val();
                load = layer.load(4, {shade:0.3});
            },
            done: function (res, index, upload) {
                layer.close(load);

                if (res.status === 1) {
                    loadImage();
                    layer.alert('上传成功');
                } else {
                    layer.alert(res.message);
                }
            }
        });

        var tab = getQueryString(window.location.href, 'tab');
        if (tab == '1') {
            element.tabChange('myFilter', 'leave-message')
        } else if(tab == '2') {
            loadHistory();
            element.tabChange('myFilter', 'history')
        }
    });

    function loadHistory() {
        // 加载订单操作记录
        $.get("{{ route('frontend.workbench.leveling.history', ['order_no' => $detail['no']]) }}", function (data) {
            if (data.status === 1) {
                $('#history').html(data.content);
            } else {
                layer.alert(data.message);
            }
        });
    }

    // 加载留言
    function loadMessage()
    {
        $.get("{{ route('frontend.workbench.leveling.leave-message', ['order_no' => $detail['no']]) }}", function (data) {
            if (data.status === 1) {
                $('.chat_window').html(data.content);
                $(".chat_window").animate({ scrollTop:$(".chat_window").prop('scrollHeight')}, 1000);
            }
        });
    }

    // 加载截图
    function loadImage()
    {
        $.get("{{ route('frontend.workbench.leveling.leave-image', ['order_no' => $detail['no']]) }}", function (data) {
            if (data.status === 1) {
                $('#leave-image').html(data.content);
            }
        });
    }

    // 图片预览
    $('#leave-image').on('click', '.show-image', function () {
        //iframe层
        layer.open({
            type: 1,
            title: '图片预览',
            area: ['50%', '80%'],
            content: '<img  src="'+ $(this).data('url') + '"  width="100%" />'
        });
    });

</script>
@endsection
