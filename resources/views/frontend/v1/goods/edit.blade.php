@extends('frontend.v1.layouts.app')

@section('title', '商品 - 修改商品')

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
        <form class="layui-form" action="">
        <input type="hidden" name="id" value="{{ $goods->id }}">
            <div class="layui-form-item">
                <label class="layui-form-label">类型</label>
                <div class="layui-input-block">
                    <select name="service_id" lay-filter="aihao" lay-verify="required">
                        <option value=""></option>
                        @forelse($services as $key => $val)
                            <option value="{{ $key }}" {{ $goods->service && $goods->service->id == $key ? 'selected' : '' }}>{{ $val }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">游戏</label>
                <div class="layui-input-block">
                    <select name="game_id" lay-filter="aihao" lay-verify="required">
                        <option value=""></option>
                        @forelse($games as $key => $val)
                            <option value="{{ $key }}" {{ $goods->game && $goods->game->id == $key ? 'selected' : '' }}>{{ $val }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">商品名</label>
                <div class="layui-input-block">
                    <input type="text" name="name" autocomplete="off" value="{{ old('name') ?: $goods->name }}" placeholder="请输入标题" class="layui-input" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">单价</label>
                <div class="layui-input-block">
                    <input type="text" name="price" autocomplete="off" value="{{ old('price') ?: $goods->price }}" placeholder="请输入单价" class="layui-input" lay-verify="required|number">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">数量</label>
                <div class="layui-input-block">
                    <input type="text" name="quantity" autocomplete="off" value="{{ old('quantity') ?: $goods->quantity }}" placeholder="可用数量与外部商品ID匹配到此商品" class="layui-input" lay-verify="required|number">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">游戏币数</label>
                <div class="layui-input-block">
                    <input type="text" name="game_gold" autocomplete="off" value="{{ old('game_gold') ?: $goods->game_gold }}" placeholder="如：100" class="layui-input" lay-verify="required|number">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">游戏币单位</label>
                <div class="layui-input-block">
                    <input type="text" name="game_gold_unit" autocomplete="off" value="{{ old('game_gold_unit') ?: $goods->game_gold_unit }}" placeholder="如：点券" class="layui-input" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">外部ID</label>
                <div class="layui-input-block">
                    <input type="text" name="foreign_goods_id" value="{{ old('foreign_goods_id') ?: $goods->foreign_goods_id }}" autocomplete="off" placeholder="请输入外部ID" class="layui-input"  lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">显示排序</label>
                <div class="layui-input-block">
                    <input type="text" name="sortord" value="{{ old('sortord') ?: $goods->sortord }}" autocomplete="off" placeholder="请输入显示排序" class="layui-input"  lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">允许亏本转单</label>
                <div class="layui-input-inline">
                    <input type="radio" name="loss" value="0" title="否" {{ $goods->loss == 0 ? 'checked' : '' }}>
                    <input type="radio" name="loss" value="1" title="是" {{ $goods->loss == 1 ? 'checked' : '' }}>
                </div>
                <div class="layui-form-mid layui-word-aux">如果设置为"是"则API的风控设置就无效</div>
            </div>
            <div class="layui-form-item" pane="">
                <label class="layui-form-label">在工作台显示</label>
                <div class="layui-input-block">
                    <input type="radio" name="display" value="1" title="是" {{ $goods->display == 1 ? 'checked' : '' }}>
                    <input type="radio" name="display" value="0" title="否" {{ $goods->display == 0 ? 'checked' : '' }}>
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue" lay-submit="" lay-filter="update">确认修改</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form, layer = layui.layer;

            //监听提交
            form.on('submit(update)', function(data){
                $.post("{{ route('frontend.goods.update') }}", {data:data.field}, function (result) {
                    layer.alert(result.message);
                }, 'json');
                return false;
            });
        });
    </script>
@endsection
