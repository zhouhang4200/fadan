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
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
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
                    <input type="text" name="foreign_order_no" autocomplete="off" class="layui-input">
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
            <div class="layui-inline">
                <label class="layui-form-mid">加急订单：</label>
                <div class="layui-input-inline" style="">
                    <input type="checkbox" name="urgent_order" lay-skin="primary" value="0" lay-filter="urgent_order">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">发单客服：</label>
                <div class="layui-input-inline" style="">
                    <select name="game" lay-search="">
                        <option value="">请选择或输入</option>
                        @forelse($employee as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">订单标签：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="label" autocomplete="off" class="layui-input fsDate"   lay-key="1">
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
                <button class="layui-btn layui-btn-normal " type="button" function="query" lay-submit="" lay-filter="">导出</button>
            </div>
        </div>
    </form>

    <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="need">全部 <span  class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span></li>
            <li class="" lay-id="1">未接单</li>
            <li class="" lay-id="13">代练中</li>
            <li class="" lay-id="14">待验收</li>
            <li class="" lay-id="15">撤销中</li>
            <li class="" lay-id="16">仲裁中</li>
            <li class="" lay-id="17">异常</li>
            <li class="" lay-id="18">锁定</li>
            <li class="" lay-id="19">已撤销</li>
            <li class="" lay-id="20">已结算</li>
            <li class="" lay-id="21">已仲裁</li>
            <li class="" lay-id="22">已下架</li>
            <li class="" lay-id="23">强制撤销 <span class="layui-badge layui-bg-blue market-order-quantity @if(marketOrderQuantity() == 0) layui-hide  @endif">{{ marketOrderQuantity() }}</span>
            </li>
        </ul>
        <div class="layui-tab-content"></div>
    </div>

    <table class="layui-hide layui-form" id="orer-list" lay-filter="user" lay-size="sm"></table>

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
@endsection

<!--START 底部-->
@section('js')
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/html" id="operation">
        <a href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no  }}" class="layui-btn layui-btn layui-btn-normal  " lay-event="edit">详情</a>
        <div class="layui-input-inline">
            <select  lay-filter="order-operation">
                <option value="">请选择操作</option>
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
                <option value="13" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">重发</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="assa" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">加急</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="asda" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消加急</option>
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
                    @{{# }  }}
                @{{# } else {  }}
                    @{{# if (d.consult == 2 && d.status == 15) {  }}
                    <option value="cancelRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消撤销</option>
                    @{{# } else if (d.consult == 1 && (d.status == 15 || d.status == 16)) {  }}
                    <option value="agreeRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">同意撤销</option>
                    @{{# }  }}
                @{{# }  }}

                @{{# if (d.status == 13 || d.status == 14 || d.status == 17 || d.status == 18) {  }}
                <option value="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</option>
                @{{# }  }}
                
                @{{# if (d.status == 15) {  }}
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
                <option value="send-message" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">发短信</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="message" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">留言</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="operation-record" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">操作记录</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="wang-wang" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">联系旺旺号</option>
                @{{# }  }}

                @{{# if (d.master && (d.status == 1 || d.status == 22)) {  }}
                <option value="delete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">删除</option>
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
        千手：@{{ d.no }} @{{# if(d.urgent_order == 1) { }}<span style="color:red">急</span> @{{#  } }} <br/>
        外部： @{{ d.foreign_order_no }}
    </script>
    <script type="text/html" id="gameTemplate">
        @{{ d.game_name }} <br/>
        @{{ d.region }} / @{{ d.serve }}
    </script>
    <script type="text/html" id="accountPasswordTemplate">
        @{{ d.account }} <br/>
        @{{ d.password }}
    </script>
    <script type="text/html" id="changeStyleTemplate">
        <style>
            .laytable-cell-@{{ d  }}-0, .laytable-cell-@{{ d  }}-5, .laytable-cell-@{{ d  }}-7 {
                height: 40px !important;
            }
            .laytable-cell-@{{ d  }}-13 {
                height: 40px !important;
                line-height: 40px !important;
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

        });
    </script>
@endsection