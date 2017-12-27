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

    </style>
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
                    <select name="game">
                        <option value="">请选择省</option>

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

    <div class="consult" style="display: none; padding: 10px">
        <div class="layui-tab-content">
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div style="width: 40%">
                    <div class="layui-form-item">
                        <label class="layui-form-label">协商返还代练费</label>
                        <div class="layui-input-block">
                            <input type="text" name="amount" lay-verify="required|number" value="" autocomplete="off" placeholder="请输入协商返还代练费" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">协商返还双金</label>
                        <div class="layui-input-block">
                            <input type="text" name="deposit" lay-verify="required|number" value="" autocomplete="off" placeholder="请输入协商返还双金" class="layui-input" style="width:400px">
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
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="complete" style="display: none; padding: 10px">
        <div class="layui-tab-content">
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div style="width: 40%">
                    <div class="layui-form-item">
                        <label class="layui-form-label">申诉理由</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入申诉理由" name="complain_message" lay-verify="required" class="layui-textarea" style="width:400px"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="complete">立即提交</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

<!--START 底部-->
@section('js')
    <script type="text/html" id="operation">
        <a href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no  }}" class="layui-btn layui-btn layui-btn-normal  " lay-event="edit">详情</a>
        <div class="layui-input-inline">
            <select  lay-filter="order-operation">
                <option value="">请选择操作</option>
                @{{# if (!d.master && d.status == 1) {  }}
                <option value="receive" data-no="@{{ d.no }}">接单</option>
                @{{# }  }}

                @{{# if (d.master && d.status == 22) {  }}
                    <option value="onSale" data-no="@{{ d.no }}">上架</option>
                @{{# }  }}
 
                @{{# if (d.master && d.status == 1) {  }}
                <option value="offSale" data-no="@{{ d.no }}">下架</option>
                @{{# }  }}

                @{{# if (d.master && (d.status == 14 || d.status == 15 || d.status == 16 || d.status == 17 || d.status == 18 || d.status == 19 || d.status == 20 || d.status == 21)) {  }}
                <option value="13" data-no="@{{ d.no }}">重发</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="assa" data-no="@{{ d.no }}">加急</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="asda" data-no="@{{ d.no }}">取消加急</option>
                @{{# }  }}

                @{{# if (d.master && (d.status == 13 || d.status == 14 || d.status == 17)) {  }}
                    <option value="lock" data-no="@{{ d.no }}">锁定</option>
                @{{# }  }}

                @{{# if (d.master && d.status == 18) {  }}
                <option value="cancelLock" data-no="@{{ d.no }}">取消锁定</option>
                @{{# }  }}
    
                @{{# if (d.master) {  }}
                    @{{# if (d.consult == 1 && d.status == 15) {  }}
                    <option value="cancelRevoke" data-no="@{{ d.no }}">取消撤销</option>
                    @{{# } else if (d.consult == 2 && (d.status == 15 || d.status == 16)) {  }}
                    <option value="agreeRevoke" data-no="@{{ d.no }}">同意撤销</option>
                    @{{# }  }}
                @{{# } else {  }}
                    @{{# if (d.consult == 2 && d.status == 15) {  }}
                    <option value="cancelRevoke" data-no="@{{ d.no }}">取消撤销</option>
                    @{{# } else if (d.consult == 1 && (d.status == 15 || d.status == 16)) {  }}
                    <option value="agreeRevoke" data-no="@{{ d.no }}">同意撤销</option>
                    @{{# }  }}
                @{{# }  }}

                @{{# if (d.status == 13 || d.status == 14 || d.status == 17 || d.status == 18) {  }}
                <option value="revoke" data-no="@{{ d.no }}">撤销</option>
                @{{# }  }}
                
                @{{# if (d.status == 15) {  }}
                <option value="applyArbitration" data-no="@{{ d.no }}">申请仲裁</option>
                @{{# }  }}
                
                @{{# if (d.master) {  }}
                    @{{# if (d.complain == 1 && d.status == 16) {  }}
                    <option value="cancelArbitration" data-no="@{{ d.no }}">取消仲裁</option>
                    @{{# }  }}
                @{{# } else {  }}
                    @{{# if (d.complain == 2 && d.status == 16) {  }}
                    <option value="cancelArbitration" data-no="@{{ d.no }}">取消仲裁</option>
                    @{{# }  }}
                @{{# }  }}

                @{{# if (d.master && d.status == 14) {  }}
                <option value="complete" data-no="@{{ d.no }}">完成</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="send-message" data-no="@{{ d.no }}">发短信</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="message" data-no="@{{ d.no }}">留言</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="operation-record" data-no="@{{ d.no }}">操作记录</option>
                @{{# }  }}

                @{{# if (d.master) {  }}
                <option value="wang-wang" data-no="@{{ d.no }}">联系旺旺号</option>
                @{{# }  }}

                @{{# if (d.master && (d.status == 1 || d.status == 22)) {  }}
                <option value="delete" data-no="@{{ d.no }}">删除</option>
                @{{# }  }}

                @{{# if (!d.master && (d.status == 13)) {  }}
                <option value="applyComplete" data-no="@{{ d.no }}">申请完成</option>
                @{{# }  }}

                @{{# if (!d.master && (d.status == 14)) {  }}
                <option value="cancelComplete" data-no="@{{ d.no }}">取消验收</option>
                @{{# }  }}
                @{{# if (!d.master && (d.status == 13)) {  }}
                <option value="abnormal" data-no="@{{ d.no }}">异常</option>
                @{{# }  }}
                @{{# if (!d.master && (d.status == 17)) {  }}
                <option value="cancelAbnormal" data-no="@{{ d.no }}">取消异常</option>
                @{{# }  }}
            </select>
        </div>
    </script>
    <script type="text/html" id="noTemplate">
        平台：@{{ d.no }} <br/>
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

            //方法级渲染
            table.render({
                elem: '#orer-list',
                url: '{{ route('frontend.workbench.leveling.order-list') }}',
                method: 'post',
                size: 'sm',
                cols: [[
                    {title: '订单号',width: '220',templet: '#noTemplate'},// ,fixed: 'left'
                    {field: 'order_source', title: '订单来源', width: '100'},
                    {field: 'lable', title: '标签', width: '150'},
                    {field: 'cstomer_service_remark', title: '客服备注', width: '250'},
                    {field: 'game_leveling_title', title: '代练标题', width: '250'},
                    {title: '游戏/区/服', templet: '#gameTemplate', width: '150'},
                    {field: 'game_leveling_type', title: '代练类型', width: '100'},
                    {title: '账号/密码', templet: '#accountPasswordTemplate', width: '200'},
                    {field: 'role', title: '角色名称', width: '100'},
                    {field: 'status_text', title: '订单状态', width: '120'},
                    {field: 'nickname', title: '打手呢称', width: '120'},
                    {field: 'original_amount', title: '来源价格', width: '100'},
                    {field: 'amount', title: '发单价', width: '100'},
                    {title: '操作', width: '230', toolbar: '#operation'}//fixed:'right',
                ]],
                id: 'order',
                page: true
//                height: 800 //固定值
//                height: 'full-200'
            });

            element.on('tab(order-list)', function () {
                 status = this.getAttribute('lay-id');
                //执行重载
                table.reload('order', {
                    where: {
                        status: status
                    },
                    height: 'full-200',
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
                        status: status,
                        no: data.field.no,
                        foreign_order_no: data.field.foreign_order_no,
                        game_id: data.field.game_id,
                        need: data.field.status,
                        wang_wang: data.field.wang_wang,
                        urgent_order: urgentOrder,
                        start_date: data.field.start_date,
                        end_date: data.field.end_date
                    },
                    done: function(res, curr, count){
                        changeStyle(layui.table.index);
                        layui.form.render();
                    }
                });
            });
            var userId = "{{ Auth::id() }}";
            // 对订单操作
            form.on('select(order-operation)', function (data) {
                var orderNo = $(data.elem).find("option:selected").attr("data-no");
                var keyWord = data.value;
                if (data.value == 'revoke') {
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '撤销',
                        area: ['550px', '450px'],
                        content: $('.consult')
                    });
                    form.on('submit(consult)', function(data){
                        $.post("{{ route('frontend.workbench.leveling.consult') }}", {
                            orderNo:orderNo, 
                            data:data.field
                        }, function (result) {
                            if (result.status == 1) {
                                $.post("{{ route('frontend.workbench.leveling.status') }}", {
                                    orderNo:orderNo, 
                                    userId:userId,
                                    keyWord:keyWord,
                                }, function (result) {
                                    if (result.status == 1) {
                                        layer.alert(result.message);
      
                                    } else {
                                        layer.alert(result.message);

                                    }
                                });
                            } else {
                                layer.alert(result.message);

                            }
                            layer.closeAll();
                        });
                        return false;
                    });
                    
                } else if (data.value == 'applyArbitration') {
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '申诉',
                        area: ['550px', '350px'],
                        content: $('.complete')
                    });
                    form.on('submit(complete)', function(data){
                        $.post("{{ route('frontend.workbench.leveling.complete') }}", {
                            orderNo:orderNo, 
                            data:data.field
                        }, function (result) {
                            if (result.status == 1) {
                                $.post("{{ route('frontend.workbench.leveling.status') }}", {
                                orderNo:orderNo, 
                                userId:userId,
                                keyWord:keyWord,
                                }, function (result) {
                                    if (result.status == 1) {
                                        layer.alert(result.message);
                    
                                    } else {
                                        layer.alert(result.message);
                         
                                    }
                                });
                            } else {
                                layer.alert(result.message);
                       
                            }
                            layer.closeAll();
                        });
                        return false;
                    });
                    
                } else {
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
                    });
                }
               
            });
            // 重新渲染后重写样式
            function changeStyle(index) {
                var getTpl = changeStyleTemplate.innerHTML, view = $('body');
                layTpl(getTpl).render(index, function(html){
                    view.append(html);
                });
            }
        });
    </script>
@endsection