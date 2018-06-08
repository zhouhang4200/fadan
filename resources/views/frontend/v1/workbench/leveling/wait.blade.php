@extends('frontend.v1.layouts.app')

@section('title', '工作台-代练订单')

@section('css')
    <style>
        .layui-laypage .layui-laypage-curr .layui-laypage-em {
            background-color: #ff8500;
        }
        .layui-form-item {
            margin-bottom: 12px;
        }
        .layui-card-body {
            padding-top: 0;
        }
        .layui-tab {
            padding: 0;
            font-size: 12px;
        }
        .layui-card-header {
            height: auto;
            border-bottom: none;
            padding-bottom: 0;
        }

        .layui-tab-title li{
            min-width: 50px;
            font-size: 12px;
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
        /* 改写header高度 */
        .layui-card-header {
            font-size:12px;
        }
    </style>
@endsection

@section('main')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header" style="padding-top: 20px;">
                <div class="layui-row layui-col-space5">
                    <form class="layui-form" action="">
                        <input type="hidden" name="status" value="0">
                        <div class="layui-col-md3 first">
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
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">绑定游戏</label>
                                <div class="layui-input-block">
                                    <select name="game_id" lay-filter="game" lay-search="">
                                        <option value="">请选择</option>
                                        @forelse($games as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3" id="type">
                            <div class="layui-form-item">
                                <label class="layui-form-label">订单类型</label>
                                <div class="layui-input-block">
                                    <select name="type" lay-filter="" lay-search="">
                                        <option value="">请选择</option>
                                        <option value="0">普通订单</option>
                                        <option value="1">推荐号</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md4 first">
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
                        <li class="" lay-id="99">全部
                            <span  class="qs-badge quantity-9 layui-hide"></span>
                        </li>
                        <li class="layui-this" lay-id="0">待处理
                            <span class="qs-badge quantity-0 layui-hide"></span>
                        </li>
                        <li class="" lay-id="1">已发布
                            <span class="qs-badge quantity-1 layui-hide"></span>
                        </li>
                        <li class="" lay-id="2">已隐藏
                            <span class="qs-badge quantity-2 layui-hide"></span>
                        </li>
                        <button class="qs-btn qs-btn-message send"  style="float: right" lay-submit="" lay-filter="send">合并发布</button>
                    </ul>
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
        <a  class="qs-btn qs-btn-sm create" style="width: 60px" data-opt="create" href="{{ route('frontend.workbench.leveling.create') }}?tid=@{{ d.tid }}&game_id=@{{ d.game_id }}"  data-tid="@{{ d.tid }}" data-click-time="@{{ d.time}}" data-status="1"  target="_blank">发布</a>
        <button  class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table update" style="width: 60px" data-opt="update" href="{{ route('frontend.workbench.leveling.wait-update') }}?id=@{{ d.id }}&status=2">隐藏</button>
        @{{# } else if (d.handle_status == 2) {  }}
        <button  class="qs-btn qs-btn-sm update" style="width: 60px" data-opt="update" href="{{ route('frontend.workbench.leveling.wait-update') }}?id=@{{ d.id }}&status=0">显示</button>
        @{{# } else if (d.handle_status == 1) {  }}
        <a  class="qs-btn qs-btn-sm create" style="width: 60px" data-opt="create" href="{{ route('frontend.workbench.leveling.create') }}?tid=@{{ d.tid }}&game_id=@{{ d.game_id }}" data-tid="@{{ d.tid }}" data-click-time="@{{ d.time}}" data-status="1"  target="_blank">发布</a>
        @{{# }  }}
    </script>
    <script type="text/html" id="wwTemplate">
        <a style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid=@{{ d.buyer_nick }}&siteid=cntaobao&status=1&charset=utf-8"  target="_blank" title="@{{ d.buyer_nick }}"><img
                    src="/frontend/images/ww.gif" alt="" width="20px">@{{ d.buyer_nick }}</a>
    </script>
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form, layer = layui.layer, element = layui.element, layTpl = layui.laytpl, table = layui.table , tableIns;
            // 状态切换
            element.on('tab(order-list)', function () {
                $('[name=status]').val(this.getAttribute('lay-id'));
                reloadOrderList();
            });
            // 搜索
            form.on('submit(search)', function (data) {
                reloadOrderList(data.field);
                return false;
            });
            // 备注编辑
            table.on('edit(order-list)', function(obj){
                var value = obj.value, field = obj.field; // 修改后的值, 修改的字段
                $.post('{{ route("frontend.workbench.leveling.wait-remark") }}', {id:obj.data.id, field:field, value:value}, function (result) {
                }, 'json');
            });
            // 排序
            table.on('sort(order-list)', function(obj) {
                var type = 'desc';
                if (obj.type != null) {
                    type = obj.type;
                }
                table.reload('order-list', {
                    initSort: {
                    field: 'created',
                    type: type
                }});
                $.post('{{ route('frontend.workbench.leveling.wait-sort') }}', {type:obj.type}, function () {
                }, 'json')
            });
            // 加载数据
            table.render({
                elem: '#order-list',
                url: '{{ route('frontend.workbench.leveling.wait-order-list') }}',
                method: 'post',
                cols: [[
                    {field: 'seller_nick', title: '店铺', width: 150},
                    {field: 'tid', title: '订单号', width: 200},
                    {field: 'trade_status', title: '淘宝订单状态', width: 120},
                    {field: 'order_status', title: '平台订单状态', width: 120},
                    {field: 'game_name', title: '绑定游戏', width: 120},
                    {field: 'buyer_nick', title: '买家旺旺', templet: '#wwTemplate',width: 150},
                    {field: 'price', title: '购买单价'},
                    {field: 'num', title: '购买数量'},
                    {field: 'payment', title: '实付金额'},
                    {field: 'created', title: '下单时间',width: 180 , sort:true},
                    {field: 'remark', title: '备注', edit:'text',width: 200 },
                    {field: 'button', title: '操作', width: 155,  toolbar: '#operation', fixed: 'right'},
                    {type: 'checkbox', fixed: 'right'}
                ]],
                height: 'full-260',
                size: 'sm', //小尺寸的表格
                page: {
                    layout: [ 'count', 'prev', 'page', 'next', 'skip'],
                    groups: 10,
                    prev: '上一页',
                    next: '下一页'
                },
                initSort: {
                    field: 'created',
                    type: '{{ $sort }}'
                },
                done: function(res, curr, count){
                    setStatusNumber(res.status_count);
                }
            });
            // 订单显示隐藏
            $('.layui-card-body').on('click', '.update', function () {
                $.post($(this).attr('href'), {ad:1}, function () {
                    reloadOrderList();
                });
            });
            // 合并发布
            form.on('submit(send)', function (obj) {

                if($(obj.elem).hasClass('qs-btn-message')) {
                    layer.msg('请选择需要合并发布的订单');
                    return false;
                }
                var checkStatus = table.checkStatus('order-list'), checkData = checkStatus.data;

                if (checkData.length > 3) {
                    layer.msg('合并发单最多能选3个订单');
                    return false;
                }

                var createIng = 0;
                $('.layui-table-fixed-r').find('input[name="layTableCheckbox"]:checked').each(function(){
                    var tr = $(this).parents('tr');
                    if (tr.find('a').attr('data-status') == 0) {
                        createIng = 1;
                        return false;
                    }
                });

                if (createIng == 1) {
                    layer.msg('您选中的订单中,有其他客服正在发布的订单，请稍后再试');
                } else {
                    var id = '';
                    var gameId = 0;
                    $.each(checkData, function (index, item) {
                        if (gameId == 0) {
                            gameId = item.game_id;
                        }
                        $.post('{{ route('frontend.workbench.leveling.wait-time') }}', {tid:item.tid}, function (result) {});
                        id += item.tid + ',';
                    });
                    window.open('{{ route('frontend.workbench.leveling.create') }}?tid=' + id + '&game_id=' + gameId);
                }
                return false;
            });

            form.on('checkbox()', function(){
                var number = 0;

                $('.layui-table-fixed-r').find('input[name="layTableCheckbox"]:checked').each(function(){
                    number = number + 1;
                });

                if (number == 0) {
                    $('.send').addClass('qs-btn-message');
                    number = 0;
                } else {
                    $('.send').removeClass('qs-btn-message');
                    number = 0;
                }
            });
            form.on('checkbox(layTableAllChoose)', function (data) {
                if(table.checkStatus('order-list').isAll) {
                    $('.send').addClass('qs-btn-message');
                } else {
                    $('.send').removeClass('qs-btn-message');
                }
            });
            form.on('select(game)', function (data) {
                if (data.value == 86) {
                    $('#type').removeClass('layui-hide');
                } else {
                    $("[name=type]").val('');
                }
                form.render();
            });
            // 订单发布
            $('.layui-card-body').on('click', '.create',  function () {
                if ($(this).attr('data-status') == 0) {
                    layer.msg('其他客服正在发布此订单，请稍后再试');
                    return false;
                }
                $.post('{{ route('frontend.workbench.leveling.wait-time') }}', {tid:$(this).attr('data-tid')}, function (result) {
                });
                // 加载
                reloadOrderList();
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
                    height: 'full-260',
                    page: {
                        curr: 1
                    },
                    done: function(res, curr, count){
                        setStatusNumber(res.status_count);
                        $('.send').addClass('qs-btn-message');
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
            // 通知
            socket.on('notification:waitOrderChange', function (data) {
                if (data.user_id == {{ auth()->user()->getPrimaryUserId() }}) {
                    // 加载
                    reloadOrderList();
                }
            });
        });
        //倒计时函数
        var time = 0;
        function updateEndTime(){
            $(".create").each(function(i){
                var clickTime = this.getAttribute("data-click-time"); // 点击的时间

                if (clickTime && clickTime != 'null') {
                    getTime();
                    //转换为时间日期类型
                    var endDate1 = eval('new Date(' + clickTime.replace(/\d+(?=-[^-]+$)/, function (a) {
                                return parseInt(a, 10) - 1
                            }).match(/\d+/g) + ')');

                    var endTime = endDate1.getTime() + 60 * 1000; //结束时间毫秒数
                    var lag = (endTime - time) / 1000; //当前时间和结束时间之间的秒数

                    if(lag > 0) {
                        var second = Math.floor(lag % 60);
                        $(this).addClass('layui-bg-gray');
                        $(this).attr('data-status', 0);
                        $(this).html('发布(' + second + ')')
                    } else {
                        $(this).html('发布');
                        $(this).attr("data-click-time", 'null');
                        $(this).attr('data-status', 1);
                        $(this).removeClass('layui-bg-gray');
                    }
                }
            });

            setTimeout("updateEndTime()", 1000);
        }
        function getTime() {
            $.ajaxSetup({async: false});
            $.get('{{ config('app.time_url') }}', function (result) {
                time = result;
            });
        }
        updateEndTime();
    </script>
@endsection