@extends('frontend.v1.layouts.app')

@section('title', '工作台-代练订单')

@section('css')
    <style>
        .layui-laypage .layui-laypage-curr .layui-laypage-em {
            background-color: #ff8500;
        }
        .layui-tab {
            padding: 0;
        }
        .qs-btn-sm {
            height: 27px;
            line-height: 27px;
            vertical-align: top;
        }
        .layui-table-view .layui-table[lay-size=sm] .layui-table-cell {
            height: 28px;
            line-height: 28px;
        }
    </style>
@endsection

@section('main')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header" style="padding-top: 20px;">
                <div class="layui-row layui-col-space5">
                    <form class="layui-form" action="">
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="">订单单号</label>
                                <div class="layui-input-block" style="">
                                    <input type="text" name="tid" lay-verify="title" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">玩家旺旺</label>
                                <div class="layui-input-block">
                                    <input type="text" name="buyer_nick" lay-verify="title" autocomplete="off" placeholder="请输入玩家旺旺" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-col-md4">
                            <div class="layui-form-item">
                                <label class="layui-form-label">发布时间</label>
                                <div class="layui-input-block">
                                    <input type="text"  class="layui-input qsdate" id="test-laydate-start" name="start_date" placeholder="开始日期">
                                    <div class="layui-form-mid" style="float:none;display: inline-block;width: 8%;text-align: center;margin:0;">
                                        -
                                    </div>
                                    <input type="text" class="layui-input qsdate" id="test-laydate-end"  name="end_date" placeholder="结束日期">
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md2">
                            <div class="layui-form-item">
                                <div class="layui-input-block" style="margin-left: 40px;">
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
                        <li class="layui-this" lay-id="0">全部
                            <span  class="qs-badge quantity-9 layui-hide"></span>
                        </li>
                        <li class="" lay-id="0">待处理
                            <span class="qs-badge quantity-0 layui-hide"></span>
                        </li>
                        <li class="" lay-id="1">已发布
                            <span class="qs-badge quantity-1 layui-hide"></span>
                        </li>
                        <li class="" lay-id="2">已隐藏
                            <span class="qs-badge quantity-2 layui-hide"></span>
                        </li>
                    </ul>
                    <div style="height: 10px"></div>
                </div>
                <div id="order-list" lay-filter="order-list">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/html" id="operation">
        @{{# if (d.handle_status == 0) {  }}
        <a  class="qs-btn qs-btn-sm " data-opt="create" href="{{ route('frontend.workbench.leveling.create') }}?tid=@{{ d.tid }}&game_id=@{{ d.game_id }}">发布</a>
        <button  class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" data-opt="update" href="{{ route('frontend.workbench.leveling.wait-update') }}?id=@{{ d.id }}&status=2">隐藏</button>
        @{{# } else if (d.handle_status == 2) {  }}
        <button  class="qs-btn qs-btn-sm" data-opt="update" href="{{ route('frontend.workbench.leveling.wait-update') }}?id=@{{ d.id }}&status=0">显示</button>
        @{{# } else if (d.handle_status == 1) {  }}
        <a  class="qs-btn qs-btn-sm" data-opt="create" href="{{ route('frontend.workbench.leveling.create') }}?tid=@{{ d.id }}&game_id=@{{ d.game_id }}">发布</a>
        @{{# }  }}
    </script>
    <script type="text/html" id="wwTemplate">
        <a style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid=@{{ d.buyer_nick }}&siteid=cntaobao&status=1&charset=utf-8"  target="_blank" title="@{{ d.buyer_nick }}"><img
                    src="/frontend/images/ww.gif" alt="" width="20px">@{{ d.buyer_nick }}</a>
    </script>
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form, layer = layui.layer, element = layui.element, layTpl = layui.laytpl, table = layui.table;
            // 状态切换
            element.on('tab(order-list)', function () {
                $('form').append('<input name="status" type="hidden" value="' + this.getAttribute('lay-id')  + '">');
                reloadOrderList();
            });
            // 搜索
            form.on('submit(search)', function (data) {
                reloadOrderList(data.field);
                return false;
            });
            // 订单表格重载
            function reloadOrderList(parameter) {
                var condition = {};
                if (parameter == undefined) {
                    var formCondition = $('form').serializeArray();
                    $.each(formCondition, function() {
                        condition[this.name] = this.value;
                    });
                } else {
                    condition = parameter;
                }
                //执行重载
                table.reload('order-list', {
                    where: condition,
                    height: 'full-250',
                    page: {
                        curr: 1
                    },
                    done: function(res, curr, count){
                        setStatusNumber(res.status_count);
                        layui.form.render();
                    }
                });
            }
            // 设置订单状态数
            function setStatusNumber(parameter) {
                if (parameter.length == 0) {
                    $('.qs-badge').addClass('layui-hide');
                }
                $.each(parameter, function(key, val) {
                    var name = 'quantity-'  +  key;
                    if ($('span').hasClass(name) && val > 0) {
                        $('.' + name).html(val).removeClass('layui-hide');
                    } else {
                        $('.' + name).addClass('layui-hide');
                    }
                });
            }
            // 备注编辑
            table.on('edit(order-list)', function(obj){
                var value = obj.value, field = obj.field; // 修改后的值, 修改的字段
                $.post('{{ route("frontend.workbench.leveling.wait-remark") }}', {id:obj.data.id, field:field, value:value}, function (result) {
                }, 'json');
            });
            // 加载数据
            table.render({
                elem: '#order-list',
                url: '{{ route('frontend.workbench.leveling.wait-order-list') }}',
                method: 'post',
                cols: [[
                    {field: 'seller_nick', title: '店铺', width: 100},
                    {field: 'tid', title: '订单号', width: 200},
                    {field: 'game_name', title: '绑定游戏'},
                    {field: 'buyer_nick', title: '买家旺旺', templet: '#wwTemplate',width: 150},
                    {field: 'price', title: '购买单价'},
                    {field: 'num', title: '购买数量'},
                    {field: 'payment', title: '实付金额'},
                    {field: 'created', title: '下单时间',width: 180 },
                    {field: 'remark', title: '备注', edit:'text',width: 200 },
                    {field: 'button', title: '操作', width: 200,  toolbar: '#operation'}
                ]],
                height: 'full-250',
                size: 'sm', //小尺寸的表格
                page: {
                    layout: [ 'count', 'prev', 'page', 'next', 'skip'],
                    groups: 10,
                    prev: '上一页',
                    next: '下一页'
                },
                done: function(res, curr, count){
                    setStatusNumber(res.status_count);
                }
            });
            // 对订单操作
            $('.layui-card-body').on('click', '.qs-btn', function () {
                $.post($(this).attr('href'), {ad:1}, function () {
                    reloadOrderList();
                });
            });
        });
    </script>
@endsection