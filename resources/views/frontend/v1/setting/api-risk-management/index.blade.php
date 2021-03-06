@extends('frontend.v1.layouts.app')

@section('title', '设置 - API下单风控')

@section('css')

@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
        <blockquote class="layui-elem-quote">
            操作提示: 该参数的意义是：当您的终端销售价低于您在本平台配置的商品价格时，则下单价不取配置的商品价格，而是取的(风控值 X 终端销售)得出最终下单价格，保证您的利益。平台默认值：0.98
        </blockquote>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-inline">
                <label class="layui-form-label">风控值</label>
                <div class="layui-input-inline">
                    <input type="text" name="rate" autocomplete="off" class="layui-input" value="{{ $riskRate }}" placeholder="例如：0.98" lay-verify="required|number">
                </div>
                <button class="qs-btn layui-btn-normal" lay-submit="" lay-filter="save">保存</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;

            form.on('submit(save)', function () {
                $.post('{{ route('frontend.setting.api-risk-management.set') }}', {rate:$('input[name=rate]').val()}, function (result) {
                    layer.msg(result.message);
                });
               return false;
            });
        });
    </script>
@endsection