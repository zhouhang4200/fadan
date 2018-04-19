@extends('frontend.layouts.app')

@section('title', '设置 - 抓单商品配置')

@section('css')
    <style>
        .layui-form-select dl {
            max-height: 190px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.setting.submenu')
@endsection

@section('main')
    <div class="explanation">
        <div class="ex_tit" style="margin-bottom: 10px;"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示" class=""></span></div>
        <ul>
            <li>用途：添加了某一淘宝/天猫商品，则会自动获取该商品对应的订单，未添加商品则无法获取商品对应订单。请确保添加商品之前，已进行店铺旺旺绑定。</li>
        </ul>
    </div>

    <form class="layui-form" id="goods-form">
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 200px;">
                <input type="text" class="layui-input" name="foreign_goods_id" placeholder="淘宝商品ID" value="{{ $foreignGoodsId  }}">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="category-search">查询</button>
            </div>
            <button class="layui-btn layui-btn-normal fr" lay-submit lay-filter="goods-add">添加商品</button>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>店铺</th>
            <th>淘宝商品ID</th>
            <th>绑定游戏</th>
            <th>备注</th>
            <th>添加时间</th>
            <th>更新时间</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($automaticallyGrabGoods as $item)
            <tr>
                <td>{{ $item->seller_nick }}</td>
                <td>{{ $item->foreign_goods_id }}</td>
                <td>{{ $item->game->name ?? '' }}</td>
                <td>{{ $item->remark }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
                <td>
                    <button class="layui-btn layui-btn-normal layui-btn-small edit-good" data-id="{{ $item->id }}">修改</button>
                    <button class="layui-btn layui-btn-normal layui-btn-small" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">删除</button>
                </td>
            </tr>
        @empty

        @endforelse
        </tbody>
    </table>

    {{ $automaticallyGrabGoods->links() }}

    <div id="goods-add" style="display: none;padding: 30px 60px 0 0px;">
        <form class="layui-form" action="" id="goods-add-form">
            <input type="hidden" name="type" value="">
            <input type="hidden" name="service_id" value="4">
            <div class="layui-form-item">
                <label class="layui-form-label">店铺</label>
                <div class="layui-input-block">
                <select name="seller_nick" lay-verify="required" lay-search>
                    <option value=""></option>
                    @forelse($shop as  $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @empty
                    @endforelse
                </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">绑定游戏</label>
                <div class="layui-input-block">
                <select name="game_id" lay-verify="required" lay-search>
                    <option value=""></option>
                    @forelse($game as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @empty
                    @endforelse
                </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">淘宝链接</label>
                <div class="layui-input-block">
                <input type="text" name="foreign_goods_id" required lay-verify="required" placeholder="淘宝链接" autocomplete="off" class="layui-input">
                    </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">备注信息</label>
                <div class="layui-input-block">
                <textarea name="remark" placeholder="备注信息" class="layui-textarea"></textarea>
                    </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="goods-add-save">确定添加</button>
                <button  type="button" class="layui-btn layui-btn-danger cancel">取消添加</button>
                    </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;

            // 添加商品
            form.on('submit(goods-add)', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加商品',
                    area: ['500px'],
                    content: $('#goods-add')
                });
                return false;
            });

            $('.content').on('blur', 'input[name=foreign_goods_id]', function () {
                var url = $(this).val();
                var goodsId = getQueryString(url, 'id');
                if (isNaN($(this).val())) {
                    $(this).val($.trim(goodsId));
                }
            });

            $('body').on('blur', 'input[name=foreign_goods_id]', function () {
                var url = $(this).val();
                var goodsId = getQueryString(url, 'id');
                if (isNaN($(this).val())) {
                    $(this).val($.trim(goodsId));
                }
            });

            $('#goods-form').on('blur', 'input[name=other_user_id]', function () {
                var url = $(this).val();
                var goodsId = getQueryString(url, 'id');
                if (isNaN($(this).val())) {
                    $(this).val($.trim(goodsId));
                }
            });

            // 保存按添加
            form.on('submit(goods-add-save)', function (data) {
                $.post('{{ route('frontend.setting.automatically-grab.add') }}', {
                    service_id:data.field.service_id,
                    game_id:data.field.game_id,
                    seller_nick:data.field.seller_nick,
                    foreign_goods_id:data.field.foreign_goods_id,
                    remark:data.field.remark
                }, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        setTimeout(function () {
                            location.reload();
                        }, 700);
                    }
                }, 'json');
                return false;
            });

            // 修改
            $('.layui-table').on('click', '.edit-good', function () {
                var id  = $(this).attr('data-id');
                $.post('{{ route("frontend.setting.automatically-grab.show") }}', {id:id}, function (result) {
                    if (result) {
                        layer.open({
                            type: 1,
                            shade: 0.2,
                            title: '修改',
                            area: ['500px', '400px'],
                            content: result,
                            success: function(layero, index){
                                form.render();
                            }
                        });
                    }
                }, 'json');
                return false;
            });
            // 保存修改
            form.on('submit(goods-edit-save)', function (data) {
                $.post('{{ route('frontend.setting.automatically-grab.edit') }}', {
                    id:data.field.id,
                    foreign_goods_id: data.field.foreign_goods_id,
                    remark: data.field.remark,
                    game_id: data.field.game_id,
                    seller_nick: data.field.seller_nick
                }, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        setTimeout(function () {
                            location.reload();
                        }, 700);
                    }
                }, 'json');
                return false;
            });


            // 删除
            form.on('submit(delete-goods)', function (data) {
                layer.confirm('您确定要删除吗?', {icon: 3, title:'提示'}, function(){
                    $.post('{{ route('frontend.setting.automatically-grab.delete') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        if (result.status == 1) {
                            setTimeout(function () {
                                location.reload();
                            }, 700);
                        }
                    }, 'json');
                });
                return false;
            });

            $('.cancel').click(function () {
               layer.closeAll();
            });
        });
    </script>
@endsection