@extends('frontend.layouts.app')

@section('title', '商品 - 接单设置')

@section('css')

@endsection

@section('submenu')
    @include('frontend.setting.submenu')
@endsection

@section('main')
    <div class="explanation">
        <div class="ex_tit" style="margin-bottom: 10px;"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示" class=""></span></div>
        <ul>
            <li>该功能可以控制，平台用户对您的订单的接单权限。</li>
            <li>控制方式：无（表示不控制所有平台用户可接您的订单）白名单（只有白名单中的用户可接您的单）黑名单（在黑名单中用户无法接您的订单）(三种方式只会有一种生效)</li>
        </ul>
    </div>
    <form class="layui-form layui-form-pane" action="">
        <div class="layui-form-item" pane>
            <label class="layui-form-label">控制方式</label>
            <div class="layui-input-block"  >
                <input type="radio" name="control" value="0" title="不开启" checked lay-filter="control" @if($receivingControl == 0) @endif>
                <input type="radio" name="control" value="1" title="白名单" lay-filter="control" @if($receivingControl == 1) @endif>
                <input type="radio" name="control" value="2" title="黑名单"lay-filter="control" @if($receivingControl == 2) @endif>
            </div>
        </div>
    </form>

    <div class="layui-tab layui-hide  @if($receivingControl == 1) layui-show  @endif" lay-filter="whitelist" id="whitelist">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="1">用户接单白名单</li>
            <li lay-id="2">商品接单白名单</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="whitelist-user-box"></div>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form" id="search-form">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <select name="service_id">
                                <option value="">所有类型</option>
                                @foreach ($services as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $serviceId ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="game_id">
                                <option value="">所有游戏</option>
                                @foreach ($games as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $gameId ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-input-inline" style="width: 200px;">
                            <input type="text" class="layui-input" name="other_user_id" placeholder="用户ID" value="{{ $otherUserId  }}">
                        </div>
                        <div class="layui-input-inline" style="width: 200px;">
                            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
                        </div>
                        <button class="layui-btn layui-btn-normal fr"  data-type="1" lay-submit lay-filter="user-add">添加用户ID</button>
                    </div>
                </form>
                <div class="whitelist-category-box"></div>
            </div>
        </div>
    </div>

    <div class="layui-tab layui-hide @if($receivingControl == 2) layui-show  @endif" lay-filter="blacklist" id="blacklist">
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
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="user-add-save">确定添加</button>
                <button  type="button" class="layui-btn layui-btn-danger cancel">取消添加</button>
            </div>
        </form>
    </div>

    <div id="category-add" style="display: none;padding: 20px">
        <form class="layui-form" action="" id="user-add-form">
            <div class="layui-form-item">
                <label class="layui-form-label">服务</label>
                <div class="layui-input-inline">
                    <select name="service_id" lay-verify="required">
                        @{{#  layui.each(d.services, function(i, v){ }}
                        @{{#  if(d.service_id == i){ }}
                        <option value="@{{ i  }}" selected>@{{ v  }}</option>
                        @{{#  } else { }}
                        <option value="@{{ i  }}">@{{ v  }}</option>
                        @{{#  } }}
                        @{{#  }); }}
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">游戏</label>
                <div class="layui-input-inline">
                    <select name="game_id" lay-verify="required">

                        @{{#  layui.each(d.games, function(i, v){ }}
                        @{{#  if(d.game_id == i){ }}
                        <option value="@{{ i  }}" selected>@{{ v  }}</option>
                        @{{#  } else { }}
                        <option value="@{{ i  }}">@{{ v  }}</option>
                        @{{# } }}
                        @{{#  }); }}
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <input type="text" name="title" required lay-verify="required" placeholder="请输入用户ID" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="user-add-save">确定添加</button>
                <button  type="button" class="layui-btn layui-btn-danger cancel">取消添加</button>
            </div>
        </form>
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
            });
            element.on('tab(blacklist)', function(){
                black = this.getAttribute('lay-id');
            });
            //监听控制方式
            form.on('radio(control)', function(data){
                if (data.value == 0) {
                    type = data.value;
                    layer.msg('已经关闭接单控制，平台所有用户可接您的订单');
                    $('#blacklist').addClass('layui-hide');
                    $('#whitelist').addClass('layui-hide');
                } else if(data.value == 1) {
                    type = data.value;
                    if (white == 1) {
                        loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}'  + '?type=' + type);
                    } else {
                        loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}'  + '?type=' + type)
                    }
                    layer.msg('已经切换为白名单模式');
                    $('#blacklist').addClass('layui-hide');
                    $('#whitelist').removeClass('layui-hide');
                } else {
                    type = data.value;
                    if (black == 1) {
                        loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}' + '?type=' + type);
                    } else {
                        loadCategoryList('{{ route('frontend.setting.receiving-control.get-control-category') }}' + '?type=' + type)
                    }
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
            form.on('submit(category-add)', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加商品模版',
                    content: $('#category-add')
                });
                return false;
            });
            // 保存按用户添加的数据
            form.on('submit(user-add-save)', function (data) {
                $.post('{{ route('frontend.setting.receiving-control.add-user') }}', {data:data.field}, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                }, 'json');
                return false;
            });
            // 按用户加载数据
            function loadUserList(url) {
                $.get(url, function (result) {
                   if (type == 1) {
                       $('.whitelist-user-box').html(result)
                   } else {
                       $('.blacklist-user-box').html(result)
                   }
                }, 'json');
            }
            // 按类别加载数据
            function loadCategoryList(url) {
                $.get(url, function (result) {
                    if (type == 1) {
                        $('.whitelist-category-box').html(result)
                    } else {
                        $('.blacklist-category-box').html(result)
                    }
                }, 'json');
            }
            // 取消按钮
            $('.cancel').click(function () {
                layer.closeAll();
            });
            // 点击页码翻页
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                if (type == 1 && white == 1) {
                    loadUserList($(this).attr('href'))
                } else {
                    loadCategoryList($(this).attr('href'))
                }
//                getOrder($(this).attr('href'), getQueryString($(this).attr('href'), "type"));
                return false;
            });
        });

    </script>
@endsection