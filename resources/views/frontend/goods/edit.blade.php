@extends('frontend.layouts.app')

@section('title', '商品 - 修改商品')

@section('submenu')
    @include('frontend.goods.submenu')
@endsection

@section('main')
    <form class="layui-form layui-form-pane" action="">
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
            <label class="layui-form-label">外部ID</label>
            <div class="layui-input-block">
                <input type="text" name="foreign_goods_id" value="{{ old('foreign_goods_id') ?: $goods->foreign_goods_id }}" autocomplete="off" placeholder="请输入外部ID" class="layui-input"  lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item" pane="">
            <label class="layui-form-label">是否显示</label>
            <div class="layui-input-block">
                <input type="checkbox" {{ $goods->display == 1 ? 'checked' : '' }} name="display" lay-skin="switch" lay-filter="switchTest" title="开关">
            </div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn layui-bg-blue" lay-submit="" lay-filter="update">确认修改</button>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form, layer = layui.layer;

            //监听提交
            form.on('submit(update)', function(data){

                $.post("{{ route('frontend.goods.update') }}", {data:data.field}, function (result) {
                    layer.alert(result.message, {
                        title: '最终的提交信息'
                    });
                    window.location.href="{{ route('frontend.goods.index') }}";
                }, 'json');
                return false;
            });
        });
    </script>
@endsection