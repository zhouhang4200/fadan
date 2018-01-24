<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>工作台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="_token" content="{{ csrf_token() }}" >
    <!--START 样式表-->
    @include('frontend.layouts.links')
    <style>
        .wrapper {
            position: relative;
            min-width: 1200px;
            width: auto;
            padding: 0 30px;
            margin: auto;
            zoom: 1;
        }
        .right-content {
            height: 800px;
            background-color: #fff;
            position: inherit;
        }
        .left-menu {
            width: 265px;
            background-color: #F5F5F5;
            border-left: solid 1px #D7D7D7;
            height: 100%;
            padding: 75px 20px 10px 0;
            position: fixed;
            z-index: 99;
            top: 0;
            bottom: 0;
            left: -296px;
            box-shadow: 0 0 10px 0 rgba(100, 100, 100, 0.5);
            min-height: 650px;
        }
        .left-menu > .open-btn {
            color: #FFF;
            background-color: #1E9FFF;
            width: 16px;
            padding: 8px 6px 8px 7px;
            margin-top: -80px;
            border: solid 1px #1E9FFF;
            border-right: 0 none;
            position: absolute;
            z-index: 99;
            top: 50%;
            right: -37px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 0 5px 0 rgba(204, 204, 204, 0.5);
            border-radius: 0 5px 5px 0;
        }
        .left-menu > .close-btn {
            color: #FFF;
            background-color: #1E9FFF;
            width: 16px;
            padding: 8px 6px 8px 7px;
            margin-top: -80px;
            border: solid 1px #1E9FFF;
            border-right: 0 none;
            position: absolute;
            z-index: 99;
            top: 50%;
            right: -31px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 0 5px 0 rgba(204, 204, 204, 0.5);
            border-radius: 0 5px 5px 0;
        }
        .prom-wrap {
            width: 100%;
            left: 0;
            bottom: 0;
        }
        .fixed {
            position: fixed;
        }
        .prom-inner {
            width: 1200px;
            margin: 0 auto;
        }
        .prom-list {
            background: white;
            position: relative;
            z-index: 100;
            border: 1px solid #1E9FFF;
            border-radius: 5px;
            box-sizing: border-box;
            width: 290px;
            margin: 5px;
        }
        .prom-list-header {
            height: 40px;
            line-height: 40px;
            font-weight: bold;
            font-size: 18px;
            padding-left: 13px;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .relative {
            position: relative;
        }
        .prom-list-body {
            padding: 20px 15px;
            font-size: 13px;
        }
        .prom-list-footer {
            height: 40px;
            line-height: 40px;
            font-weight: bold;
            font-size: 15px;
            color: #1E9FFF;
            border-top: 1px solid #1E9FFF;
        }
        .prom-list-footer .prom-list-footer-tab.get {
            border-right: 1px solid #1E9FFF;
        }
        .prom-list-footer .prom-list-footer-tab {
            width: 50%;
            box-sizing: border-box;
            cursor: pointer;
        }
        .prom-list-contents span {
            width: 48%;
        }
        .close-prom {
            background-color: #1E9FFF;
            color: white;
            padding: 2px 6px;
            top: 6px;
            right: 6px;
            line-height: 22px;
            text-align: center;
            cursor: pointer;
            border-radius: 3px;
            font-size: 14px;
        }
        .header .user {
            right: 30px;
        }
        .layui-tab-content {
            padding: 0 !important;
        }
    </style>
    <!--END 样式表-->
</head>
<body>




<!--START 顶部菜单-->
@include('frontend.layouts.header')
<!--END 顶部菜单-->

<!--START 主体-->
<div class="main">
    <div class="workbench-wrapper">
        @can('frontend.workbench.order')
            <div class="left-menu" id="left-menu">
            <form class="layui-form " action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">类型</label>
                    <div class="layui-input-block">
                        <select name="service_id" lay-verify="required" lay-search lay-filter="service">
                            <option value="">请选择类型</option>
                            @forelse($services as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">游戏</label>
                    <div class="layui-input-block">
                        <select name="game_id" lay-verify="required" lay-search lay-filter="game">
                            <option value="">请选择游戏</option>
                            @forelse($games as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">商品</label>
                    <div class="layui-input-block">
                        <select name="goods" lay-verify="required" lay-search id="goods" lay-filter="goods" >
                            <option value="">请选择商品</option>
                        </select>
                    </div>
                </div>
                <div id="template"></div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-bg-blue" lay-submit lay-filter="order">确认下单</button>
                    </div>
                </div>
            </form>
            <div class="open-btn"> 打开下单面板</div>
            <div class="close-btn layui-hide">关闭下单面板</div>
        </div>
        @endcan
        <div class="right-content">
            <div class="content">
                <div class="path"><span>工作台</span>
                </div>

                <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="need">急需处理 <span class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span></li>
                        <li class="" lay-id="ing">处理中</li>
                        <li class="" lay-id="finish">已完成</li>
                        <li class="" lay-id="after-sales">售后中</li>
                        <li class="" lay-id="cancel">已取消</li>
                        <li class="" lay-id="market">集市 <span class="layui-badge layui-bg-blue market-order-quantity @if(marketOrderQuantity() == 0) layui-hide  @endif">{{ marketOrderQuantity() }}</span></li>
                    </ul>

                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show need"></div>
                        <div class="layui-tab-item ing"></div>
                        <div class="layui-tab-item finish"></div>
                        <div class="layui-tab-item after-sales"></div>
                        <div class="layui-tab-item cancel"></div>
                        <div class="layui-tab-item market"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="prom-wrap fixed" id='prom'>
    <ul class="prom-inner">
    </ul>
</div>
<div id="audio"></div>
<!--END 主体-->

@include('frontend.layouts.scripts')

<!--START 底部-->
<link rel="stylesheet" href="/frontend/css/layui-rewrit.css">
@include('frontend.layouts.footer')
<!--END 底部-->

<!--START 脚本-->
@yield('js')
<script type="text/javascript" src="{{ asset('/frontend/js/orders-notice.js?v20170927') }}"></script>
<script type="text/html" id="goodsTemplate">
    @{{#  layui.each(d, function(index, item){ }}

    @{{#  if(item.field_type === 1){ }}
    <div class="layui-form-item">
        <label class="layui-form-label">@{{ item.field_display_name }}</label>
        <div class="layui-input-block">
            @{{#  if(item.field_name == 'password'){ }}
            <input type="password" name="@{{ item.field_name }}" placeholder="请输入@{{ item.field_display_name }}"
                   autocomplete="off" class="layui-input" value=""
                   @{{#  if(item.field_required == 1){ }} lay-verify="required" @{{#  } }} >
            @{{#  } else { }}
            <input type="text" name="@{{ item.field_name }}" placeholder="请输入@{{ item.field_display_name }}"
                   autocomplete="off" class="layui-input" value=""
                   @{{#  if(item.field_required == 1){ }} lay-verify="required" @{{#  } }}>
            @{{#  } }}
        </div>
    </div>
    @{{#  } }}

    @{{#  if(item.field_type === 2){ }}
    <div class="layui-form-item">
        <label class="layui-form-label">@{{ item.field_display_name }}</label>
        <div class="layui-input-block">
            <select name="@{{ item.field_name }}" lay-filter="change-select" data-id="@{{ item.id }}"
                    id="select-parent-@{{ item.field_parent_id }}"
                    @{{#  if(item.field_required == 1){ }} lay-verify="required" @{{#  } }} lay-search>
                <option value="">请选择@{{ item.field_display_name }}</option>
                @{{#  if(item.field_parent_id  == 0){ }}
                @{{# var option = (item.field_value).split("|") }}
                @{{#  layui.each(option, function(i, v){ }}
                <option value="@{{ v }}">@{{ v }}</option>
                @{{#  }); }}
                @{{#  } else { }}
                <option value="请选上级">请选上级</option>
                @{{#  } }}
            </select>
        </div>
    </div>
    @{{#  } }}

    @{{#  if(item.field_type === 3){ }}
    <div class="layui-form-item">
        <label class="layui-form-label">@{{ item.field_display_name }}</label>
        <div class="layui-input-block">
            @{{# var option = (item.field_value).split("|") }}
            @{{#  layui.each(option, function(i, v){ }}
            @{{#  if(item.field_default_value ==  v ){  }}
            <input type="radio" name="field_type" value="@{{ v }}" title="@{{ v }}" checked="">
            @{{#  } else { }}
            <input type="radio" name="field_type" value="@{{ v }}" title="@{{ v }}">
            @{{#  } }}
            @{{#  }); }}
        </div>
    </div>
    @{{#  } }}

    @{{#  }); }}

    @{{#  if(d.length === 0){ }}
    没有组件
    @{{#  } }}
</script>
<script>
    layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
        var form = layui.form,
                layer = layui.layer,
                layTpl = layui.laytpl,
                util = layui.util,
                element = layui.element;
        var serviceId = 0, gameId = 0, currentUrl, currentType;
        // 倒计时

        // 打开工作台时加载订单列表
        getOrder('{{ route('frontend.workbench.order-list') }}', 'need');
        // 切换订单状态
        element.on('tab(order-list)', function () {
            try {
                var type = this.getAttribute('lay-id');
                if (type) {
                    $('.search').empty();
                    if (type == 'search') {
                        return false;
                    } else {
                        element.tabDelete('order-list', 'search');
                    }
                    // 如果是急需处理
                    if (type == 'need') {
                        $(this).children('span').addClass('layui-hide');
                        $.post('{{ route('frontend.workbench.clear-wait-handle-quantity') }}', {id:1}, function (result) {

                        }, 'json')
                    }
                    getOrder('{{ route('frontend.workbench.order-list') }}', type);
                    return false;
                }
            } catch (e) {

            }
        });
        // 订单操作
        form.on('select(operation)', function (data) {
            eval(data.value + "('" + data.elem.getAttribute('data-no')  + "')");
        });
        // 选择服务
        form.on('select(service)', function (data) {
            serviceId = data.value;
            if (serviceId) {
                goods();
            }
            clearGoods();
            layui.form.render();
        });
        // 选择游戏
        form.on('select(game)', function (data) {
            gameId = data.value;
            if (gameId) {
                goods();
            }
            clearGoods();
            layui.form.render();
        });
        // 下拉框根据父级选择项获取子级的选项
        form.on('select(change-select)', function (data) {
            var subordinate = "#select-parent-" + data.elem.getAttribute('data-id');
            if ($(subordinate).length > 0 && data.elem.selectedIndex != 0) {
                $.post('{{ route('frontend.workbench.widget.child') }}', {
                    id: data.elem.selectedIndex,
                    parent_id: data.elem.getAttribute('data-id')
                }, function (result) {
                    $(subordinate).html(result);
                    $(result.content.child).each(function (index, name) {
                        $(subordinate).append('<option value="' + name + '">' + name + '</option>');
                    });
                    layui.form.render();
                }, 'json');
            }
            return false;
        });
        // 下单
        form.on('submit(order)', function (data) {
            $.post('{{ route('frontend.workbench.order') }}', {data: data.field}, function (result) {
                layer.msg(result.message)
            }, 'json');
            return false;
        });
        // 搜索
        form.on('submit(search)', function (data) {
            getOrder('{{ route('frontend.workbench.order-list') }}', 'search', data.field.search_type, data.field.search_content);
        });
        // 获取商品流程
        function goods() {
            if (serviceId != 0 && gameId != 0) {
                $.post('{{ route("frontend.workbench.goods") }}', {
                    service_id: serviceId,
                    game_id: gameId
                }, function (result) {
                    if (result.status == 0) {
                        layer.msg(result.message);
                    } else {
                        clearGoods();
                        // 加载商品
                        $.each(result.content.goods, function (i, v) {
                            $('#goods').append('<option value="' + i + '">' + v + '</option>');
                            layui.form.render();
                        });
                        // 加载模版
                        template();
                        layui.form.render();
                    }
                }, 'json');
            }
        }
        // 清空商品栏
        function clearGoods() {
            $('#goods').empty().append('<option value="">请选择商品</option>');
        }
        // 加载模版
        function template() {
            var getTpl = goodsTemplate.innerHTML, view = document.getElementById('template');
            $.post('{{ route('frontend.workbench.template' ) }}', {
                service_id: serviceId,
                game_id: gameId
            }, function (result) {
                layTpl(getTpl).render(result.content.widgets, function (html) {
                    view.innerHTML = html;
                    layui.form.render()
                });
            }, 'json');
        }
        // 获取订单
        function getOrder(url, type, searchType, searchContent) {
            type = type || 'need';
            searchType = type == 'search' ?  searchType : '';
            searchContent = type  == 'search' ?  searchContent : '';
            currentUrl = url;
            currentType = type;

            $.post(url, {type: type, search_type:searchType, search_content:searchContent}, function (result) {
                if (type == 'search') {
                    element.tabDelete('order-list', 'search');
                    element.tabAdd('order-list', {
                        title: '搜索结果',
                        id: 'search',
                        content: result
                    });
                    element.tabChange('order-list', 'search'); //切换到：用户管理
                } else {
                    $('.' + type).html(result);
                }
                layui.form.render();
            }, 'json');
        }
        // 订单操作：接单
        function receiving(no) {
            $.post('{{ route('frontend.workbench.order-operation.receiving') }}', {no:no}, function (result) {
                notification(result.status, result.message)
            }, 'json')
        }
        // 订单操作：支付
        function payment(no) {
            $.post('{{ route('frontend.workbench.order-operation.payment') }}', {no:no}, function (result) {
                notification(result.status, result.message)
            }, 'json')
        }
        // 订单操作：查看
        function detail(no) {
            layer.open({
                type: 2,
                title: '订单详情',
                shadeClose: true,
                maxmin: true, //开启最大化最小化按钮
                area: ['600px', '630px'],
                scrollbar: false,
                content: "{{ route('frontend.workbench.order-operation.detail') }}?no=" + no
            });
        }
        // 订单发货
        function delivery(no) {
            layer.confirm('您确定订单完成要发货吗?', {icon: 3, title:'提示'}, function(index){
                $.post('{{ route('frontend.workbench.order-operation.delivery') }}', {no:no}, function (result) {
                    notification(result.status, result.message)
                }, 'json');
                layer.close(index);
            });

        }
        // 失败订单
        function fail(no) {
            layer.prompt({
                formType: 2,
                title: '请输入失败原因',
                area: ['200', '100']
            }, function(value, index, elem){
                $.post('{{ route('frontend.workbench.order-operation.fail') }}', {no:no,remark:value}, function (result) {
                    notification(result.status, result.message)
                }, 'json');
                layer.close(index);
            });
        }
        // 取消订单
        function cancel(no) {
            $.post('{{ route('frontend.workbench.order-operation.cancel') }}', {no:no}, function (result) {
                notification(result.status, result.message)
            }, 'json')
        }
        // 确认收货
        function confirm(no) {
            layer.confirm('您确定要"确认收货"吗?', {icon: 3, title:'提示'}, function(index) {
                $.post('{{ route('frontend.workbench.order-operation.confirm') }}', {no:no}, function (result) {
                    notification(result.status, result.message)
                }, 'json')
            });
        }
        // 返回集市
        function turnBack(no) {
            layer.prompt({
                formType: 2,
                title: '请输入返回集市原因',
                area: ['200', '100']
            }, function(value, index, elem){
                $.post('{{ route('frontend.workbench.order-operation.turnBack') }}', {no:no,remark:value}, function (result) {
                    notification(result.status, result.message)
                }, 'json');
                layer.close(index);
            });
        }
        function afterSales(no) {
            layer.prompt({
                formType: 2,
                title: '请输入发起售后的原因',
                area: ['200', '100']
            }, function(value, index, elem){
                $.post("{{ route('frontend.workbench.order-operation.after-sales') }}", {
                    no:no,
                    remark:value
                }, function (result) {
                    notification(result.status, result.message)
                }, 'json')
                layer.close(index);
            });

        }
        // 操作提示
        function notification(type, message) {
            if (type == 1) {
                layer.open({
                    content: message,
                    success: function (layero, index) {
                    }
                });
                getOrder(currentUrl, currentType);
            } else {
                layer.msg(message)
            }
        }
        // 点击页码翻页
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            getOrder($(this).attr('href'), getQueryString($(this).attr('href'), "type"));
            return false;
        });
//        $(window).resize(function() {
//            $(".right-content").width($(window).width()-275)
//        });
//        $(".right-content").width($(window).width()-275);
        $('#prom').on('click', '.windows-receiving', function () {
            receiving($(this).attr('data-no'));
        });
        // 下单面板开关
        $(".open-btn").click(function () {
            $("#left-menu").animate({left: "0"});
            $(".open-btn").addClass("layui-hide").removeClass("layui-show");
            $(".close-btn").addClass("layui-show").removeClass("layui-hide");
        });
        $(".close-btn").click(function () {
            $("#left-menu").animate({left: "-296"});
            $(".close-btn").addClass("layui-hide").removeClass("layui-show");
            $(".open-btn").addClass("layui-show").removeClass("layui-hide");
        });
    });
    // 监听新订单
    socket.on('notification:NewOrderNotification', function (data) {
        if(data.creator_user_id != '{{ Auth::user()->id }}') {
            var notification = {
                'orderId':data.no,
                'gameName':data.game_name,
                'goods':data.goods_name,
                'price':data.price,
                'remarks':1
            };
            $('#audio').html('<audio autoplay="autoplay"><source src="/frontend/audio/new-order.mp3" type="audio/mpeg"/></audio>');
            orderHub.addData(notification);
        }
    });
    // 订单数
    socket.on('notification:MarketOrderQuantity', function (data) {
        if (data.quantity == 0) {
            $('.market-order-quantity').addClass('layui-hide');
        } else {
            $('.market-order-quantity').removeClass('layui-hide').html(data.quantity);
        }
    });
    // 待处理订单数
    socket.on('notification:waitHandleQuantity', function (data) {
        if (data.quantity == 0) {
            $('.wait-handle-quantity').addClass('layui-hide');
        } else {
            $('.wait-handle-quantity').removeClass('layui-hide').html(data.quantity);
        }
    });

    $(function(){
        updateEndTime();
    });

    //倒计时函数
    function updateEndTime(){

        var date = new Date();
        var time = date.getTime();  //当前时间距1970年1月1日之间的毫秒数

        $(".end-time").each(function(i){

            var endDate = this.getAttribute("data-time"); //结束时间字符串

            if (endDate == 0) {
                return $(this).html("-");
            }

            //转换为时间日期类型
            var endDate1 = eval('new Date(' + endDate.replace(/\d+(?=-[^-]+$)/, function (a) { return parseInt(a, 10) - 1 }).match(/\d+/g) + ')');

            var endTime = endDate1.getTime() + {{ config('order.max_use_time') }}*1000; //结束时间毫秒数
            var lag = (endTime - time) / 1000; //当前时间和结束时间之间的秒数

            if(lag > 0) {
                var second = Math.floor(lag % 60);
                var minite = Math.floor((lag / 60) % 60);
                // var hour = Math.floor((lag / 3600) % 24);
                // var day = Math.floor((lag / 3600) / 24);
                $(this).html(minite+"分"+second+"秒");
            } else {
                $(this).html("超时");
            }
        });
        setTimeout("updateEndTime()", 1000);
    }
</script>
<!--END 脚本-->
</body>
</html>
