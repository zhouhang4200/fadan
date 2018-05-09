@extends('frontend.v1.layouts.app')

@section('title', '工作台 - 代练')

@section('css')
    <style>
        td .laytable-cell-1-no,
        td .laytable-cell-1-button{
            display: block;
            height: 50px;
            line-height: 50px;
            word-break: break-all;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-left: 15px;
        }
        .layui-card .layui-tab-brief .layui-tab-title li {
            margin: 0;
        }
        .layui-laypage .layui-laypage-curr .layui-laypage-em {
            background-color: #ff8500;
        }
        /*重写button 样式*/
        .qs-btn {
            height: 30px;
            line-height: 30px;
            width: 80px;
            padding: 0;
        }
    </style>
@endsection

@section('main')
    <div class="layui-card-header" style="padding-top: 20px;">
        <div class="layui-row layui-col-space5">
            <form class="layui-form" action="">
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="text-align: left;padding: 9px 0">订单单号</label>
                        <div class="layui-input-block" style="margin-left: 90px;">
                            <input type="text" name="no" lay-verify="title" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">玩家旺旺</label>
                        <div class="layui-input-block">
                            <input type="text" name="wang_wang" lay-verify="title" autocomplete="off" placeholder="请输入玩家旺旺" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">天猫状态</label>
                        <div class="layui-input-block">
                            <select name="taobao_status" lay-search="">
                                <option value="">请选择</option>
                                <option value="1">买家付完款</option>
                                <option value="2">交易成功</option>
                                <option value="3">买家发起退款</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">代练游戏</label>
                        <div class="layui-input-block">
                            <select name="game_id" lay-search="">
                                <option value="">请选择游戏</option>
                                @foreach($game as  $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label"  style="text-align: left;padding: 9px 0">发单客服</label>
                        <div class="layui-input-block" style="margin-left: 90px;">
                            <select name="customer_service_name" lay-search="">
                                <option value="">请选择</option>
                                @forelse($employee as $item)
                                    <option value="{{ $item->username }}">{{ $item->username }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">代练平台</label>
                        <div class="layui-input-block">
                            <select name="platform">
                                <option value="">全部</option>
                                @foreach (config('partner.platform') as $key => $value)
                                    <option value="{{ $key }}">{{ $value['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md6">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">活动日期</label>
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test-laydate-start" name="start_date" placeholder="开始日期">
                            </div>
                            <div class="layui-form-mid">
                                -
                            </div>
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test-laydate-end" name="end_date" placeholder="结束日期">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin-left: 0px;">
                            <button class="qs-btn" lay-submit="" lay-filter="search">搜索</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="0">全部 <span  class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span></li>
                <li class="" lay-id="1">未接单</li>
                <li class="" lay-id="13">代练中</li>
                <li class="" lay-id="14">待验收
                    <span class="qs-badge quantity-14 layui-hide">1</span>
                </li>
                <li class="" lay-id="15">撤销中
                    <span class="  quantity-15 layui-hide"></span>
                </li>
                <li class="" lay-id="16">仲裁中
                    <span class="quantity-16 layui-hide"></span>
                </li>
                <li class="" lay-id="17">异常
                    <span class="quantity-17 layui-hide"></span>
                </li>
                <li class="" lay-id="18">锁定
                    <span class="quantity-18 layui-hide"></span>
                </li>
                <li class="" lay-id="19">已撤销</li>
                <li class="" lay-id="20">已结算</li>
                <li class="" lay-id="21">已仲裁</li>
                <li class="" lay-id="22">已下架</li>
                <li class="" lay-id="23">强制撤销</li>
                <li class="" lay-id="24">已撤单</li>
            </ul>
            <div style="height: 10px"></div>
        </div>
        <div id="order-list" lay-filter="order-list">
        </div>
    </div>
@endsection

<!--START 底部-->
@section('js')
    <script type="text/html" id="operation">

        @{{# if (d.master) {  }}

            @{{# if (d.status == 1) {  }}
                <button class="qs-btn" value="offSale" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">下架</button>
                <button class="qs-btn" value="delete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤单</button>
            @{{# } else if (d.status == 13) {  }}
                <button class="qs-btn" value="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
                <button class="qs-btn" value="applyArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请仲裁</button>
            @{{# } else if (d.status == 14) {  }}
                <button class="qs-btn" value="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
                <button class="qs-btn" value="applyArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请仲裁</button>
            @{{# } else if (d.status == 15) {  }}

                @{{# if (d.consult == 1) {  }}
                    <button class="qs-btn" value="cancelRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消撤销</button>
                @{{# } else if (d.consult == 2) {  }}
                    <button value="agreeRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">同意撤销</button>
                @{{# } }}

                <button class="qs-btn" value="applyArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请仲裁</button>

            @{{# } else if (d.status == 16) {  }}

                @{{# if (d.complain == 1) {  }}
                    <button class="qs-btn" value="cancelArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消仲裁</button>
                @{{# } }}

                @{{# if (d.consult == 2) {  }}
                    <button value="agreeRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">同意撤销</button>
                @{{# }   }}

            @{{# } else if (d.status == 17) {  }}
                <button class="qs-btn" value="lock" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">锁定</button>
                <button class="qs-btn" value="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
            @{{# } else if (d.status == 18) {  }}
                <button class="qs-btn" value="cancelLock" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消锁定</button>
                <button class="qs-btn" value="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
            @{{# } else if (d.status == 19 || d.status == 20 || d.status == 21) {  }}
                <button class="qs-btn" value="repeat" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">重发</button>
            @{{# } else if (d.status == 22) {  }}
                <button class="qs-btn" value="onSale" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">上架</button>
                <button class="qs-btn" value="delete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤单</button>
            @{{# } else if (d.status == 23) {  }}
                <button class="qs-btn" value="repeat" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">重发</button>
            @{{# }  }}

        @{{# } else {  }}


        @{{# }  }}
    </script>
    <script type="text/html" id="noTemplate">
        天猫：<a style="color:#1f93ff"  href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}"> @{{ d.source_order_no }}</a> <br/>
        @{{# if(d.third_name) { }}  @{{ d.third_name }}：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}"> @{{  d.third_order_no }} </a>  @{{#  } }}
    </script>
    <script type="text/html" id="wwTemplate">
        @{{# if(d.third_name) { }}
        <a  style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid=@{{ d.client_wang_wang }}&siteid=cntaobao&status=1&charset=utf-8"  target="_blank" title="@{{ d.client_wang_wang }}"> @{{ d.client_wang_wang }}</a><img
                src="/frontend/images/ww.png">
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
    <script type="text/html" id="accountPasswordTemplate">
        @{{ d.account }} <br/>
        @{{ d.password }}
    </script>
    <script type="text/html" id="changeStyleTemplate">
        <style>
            td .laytable-cell-@{{ d  }}-no {
                display: block;
                height: 50px;
                line-height: 25px;
                word-break: break-all;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                padding-left: 15px;
            }
            td .laytable-cell-@{{ d  }}-button{
                display: block;
                height: 50px;
                line-height: 50px;
                word-break: break-all;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                padding-left: 15px;
            }
            .layui-laypage .layui-laypage-curr .layui-laypage-em {
                background-color: #ff8500;
            }
        </style>
    </script>
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form,
                    layer = layui.layer,
                    element = layui.element,
                    laydate = layui.laydate,
                    layTpl = layui.laytpl,
                    table = layui.table;
            // 是否将天猫订单发货
            var delivery = 0;

            laydate.render({elem: '#start-date'});
            laydate.render({elem: '#end-date'});

            // 状态切换
            element.on('tab(order-list)', function () {
                $('form').append('<input name="status" type="hidden" value="' + this.getAttribute('lay-id')  + '">');
                var condition = {};
                var formCondition = $('form').serializeArray();
                $.each(formCondition, function() {
                    condition[this.name] = this.value;
                });
                reloadOrderList(condition);
            });
            // 弹窗的取消按钮
            $('.cancel').click(function () {
                layer.closeAll();
            });
            // 搜索
            form.on('submit(search)', function (data) {
                reloadOrderList(data.field);
                return false;
            });
            // 订单表格重载
            function reloadOrderList(condition) {
                //执行重载
                table.reload('order-list', {
                    where: condition,
                    height: 'full-420',
                    page: {
                        curr: 1
                    },
                    done: function(res, curr, count){
                        console.log(layui.table.index);
                        changeStyle(layui.table.index);
                        layui.form.render();
                    }
                });
            }
            // 重新渲染后重写样式
            function changeStyle(index) {
                var getTpl = changeStyleTemplate.innerHTML, view = $('body');
                layTpl(getTpl).render(index, function(html){
                    view.append(html);
                });
            }
            // 备注编辑
            table.on('edit(order-list)', function(obj){
                var value = obj.value //得到修改后的值
                        ,data = obj.data //得到所在行所有键值
                        ,field = obj.field; //得到字段
                layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
            });
            // 加载数据
            table.render({
                elem: '#order-list',
                url: '{{ route('frontend.workbench.leveling.order-list') }}',
                method: 'post',
                cols: [[
                    {field: 'no', title: '订单号', width: 270, templet: '#noTemplate', style:"height: 50px;line-height: 25px;"},
                    {field: 'status_text', title: '订单状态', width: 80, style:"height: 50px;line-height: 50px;"},
                    {field: 'seller_nick', title: '玩家旺旺', minWidth: 150},
                    {field: 'customer_service_remark', title: '客服备注', minWidth: 160,edit: 'text'},
                    {field: 'game_leveling_title', title: '代练标题', width: 80},
                    {field: 'game_name', title: '游戏/区/服', width: 100},
                    {field: 'account_password', title: '账号/密码', width: 100},
                    {field: 'role', title: '角色名称', width: 100},
                    {field: 'amount', title: '代练价格', width: 100},
                    {field: 'efficiency_deposit', title: '效率保证金', width: 100},
                    {field: 'security_deposit', title: '安全保证金', width: 100},
                    {field: 'created_at', title: '发单时间', width: 100},
                    {field: 'receiving_time', title: '接单时间', width: 100},
                    {field: 'leveling_time', title: '代练时间', width: 100},
                    {field: 'left_time', title: '剩余时间', width: 100},
                    {field: 'hatchet_man_qq', title: '打手QQ', width: 100},
                    {field: 'hatchet_man_phone', title: '打手电话', width: 100},
                    {field: 'city', title: '号主电话', width: 100},
                    {field: 'source_price', title: '来源价格', width: 100},
                    {field: 'payment_amount', title: '支付金额', width: 100},
                    {field: 'get_amount', title: '获得金额', width: 100},
                    {field: 'poundage', title: '手续费', width: 100},
                    {field: 'city', title: '利润', width: 100},
                    {field: 'customer_service_name', title: '发单客服', width: 100},
                    {field: 'button', title: '操作', width: 200, fixed: 'right', style:"height: 50px;line-height: 50px;", toolbar: '#operation'}
                ]],
                height: 'full-420',
                page: {
                    layout: [ 'count', 'prev', 'page', 'next', 'skip'],
                    groups: 10,
                    prev: '上一页',
                    next: '下一页'
                }
            });
        });
    </script>
@endsection