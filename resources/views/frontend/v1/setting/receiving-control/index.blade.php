@extends('frontend.v1.layouts.app')

@section('title', '设置 - 接单设置')

@section('css')

@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
        <blockquote class="layui-elem-quote">
            操作提示: 该功能可以控制，平台用户对您的订单的接单权限。控制方式：无（表示不控制所有平台用户可接您的订单）白名单（只有白名单中的用户可接您的单）黑名单（在黑名单中用户无法接您的订单）(三种方式只会有一种生效)
        </blockquote>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item" pane>
                <label class="layui-form-label">控制方式</label>
                <div class="layui-input-block"  >
                    <input type="radio" name="control" value="0" title="不开启"  lay-filter="control" @if($receivingControl == 0) checked @endif>
                    <input type="radio" name="control" value="1" title="白名单" lay-filter="control" @if($receivingControl == 1) checked @endif>
                    <input type="radio" name="control" value="2" title="黑名单"lay-filter="control" @if($receivingControl == 2) checked  @endif>
                </div>
            </div>
        </form>

        <div class="layui-tab  @if($receivingControl != 1) layui-hide  @endif" lay-filter="whitelist" id="whitelist">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="1">用户接单白名单</li>
                <li lay-id="2">游戏接单白名单</li>
                <li lay-id="3">商品接单白名单</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="whitelist-user-box"></div>
                </div>
                <div class="layui-tab-item">
                    <div class="whitelist-category-box"></div>
                </div>
                <div class="layui-tab-item">
                    <div class="whitelist-goods-box"></div>
                </div>
            </div>
        </div>

        <div class="layui-tab @if($receivingControl != 2)  layui-hide  @endif" lay-filter="blacklist" id="blacklist">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="1">用户接单黑名单</li>
                <li lay-id="2">商品接单黑名单</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="blacklist-user-box"></div>
                </div>
                <div class="layui-tab-item">
                    <div class="blacklist-category-box"></div>
                </div>
            </div>
        </div>

        <div id="user-add" style="display: none;padding: 20px">
            <form class="layui-form"   id="user-add-form">
                <input type="hidden" name="type" value="">
                <div class="layui-form-item">
                    <input type="text" name="other_user_id" required lay-verify="required" placeholder="请输入用户ID" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-item layui-form-text">
                    <textarea name="remark" placeholder="备注信息" class="layui-textarea"></textarea>
                </div>
                <div class="layui-form-item">
                    <button class="qs-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="user-add-save">确定添加</button>
                    <button  type="button" class="qs-btn layui-btn-danger cancel">取消添加</button>
                </div>
            </form>
        </div>

        <div id="category-add" style="display: none;padding: 20px">
            <form class="layui-form" action="" id="category-add-form">
                <input type="hidden" name="type" value="">
                <div class="layui-form-item">
                    <select name="service_id" lay-verify="required">
                        @foreach ($services as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-form-item">
                    <select name="game_id" lay-verify="required">
                        @foreach ($games as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-form-item">
                    <input type="text" name="other_user_id" required lay-verify="required" placeholder="请输入用户ID" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-item layui-form-text">
                    <textarea name="remark" placeholder="备注信息" class="layui-textarea"></textarea>
                </div>
                <div class="layui-form-item">
                    <button class="qs-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="category-add-save">确定添加</button>
                    <button  type="button" class="qs-btn layui-btn-danger cancel">取消添加</button>
                </div>
            </form>
        </div>

        <div id="goods-add" style="display: none;padding: 20px">
            <form class="layui-form" action="" id="goods-add-form">
                <input type="hidden" name="type" value="">
                <div class="layui-form-item">
                    <select name="service_id" lay-verify="required">
                        @foreach ($services as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-form-item">
                    <select name="goods_id" lay-verify="required">
                        @foreach ($goods as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-form-item">
                    <input type="text" name="other_user_id" required lay-verify="required" placeholder="请输入用户ID" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-item layui-form-text">
                    <textarea name="remark" placeholder="备注信息" class="layui-textarea"></textarea>
                </div>
                <div class="layui-form-item">
                    <button class="qs-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="goods-add-save">确定添加</button>
                    <button  type="button" class="qs-btn layui-btn-danger cancel">取消添加</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;
            var type = "{{ $receivingControl }}";
            var white = 1;
            var black = 1;

            element.on('tab(whitelist)', function(){
                white = this.getAttribute('lay-id');
                if (white == 1) {
                    loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}'  + '?type=' + type);
                } else if(white == 2) {
                    loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}'  + '?type=' + type)
                } else {
                    loadGoodsList('{{ route('frontend.setting.receiving-control.get-control-goods') }}'  + '?type=' + type)
                }
            });
            element.on('tab(blacklist)', function(){
                black = this.getAttribute('lay-id');
                if (black == 1) {
                    loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}'  + '?type=' + type);
                } else {
                    loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}'  + '?type=' + type)
                }
            });
            //监听控制方式
            form.on('radio(control)', function(data){
                type = data.value;
                if (type == 0) {
                    setControlMode('0');
                    layer.msg('已经关闭接单控制，平台所有用户可接您的订单');
                    $('#blacklist').addClass('layui-hide');
                    $('#whitelist').addClass('layui-hide');
                } else if(type == 1) {
                    if (white == 1) {
                        loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}'  + '?type=' + type);
                    } else {
                        loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}'  + '?type=' + type)
                    }
                    setControlMode('1');
                    layer.msg('已经切换为白名单模式');
                    $('#blacklist').addClass('layui-hide');
                    $('#whitelist').removeClass('layui-hide');
                } else {
                    if (black == 1) {
                        loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}' + '?type=' + type);
                    } else {
                        loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}' + '?type=' + type)
                    }
                    setControlMode('2');
                    layer.msg('已经切换为黑名单模式');
                    $('#whitelist').addClass('layui-hide');
                    $('#blacklist').removeClass('layui-hide');
                }
            });
            // 按用户ID添加
            form.on('submit(user-add)', function (data) {
                var title = data.elem.getAttribute('data-type') == 1 ? '用户接单白名单添加' : '用户接单黑名单添加';
                $('#user-add-form > input[name=type]').val(data.elem.getAttribute('data-type'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: title,
                    content: $('#user-add')
                });
                return false;
            });
            // 按类别添加
            form.on('submit(category-add)', function (data) {
                var title = data.elem.getAttribute('data-type') == 1 ? '用户接单白名单添加' : '用户接单黑名单添加';
                $('#category-add-form > input[name=type]').val(data.elem.getAttribute('data-type'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: title,
                    content: $('#category-add')
                });
                return false;
            });
            // 按商品添加
            form.on('submit(goods-add)', function (data) {
                var title = data.elem.getAttribute('data-type') == 1 ? '商品接单白名单添加' : '商品接单黑名单添加';
                $('#goods-add-form > input[name=type]').val(data.elem.getAttribute('data-type'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: title,
                    content: $('#goods-add')
                });
                return false;
            });
            // 保存按用户添加的数据
            form.on('submit(user-add-save)', function (data) {
                $.post('{{ route('frontend.setting.receiving-control.add-user') }}', {data:data.field}, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}'  + '?type=' + type);
                    }
                }, 'json');
                return false;
            });
            // 保存按类别添加的数据
            form.on('submit(category-add-save)', function (data) {
                $.post('{{ route('frontend.setting.receiving-control.add-category') }}', {data:data.field}, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}'  + '?type=' + type);
                    }
                }, 'json');
                return false;
            });
            // 保存按商品添加的数据
            form.on('submit(goods-add-save)', function (data) {
                $.post('{{ route('frontend.setting.receiving-control.add-goods') }}', {data:data.field}, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        loadGoodsList('{{ route('frontend.setting.receiving-control.get-control-goods') }}'  + '?type=' + type);
                    }
                }, 'json');
                return false;
            });
            // 删除用户名单中的用户ID
            form.on('submit(delete-user)', function (data) {
                layer.confirm('您确定要删除吗?', {icon: 3, title:'提示'}, function(){
                    $.post('{{ route('frontend.setting.receiving-control.delete-control-user') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}'  + '?type=' + type);
                    }, 'json');
                });
                return false;
            });
            // 删除分类中的用户ID
            form.on('submit(delete-category)', function (data) {
                layer.confirm('您确定要删除吗?', {icon: 3, title:'提示'}, function(){
                    $.post('{{ route('frontend.setting.receiving-control.delete-control-category') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}'  + '?type=' + type);
                    });
                });
                return false;
            });
            // 删除商品中的用户ID
            form.on('submit(delete-goods)', function (data) {
                layer.confirm('您确定要删除吗?', {icon: 3, title:'提示'}, function(){
                    $.post('{{ route('frontend.setting.receiving-control.delete-control-goods') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        loadGoodsList('{{ route('frontend.setting.receiving-control.get-control-goods') }}'  + '?type=' + type);
                    }, 'json');
                });
                return false;
            });

            // 按用户搜索
            form.on('submit(user-search)', function (data) {
                var par = '?type=' + type + '&other_user_id=' + data.field.other_user_id;
                loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}' + par);
                return false;
            });
            // 按类型搜索
            form.on('submit(category-search)', function (data) {
                var par = '?type=' + type + '&other_user_id=' + data.field.other_user_id
                        + '&server_id=' + data.field.other_user_id  + '&game_id=' + data.field.game_id;
                loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}' + par);
                return false;
            });
            // 取消按钮
            $('.cancel').click(function () {
                layer.closeAll();
            });
            // 按用户加载数据
            function loadUserList(url) {
                $.get(url, function (result) {
                    if (type == 1) {
                        $('.whitelist-user-box').html(result);
                        layui.form.render();
                    } else {
                        $('.blacklist-user-box').html(result);
                        layui.form.render();
                    }
                }, 'json');

            }
            // 按类别加载数据
            function loadCategoryList(url) {
                $.get(url, function (result) {
                    if (type == 1) {
                        $('.whitelist-category-box').html(result);
                        layui.form.render();
                    } else {
                        $('.blacklist-category-box').html(result);
                        layui.form.render();
                    }
                }, 'json');
            }
            // 按商品加载数据
            function loadGoodsList(url) {
                $.get(url, function (result) {
                    if (type == 1) {
                        $('.whitelist-goods-box').html(result);
                        layui.form.render();
                    } else {
                        $('.blacklist-goods-box').html(result);
                        layui.form.render();
                    }
                }, 'json');
            }
            // 设置控制方式
            function setControlMode(model) {
                $.post('{{ route('frontend.setting.receiving-control.control-mode') }}', {model:model},function (result) {
                }, 'json');
            }
            // 点击页码翻页
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                if (type == 1 && white == 1) {
                    loadUserList($(this).attr('href'))
                } else {
                    loadCategoryList($(this).attr('href'))
                }
                return false;
            });
        });
    </script>
@endsection