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
            border: solid 1px #2588e5;
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
            border: solid 1px #2588e5;
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
    </style>
@endsection

@section('main')
    <div class="layui-tab layui-tab-brief layui-form" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li class="layui-this">急需处理</li>
            <li class="">处理中</li>
            <li class="">已完成</li>
            <li class="">售后中</li>
            <li class="">集市 <span class="layui-badge layui-bg-blue">12321</span></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <table class="layui-table" lay-size="sm">
                    <colgroup>
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>订单号</th>
                        <th>类型</th>
                        <th>游戏</th>
                        <th>商品</th>
                        <th>数量</th>
                        <th>总价</th>
                        <th>状态</th>
                        <th width="13%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>2017009202222239374</td>
                        <td>游戏充值</td>
                        <td>王者荣耀</td>
                        <td>680+233</td>
                        <td>99.39</td>
                        <td>99.39</td>
                        <td>99.39</td>
                        <td>
                            <div class="layui-input-inline">
                                <select name="city" lay-verify="required" lay-filter="operation">
                                    <option value="">请选择操作</option>
                                    <option value="0">订单详情</option>
                                    <option value="1">订单发货</option>
                                    <option value="1">订单失败</option>
                                    <option value="2">返回集市</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="layui-tab-item" lay-size="sm">
                <table class="layui-table">
                    <colgroup>
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>昵称</th>
                        <th>加入时间</th>
                        <th>签名</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>贤心</td>
                        <td>2016-11-29</td>
                        <td>人生就像是一场修行</td>
                    </tr>
                    <tr>
                        <td>许闲心</td>
                        <td>2016-11-28</td>
                        <td>于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="layui-tab-item" lay-size="sm">
                <table class="layui-table">
                    <colgroup>
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>昵称</th>
                        <th>加入时间</th>
                        <th>签名</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>贤心</td>
                        <td>2016-11-29</td>
                        <td>人生就像是一场修行</td>
                    </tr>
                    <tr>
                        <td>许闲心</td>
                        <td>2016-11-28</td>
                        <td>于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="layui-tab-item" lay-size="sm">
                <table class="layui-table">
                    <colgroup>
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>昵称</th>
                        <th>加入时间</th>
                        <th>签名</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>贤心</td>
                        <td>2016-11-29</td>
                        <td>人生就像是一场修行</td>
                    </tr>
                    <tr>
                        <td>许闲心</td>
                        <td>2016-11-28</td>
                        <td>于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                    </tr>
                    </tbody>
                </table>
            </div>
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
                    <select name="goods" lay-verify="required" lay-filter="goods" lay-search id="goods"  placeholder="请选择商品" >
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
<script id="goodsTemplate" type="text/html">
    @{{#  layui.each(d, function(index, item){ }}

    @{{#  if(item.field_type === 1){ }}
    <div class="layui-form-item">
        <label class="layui-form-label">@{{ item.field_display_name }}</label>
        <div class="layui-input-block">
            @{{#  if(item.field_name == 'password'){ }}
            <input type="password" name="@{{ item.field_name }}"  placeholder="请输入@{{ item.field_display_name }}" autocomplete="off" class="layui-input" value=""  @{{#  if(item.field_required == 1){ }} lay-verify="required"  @{{#  } }} >
            @{{#  } else { }}
            <input type="text" name="@{{ item.field_name }}"  placeholder="请输入@{{ item.field_display_name }}" autocomplete="off" class="layui-input" value="" @{{#  if(item.field_required == 1){ }} lay-verify="required"  @{{#  } }}>
            @{{#  } }}
        </div>
    </div>
    @{{#  } }}

    @{{#  if(item.field_type === 2){ }}
    <div class="layui-form-item">
        <label class="layui-form-label">@{{ item.field_display_name }}</label>
        <div class="layui-input-block">
            <select name="@{{ item.field_name }}" lay-filter="change-select" data-id="@{{ item.id }}" id="select-parent-@{{ item.field_parent_id }}" @{{#  if(item.field_required == 1){ }} lay-verify="required"  @{{#  } }} lay-search>
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
        <div class="layui-input-block" >
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
    layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
        var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
        var serviceId = 0, gameId = 0;

        //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
        var layid = location.hash.replace(/^#test1=/, '');
        element.tabChange('test1', layid); //假设当前地址为：http://a.com#test1=222，那么选项卡会自动切换到“发送消息”这一项

        // 切换订单状态
        element.on('tab(test1)', function () {
            location.hash = 'test1=' + this.getAttribute('lay-id');
        });

        // 订单操作
        form.on('select(operation)', function(data){
            layer.open({
                content: '测试回调',
                success: function(layero, index){
                    console.log(layero, index);
                }
            });
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
        form.on('select(change-select)', function(data){
            var subordinate = "#select-parent-" + data.elem.getAttribute('data-id');
            if($(subordinate).length > 0){
                $.post('{{ route('frontend.workbench.widget.child') }}', {id:data.elem.selectedIndex, parent_id:data.elem.getAttribute('data-id')}, function (result) {
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
        form.on('submit(order)', function(data){
            layer.open({
                content: JSON.stringify(data.field),
                success: function(layero, index){
                    console.log(layero, index);
                }
            });
            $.post('{{ route('frontend.workbench.order') }}', {data:data.field}, function (result) {

            });
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
            $.post('{{ route('frontend.workbench.template' ) }}', {service_id:serviceId, game_id:gameId}, function (result) {
                layTpl(getTpl).render(result.content.widgets, function(html){
                    view.innerHTML = html;
                    layui.form.render()
                });
            }, 'json');
        }
    });

    $(document).scroll(function () {
        var top = $(document).scrollTop();
        if (top > 65) {
            $('.left-menu').css('padding', '10px 20px 10px 0');
        } else {
            $('.left-menu').css('padding', '75px 20px 10px 0');
        }
    });

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
</script>
@endsection
