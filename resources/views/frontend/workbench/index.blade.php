@extends('frontend.layouts.app')

@section('title', '工作台')

@section('css')
    <style>
        .left-menu {
            width: 275px;
            background-color: #F5F5F5;
            border-left: solid 1px #D7D7D7;
            height: 100%;
            padding: 75px 20px 10px 0;
            position: fixed;
            z-index: 99;
            top: 0;
            bottom: 0;
            left: 0;
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
            right: -30px;
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
            right: -30px;
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
    </style>
@endsection

@section('main')
    <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="need">急需处理</li>
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
    <div class="left-menu" id="left-menu">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">类型</label>
                <div class="layui-input-block">
                    <select name="service_id" lay-verify="required" lay-search lay-filter="service">
                        <option value="0">请选择类型</option>
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
                        <option value="0">请选择游戏</option>
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
                    <select name="goods" lay-verify="required" lay-filter="goods" lay-search id="goods"
                            placeholder="请选择商品">
                        <option value="0">请选择商品</option>
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
        <div class="open-btn layui-hide"> 打开下单面板</div>
        <div class="close-btn layui-show">关闭下单面板</div>
    </div>
@endsection

@section('js')
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
                    element = layui.element;
            var serviceId = 0, gameId = 0;
            // 打开工作台时加载订单列表
            getOrder('{{ route('frontend.workbench.order-list') }}', 'need');
            // 切换订单状态
            element.on('tab(order-list)', function () {
                var type = this.getAttribute('lay-id');
                getOrder('{{ route('frontend.workbench.order-list') }}', type);
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
                $('#goods').empty().append('<option value="0">请选择商品</option>');
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
            // 获取url 参数
            function getQueryString(url, variable) {
                var str = url; //取得整个地址栏
                var num = str.indexOf("?");
                str = str.substr(num + 1); //取得所有参数   stringvar.substr(start [, length ]
                var arr = str.split("&"); //各个参数放到数组里
                for (var i = 0; i < arr.length; i++) {
                    var temp = arr[i].split("=");
                    if (temp[0] == variable) {
                        return temp[1];
                    }
                }
            }
            // 获取订单
            function getOrder(url, type) {
                type = type || 'need';
                $.post(url, {type: type}, function (result) {
                    $('.' + type).html(result);
                    layui.form.render();
                }, 'json');
            }
            // 订单操作：接单
            function receiving(no) {
                $.post('{{ route('frontend.workbench.order-operation.receiving') }}', {no:no}, function (result) {
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
                    area: ['500px', '600px'],
                    scrollbar: false,
                    content: "{{ url('/workbench/order-operation/detail') }}?no=" + no
                });
            }
            // 订单发货
            function delivery(no) {
                $.post('{{ route('frontend.workbench.order-operation.delivery') }}', {no:no}, function (result) {
                    notification(result.status, result.message)
                }, 'json')
            }
            // 失败订单
            function fail(no) {
                $.post('{{ route('frontend.workbench.order-operation.fail') }}', {no:no}, function (result) {
                    notification(result.status, result.message)
                }, 'json')
            }
            // 取消订单
            function cancel(no) {
                $.post('{{ route('frontend.workbench.order-operation.cancel') }}', {no:no}, function (result) {
                    notification(result.status, result.message)
                }, 'json')
            }
            // 取消订单
            function confirm(no) {
                $.post('{{ route('frontend.workbench.order-operation.confirm') }}', {no:no}, function (result) {
                    notification(result.status, result.message)
                }, 'json')
            }

            // 操作提示
            function notification(type, message) {
                if (type == 1) {
                    layer.open({
                        content: message,
                        success: function (layero, index) {
                            console.log(layero, index);
                        }
                    });
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
            $(document).scroll(function () {
                var top = $(document).scrollTop();
                if (top > 65) {
                    $('.left-menu').css('padding', '10px 20px 10px 0');
                } else {
                    $('.left-menu').css('padding', '75px 20px 10px 0');
                }
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
            var notification = {
                'orderId':data.no,
                'gameName':data.game_name,
                'goods':data.goods_name,
                'price':data.price,
                'remarks':1
            };
            orderHub.addData(notification);
        });
        // 订单数
        socket.on('notification:MarketOrderQuantity', function (data) {
            if (data.quantity == 0) {
                $('.market-order-quantity').addClass('layui-hide');
            } else {
                $('.market-order-quantity').removeClass('layui-hide').html(data.quantity);
            }
        });
    </script>
@endsection
