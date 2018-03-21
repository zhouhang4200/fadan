@extends('frontend.layouts.app')

@section('title', '设置 - 发单设置')

@section('css')
    
@endsection

@section('submenu')
    @include('frontend.setting.submenu')
@endsection

@section('main')
    <div class="explanation">
        <div class="ex_tit" style="line-height: 35px;"><i class="sc_icon"></i><h4>操作提示</h4>
            <span id="explanationZoom" title="收起提示" class=""></span>
        </div>
        <ul>
            <li style="line-height: 30px;">该功能可以控制订单“重发”功能所发出的订单的客服人员。如果选择“首次发单客服”，则重发的订单的发单客服为该订单第一次创建时的客服；如果选择“当前发单客服”，则重发的订单的发单客服为该订单重发时的客服。</li>
        </ul>
    </div>
    <form class="layui-form  layui-form-pane" action="">
        <div class="layui-form-item" pane style="height: 33px;padding-left:10px">
            <lable  class="layui-form-label" >重发订单客服</lable>
            <div class="layui-input-block">
                <input type="radio" name="sending-control" value="0" title="首次发单客服"  lay-filter="sending-control" @if($sendingControl == 0) checked @endif>
                <input type="radio" name="sending-control" value="1" title="当前发单客服" lay-filter="sending-control" @if($sendingControl == 1) checked @endif>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use(['form', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;

             form.on('radio(sending-control)', function(data){
                type = data.value;
                $.post('{{ route('frontend.setting.sending-control.change') }}', {type:type}, function (result) {
                    layer.msg(result.message);
                });
            });
        });
    </script>
@endsection