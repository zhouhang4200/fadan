@extends('frontend.v1.layouts.app')

@section('title', '工作台 - 代充')

@section('css')
    {{--<link href="{{ asset('/css/index.css') }}" rel="stylesheet">--}}
    <style>
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

        /**新订单提示*/
        .prom-wrap {
            width: 100%;
            left: 0;
            bottom: 0;
            z-index: 999;
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
            border: 1px solid #F78400;
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
            padding: 0 15px 15px 15px;
            font-size: 13px;
        }
        .prom-list-footer {
            height: 40px;
            line-height: 40px;
            font-weight: bold;
            font-size: 15px;
            color: #F78400;
            border-top: 1px solid #F78400;
            text-align: center;
        }
        .prom-list-footer .prom-list-footer-tab.get {
            border-right: 1px solid #F78400;
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
        .fr {
            float: right;
        }
        .fl {
            float: left;
        }
        .overflow {
            overflow: hidden;
        }


    </style>
@endsection

@section('main')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">
            </div>
            <div class="layui-card-body">
                <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="need">急需处理 <span class="qs-badge wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span></li>
                        <li class="" lay-id="ing">处理中</li>
                        <li class="" lay-id="finish">已完成</li>
                        <li class="" lay-id="after-sales">售后中</li>
                        <li class="" lay-id="cancel">已取消</li>
                        <li class="" lay-id="market">集市 <span class="qs-badge market-order-quantity @if(marketOrderQuantity() == 0) layui-hide  @endif">{{ marketOrderQuantity() }}</span></li>
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
    <div class="prom-wrap fixed" id='prom'>
        <ul class="prom-inner">
        </ul>
    </div>
    <div id="audio"></div>
@endsection

<!--START 脚本-->
@section('js')
<script type="text/javascript" src="{{ asset('/frontend/js/orders-notice.js?20180604') }}"></script>
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
                content: "{{ url('/workbench/recharge/order-operation/detail') }}?no=" + no
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
            layer.confirm('您确定要"取消订单"吗?', {icon: 3, title:'提示'}, function(index) {
                $.post('{{ route('frontend.workbench.order-operation.cancel') }}', {no:no}, function (result) {
                    notification(result.status, result.message)
                }, 'json')
            });
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
                }, 'json');
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
@endsection