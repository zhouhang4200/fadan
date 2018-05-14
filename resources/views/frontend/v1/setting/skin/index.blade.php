@extends('frontend.v1.layouts.app')

@section('title', '设置 - 皮肤交易QQ')

@section('css')

@endsection

@section('main')
<div class="layui-card qs-text">
<div class="layui-card-body">
    {{--<div class="explanation">--}}
        {{--<div class="ex_tit" style="margin-bottom: 10px;"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示" class=""></span></div>--}}
        {{--<ul>--}}
            {{--<li>该参数的意义是：当您的终端销售价低于您在本平台配置的商品价格时，则下单价不取配置的商品价格，而是取的(风控值 X 终端销售)得出最终下单价格，保证您的利益</li>--}}
            {{--<li>平台默认值：0.98</li>--}}
        {{--</ul>--}}
    {{--</div>--}}
    <form class="layui-form layui-form-pane" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">交易qq</label>
            <div class="layui-input-inline">
                <input type="text" name="qq" autocomplete="off" class="layui-input" value="{{ $skinTradeQQ }}" placeholder="请输入QQ号"  lay-verify="required|number">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">交易wx</label>
            <div class="layui-input-inline">
                <input type="text" name="wx" autocomplete="off" class="layui-input" value="{{ $skinTradeWX }}" placeholder="请输入WX号"  lay-verify="required">
            </div>
        </div>
        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">保存</button>
    </form>
</div>
</div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;

            form.on('submit(save)', function () {
                $.post('{{ route('frontend.setting.skin.set') }}', {qq:$('input[name=qq]').val(), wx:$('input[name=wx]').val()}, function (result) {
                    layer.msg(result.message);
                });
               return false;
            });
        });
    </script>
@endsection