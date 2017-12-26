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
    </style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')
    <form class="layui-form" id="query_form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">平台单号：</label>
                <div class="layui-input-inline">
                    <input type="tel" name="id" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">游戏：</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">订单状态：</label>
                <div class="layui-input-inline" style="">
                    <select name="quiz1">
                        <option value="">请选择省</option>
                        <option value="浙江" selected="">浙江省</option>
                        <option value="你的工号">江西省</option>
                        <option value="你最喜欢的老师">福建省</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">加急订单：</label>
                <div class="layui-input-inline" style="">
                    <input type="checkbox" name="like1[write]" lay-skin="primary">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">发单客服：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="createDate" autocomplete="off" class="layui-input fsDate" daterange="1"
                           placeholder=" - " lay-key="1">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">标签：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="createDate" autocomplete="off" class="layui-input fsDate" daterange="1"
                           placeholder=" - " lay-key="1">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">发布时间：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="createDate" autocomplete="off" class="layui-input fsDate" daterange="1"
                           placeholder=" - " lay-key="1">
                </div>
                <div class="layui-input-inline" style="">
                    <input type="text" name="createDate" autocomplete="off" class="layui-input fsDate" daterange="1"
                           placeholder=" - " lay-key="1">
                </div>
                <button class="layui-btn layui-btn-normal " type="button" function="query">查询</button>
                <button class="layui-btn layui-btn-normal " type="button" function="query">导出</button>
            </div>
        </div>
    </form>

    <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="need">全部 <span
                        class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span>
            </li>
            <li class="" lay-id="ing">未接单</li>
            <li class="" lay-id="finish">代练中</li>
            <li class="" lay-id="after-sales">待验收</li>
            <li class="" lay-id="cancel">撤销中</li>
            <li class="" lay-id="cancel">仲裁中</li>
            <li class="" lay-id="cancel">异常</li>
            <li class="" lay-id="cancel">锁定</li>
            <li class="" lay-id="cancel">已撤销</li>
            <li class="" lay-id="cancel">已结算</li>
            <li class="" lay-id="cancel">已仲裁</li>
            <li class="" lay-id="cancel">已下架</li>
            <li class="" lay-id="market">强制撤销 <span
                        class="layui-badge layui-bg-blue market-order-quantity @if(marketOrderQuantity() == 0) layui-hide  @endif">{{ marketOrderQuantity() }}</span>
            </li>
        </ul>
        <div class="layui-tab-content"></div>
    </div>

    <table class="layui-hide layui-form" id="orer-list" lay-filter="user" lay-size="sm"></table>
@endsection

<!--START 底部-->
@section('js')
    <script type="text/html" id="operation">
        <a class="layui-btn layui-btn layui-btn-normal  " lay-event="edit">详情</a>
        <div class="layui-input-inline">
            <select  lay-filter="order-operation">
                <option value="">请选择操作</option>
                <option value="2">1asdfasdf</option>
                <option value="3">2asdfasdf</option>
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
                    table = layui.table;
            // 当前tab 所在位置
            var status = 0;
            //方法级渲染
            table.render({
                elem: '#orer-list',
                url: '{{ route('frontend.workbench.leveling.order-list') }}',
                method: 'post',
                size: 'sm',
                cols: [[
//                    {checkbox: true, fixed: true},
                    {
                        title: '订单号',
                        fixed: 'left',
                        width: '220',
                        templet: '#noTemplate',
                        style: "height: 38px;line-height: 18px;"
                    },
                    {field: 'order_source', title: '订单来源', width: '100'},
                    {field: 'lable', title: '标签', width: '150'},
                    {field: 'cstomer_service_remark', title: '客服备注', width: '250'},
                    {field: 'game_leveling_title', title: '代练标题', width: '250'},
                    {title: '游戏/区/服', templet: '#gameTemplate', width: '150'},
                    {field: 'game_leveling_type', title: '代练类型', width: '100'},
                    {title: '账号/密码', templet: '#accountPasswordTemplate', width: '200'},
                    {field: 'role', title: '角色名称', width: '100'},
                    {field: 'status', title: '订单状态', width: '120'},
                    {field: 'nickname', title: '打手呢称', width: '120'},
                    {field: 'original_amount', title: '来源价格', width: '100'},
                    {field: 'amount', title: '发单价', width: '100'},
                    {title: '操作',fixed:'right', width: '230', toolbar: '#operation'}
                ]],
                id: 'order',
                page: true,
                height: 'full-200'
            });

            //监听Tab切换，以改变地址hash值
            element.on('tab(order-list)', function () {
//                location.hash = 'test1='+ this.getAttribute('lay-id');
                var status = this.getAttribute('lay-id');
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

            function changeStyle(index) {
                var getTpl = changeStyleTemplate.innerHTML, view = $('body');
                layTpl(getTpl).render(index, function(html){
                    view.append(html);
                });
            }
        });
    </script>
@endsection