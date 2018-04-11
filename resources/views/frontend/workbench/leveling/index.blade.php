@extends('frontend.layouts.app')

@section('title', '工作台 - 代练')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .wrapper {
            width: 1600px;
        }
        .main .right {
            width: 1430px;
        }
        .layui-laypage-skip input {
            height: 27px !important;
        }
        .laytable-cell-1-0, .laytable-cell-1-5, .laytable-cell-1-7{
            height: 40px !important;
        }
        /*th:nth-child(1) > div, th:nth-child(6) > div, th:nth-child(8) > div {
            line-height: 40px !important;
        }*/
        .laytable-cell-1-22{
            height: 40px !important;
            line-height: 40px !important;
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
        #info .layui-form-item .layui-input-block{
            margin-left: 200px;
        }
        #info .layui-form-item .layui-form-label{
           width: 160px;
        }
        /*下拉菜单*/
        .layui-table-fixed .layui-table-body {
            overflow: visible;
        }
        .layui-table-box, .layui-table-view {
            position: relative;
            overflow: unset;
        }
        tr > .laytable-cell-2-label {
            padding: 0 !important;
        }
        .layui-table-view .layui-table[lay-size=sm] .layui-table-cell {
            height: 40px;
            line-height: 20px;
        }
        .layui-form-select dl {
            max-height: 500px;
        }
        .layui-table-header  .layui-table-cell {
            height: 40px !important;
            line-height: 40px !important;
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
                <label class="layui-form-mid">&nbsp;&nbsp;&nbsp; 订单号：</label>
                <div class="layui-input-inline">
                    <input type="text" name="no" autocomplete="off" class="layui-input">
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
                            <option value="{{ $key }}">{{ $value }}</option>
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
                        <option value="">请选择或输入</option>
                        @forelse($employee as $item)
                            <option value="{{ $item->username }}">{{ $item->username }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">接单平台：</label>
                <div class="layui-input-inline" style="">
                    <select name="label">
                        <option value="">全部</option>
                        @foreach (config('partner.platform') as $key => $value)
                            <option value="{{ $key }}">{{ $value['name'] }}</option>
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
        <div class="layui-tab-content"></div>
    </div>

    <table class="layui-hide layui-form" id="orer-list" lay-filter="user" lay-size="sm"></table>

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
                    <option value="">选择模版</option>
                    @forelse($smsTemplate as $item)
                        <option value="{{ $item->contents }}" data-template="">{{ $item->name }}</option>
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
    <script type="text/html" id="operation">
        {{--<a href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no  }}" class="layui-btn layui-btn layui-btn-normal  " lay-event="edit">详情</a>--}}
        <div class="layui-input-inline">
            <select  lay-filter="order-operation">
                <option value="">请选择操作</option>
                    <option value="detail" data-no="@{{ d.no }}" >详情</option>
                @{{# if (!d.master && d.status == 1) {  }}
                    <option value="receive" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">接单</option>
                @{{# }  }}

                @{{# if (d.master && d.status == 22) {  }}
                    <option value="onSale" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">上架</option>
                @{{# }  }}

                @{{# if (d.master && d.status == 1) {  }}
                <option value="offSale" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">下架</option>
                @{{# }  }}

                @{{# if (d.master && (d.status == 14 || d.status == 15 || d.status == 16 || d.status == 17 || d.status == 18 || d.status == 19 || d.status == 20 || d.status == 21)) {  }}
                <option value="repeat" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">重发</option>
                @{{# }  }}

                @{{# if (d.master && d.urgent_order != 1) {  }}
                <option value="urgent" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">加急</option>
                @{{# }  }}

                @{{# if (d.master && d.urgent_order == 1) {  }}
                <option value="unUrgent" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消加急</option>
                @{{# }  }}

                @{{# if (d.master && (d.status == 13 || d.status == 14 || d.status == 17)) {  }}
                    <option value="lock" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">锁定</option>
                @{{# }  }}

                @{{# if (d.master && d.status == 18) {  }}
                <option value="cancelLock" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消锁定</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                    @{{# if (d.consult == 1 && d.status == 15) {  }}
                    <option value="cancelRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消撤销</option>
                    @{{# } else if (d.consult == 2 && (d.status == 15 || d.status == 16)) {  }}
                    <option value="agreeRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">同意撤销</option>
                    <option value="refuseRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">不同意撤销</option>
                    @{{# }  }}
                @{{# } else {  }}
                    @{{# if (d.consult == 2 && d.status == 15) {  }}
                    <option value="cancelRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消撤销</option>
                    @{{# } else if (d.consult == 1 && (d.status == 15 || d.status == 16)) {  }}
                    <option value="agreeRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">同意撤销</option>
                    <option value="refuseRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">不同意撤销</option>
                    @{{# }  }}
                @{{# }  }}

                @{{# if (d.status == 13 || d.status == 14 || d.status == 17 || d.status == 18) {  }}
                <option value="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</option>
                @{{# }  }}

                @{{# if (d.status == 13 || d.status == 14 || d.status == 15) {  }}
                <option value="applyArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请仲裁</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                    @{{# if (d.complain == 1 && d.status == 16) {  }}
                    <option value="cancelArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消仲裁</option>
                    @{{# }  }}
                @{{# } else {  }}
                    @{{# if (d.complain == 2 && d.status == 16) {  }}
                    <option value="cancelArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消仲裁</option>
                    @{{# }  }}
                @{{# }  }}

                @{{# if (d.master && d.status == 14) {  }}
                <option value="complete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">完成</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="sendSms" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">发短信</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="message" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">留言</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="operationRecord" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">操作记录</option>
                @{{# }  }}

                @{{# if (d.master && d.client_wang_wang) {  }}
                <option value="wangWang" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}" data-wang-wang="@{{ d.client_wang_wang }}">联系旺旺号</option>
                @{{# }  }}

                @{{# if (d.master && (d.status == 1 || d.status == 22)) {  }}
                <option value="delete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤单</option>
                @{{# }  }}

                @{{# if (!d.master && (d.status == 13)) {  }}
                <option value="applyComplete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请完成</option>
                @{{# }  }}

                @{{# if (!d.master && (d.status == 14)) {  }}
                <option value="cancelComplete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消验收</option>
                @{{# }  }}
                @{{# if (!d.master && (d.status == 13)) {  }}
                <option value="abnormal" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">异常</option>
                @{{# }  }}
                @{{# if (!d.master && (d.status == 17)) {  }}
                <option value="cancelAbnormal" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消异常</option>
                @{{# }  }}
            </select>
        </div>
    </script>
    <script type="text/html" id="noTemplate">
        {{--千手：@{{ d.no }} @{{# if(d.urgent_order == 1 && d.master) { }}<span style="color:red">急</span> @{{#  } }} <br/>--}}
        天猫：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}"> @{{ d.source_order_no }}</a> <br/>
        @{{# if(d.third_name) { }}  @{{ d.third_name }}：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}"> @{{  d.third_order_no }} </a>  @{{#  } }}
    </script>
    <script type="text/html" id="wwTemplate">
        @{{# if(d.third_name) { }}
        <a  style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid=@{{ d.client_wang_wang }}&siteid=cntaobao&status=1&charset=utf-8"  target="_blank" title="@{{ d.client_wang_wang }}"> @{{ d.client_wang_wang }}</a><img
                src="/frontend/images/ww.png" alt="">
        @{{#  } }}
    </script>
    <script type="text/html" id="gameTemplate">
        @{{ d.game_name }} <br/>
        @{{ d.region }} / @{{ d.serve }}
    </script>
    <script type="text/html" id="accountPasswordTemplate">
        @{{ d.account }} <br/>
        @{{ d.password }}
    </script>
    <script type="text/html" id="getAmountTemplate">
        @{{ d.get_amount ? d.get_amount : '' }} <br/>
    </script>
    <script type="text/html" id="labelTemplate">
        @{{# if (d.label == '红色') { }}
            <div style="height:100%;width:100%;background-color: #ff6159"></div>
        @{{# } else if(d.label == '橙色') { }}
            <div style="height:100%;width:100%;background-color: #f9a749"></div>
        @{{# } else if(d.label == '黄色') { }}
            <div style="height:100%;width:100%;background-color: #f4cf54"></div>
        @{{# } else if(d.label == '绿色') { }}
            <div style="height:100%;width:100%;background-color: #69cd5d"></div>
        @{{# } else if(d.label == '蓝色') { }}
            <div style="height:100%;width:100%;background-color: #48b7f2"></div>
        @{{# } else if(d.label == '紫色') { }}
            <div style="height:100%;width:100%;background-color: #d285df"></div>
        @{{# } else if(d.label == '灰色') { }}
            <div style="height:100%;width:100%;background-color: #a5a5a7"></div>
        @{{# } }}
    </script>
    <script type="text/html" id="changeStyleTemplate">
        <style>
            .laytable-cell-@{{ d  }}-0, .laytable-cell-@{{ d  }}-5, .laytable-cell-@{{ d  }}-7 {
                height: 40px !important;
            }
            .laytable-cell-@{{ d  }}-22 {
                height: 40px !important;
                line-height: 40px !important;
            }
            .layui-table-fixed .layui-table-body {
                overflow: visible;
            }
            .layui-table-box, .layui-table-view {
                position: relative;
                overflow: unset;
            }
            .layui-table-mend {
                width：0 !important;
            }
        </style>
    </script>
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form,
                    layer = layui.layer,
                    layTpl = layui.laytpl,
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

            //方法级渲染
            table.render({
                elem: '#orer-list',
                url: '{{ route('frontend.workbench.leveling.order-list') }}',
                method: 'post',
                size: 'sm',
                cols: [[
                    {title: '订单号',width: '225',templet: '#noTemplate'},// ,fixed: 'left'
                    {title: '号主旺旺',width: '150',templet: '#wwTemplate'},// ,fixed: 'left'
//                    {field: 'client_wang_wang', title: '号主旺旺', width: '150'},
                    {field: 'customer_service_remark', title: '客服备注', width: '150'},
                    {field: 'game_leveling_title', title: '代练标题', width: '250'},
                    {title: '游戏/区/服', templet: '#gameTemplate', width: '150'},
                    {title: '账号/密码', templet: '#accountPasswordTemplate', width: '100'},
                    {field: 'role', title: '角色名称', width: '100'},
                    {field: 'status_text', title: '订单状态', width: '120'},
                    {field: 'amount', title: '代练价格', width: '80'},
                    {field: 'efficiency_deposit', title: '效率保证金', width: '80'},
                    {field: 'security_deposit', title: '安全保证金', width: '80'},
                    {field: 'created_at', title: '发单时间', width: '150'},
                    {field: 'receiving_time', title: '接单时间', width: '150'},
                    {field: 'leveling_time', title: '代练时间', width: '80'},
                    {field: 'left_time', title: '剩余时间', width: '120'},
                    {field: 'hatchet_man_name', title: '打手呢称', width: '120'},
                    {field: 'hatchet_man_phone', title: '打手电话', width: '120'},
                    {field: 'client_phone', title: '号主电话', width: '120'},
                    {field: 'original_amount', title: '来源价格', width: '100'},
                    {field: 'payment_amount', title: '支付金额', width: '80'},
                    {field: 'get_amount', title: '获得金额', width: '80'},
                    {field: 'poundage', title: '手续费', width: '80'},
                    {field: 'profit', title: '利润', width: '80'},
                    {field: 'customer_service_name', title: '发单客服', width: '80'},
                    {title: '操作',fixed:'right',width: '230', toolbar: '#operation'}//fixed:'right',
                ]],
                id: 'order',
                height: 200,
                page: {
                    layout: [ 'count', 'prev', 'page', 'next', 'skip'],
                    groups: 10,
                    prev: '上一页',
                    next: '下一页'
                }
            });
            // 根据状态获取订单
            element.on('tab(order-list)', function () {
                 status = this.getAttribute('lay-id');
                // 清空角标
                $.post('{{ route("frontend.workbench.clear-count") }}', {status:status}, function () {
                }, 'json');
                //执行重载
                table.reload('order', {
                    where: {
                        status: status
                    },
                    done: function(res, curr, count){
                        changeStyle(layui.table.index);
                        layui.form.render();
                    }
                });
            });
            form.on('checkbox(urgent_order)', function(data){
                urgentOrder = data.elem.checked ? 1 : 0;
            });

            // 搜索
            form.on('submit(search)', function (data) {
                table.reload('order', {
                    where: {
                        page: 1,
                        status: status,
                        no: data.field.no,
                        source_order_no: data.field.source_order_no,
                        game_id: data.field.game_id,
                        need: data.field.status,
                        wang_wang: data.field.wang_wang,
                        urgent_order: urgentOrder,
                        start_date: data.field.start_date,
                        end_date: data.field.end_date,
                        label: data.field.label,
                        customer_service_name: data.field.customer_service_name
                    },
                    done: function(res, curr, count){
                        changeStyle(layui.table.index);
                        layui.form.render();
                    }
                });
            });
            var userId = "{{ Auth::id() }}";

            function reload() {
                //执行重载
                table.reload('order', {
                    where: {
                        status: status
                    },
                    done: function(res, curr, count){
                        changeStyle(layui.table.index);
                        layui.form.render();
                    }
                });
            }
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
            // 发送短信
            form.on('submit(confirm-send-sms)', function (data) {
                $.post('{{ route('frontend.workbench.leveling.send-sms') }}', {no:data.field.no, contents:data.field.contents},function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                }, 'json');
                return false;
            });
            // 重新渲染后重写样式
            function changeStyle(index) {
                var getTpl = changeStyleTemplate.innerHTML, view = $('body');
                layTpl(getTpl).render(index, function(html){
                    view.append(html);
                });
            }

            // 导出
            form.on('submit(export)', function (data) {
                var fields = data.field;
                var datas = '?no=' + fields.no+'&source_order_no='+fields.source_order_no+'&gameId='+fields.game_id+'&wangWang='+fields.wang_wang+'&startDate='+fields.start_date+'&endDate='+fields.end_date+'&status='+status+'&urgentOrder='+urgentOrder;
                window.location.href="{{ route('frontend.workbench.leveling.excel') }}"+datas;
            });
            // 选择短信模板
            form.on('select(chose-sms-template)', function(data){
                $('textarea[name=contents]').val(data.value);
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
