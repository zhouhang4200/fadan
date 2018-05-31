@extends('frontend.v1.layouts.app')

@section('title', '设置 - 发布渠道设置')

@section('css')
    <style>
        .layui-form-item .layui-input-inline {
            float: left;
            width: 150px;
            margin-right: 10px;
        }
        .layui-form-checked[lay-skin="primary"] span {     
            background-color: #fff;
        }
    </style>
@endsection

@section('main') 
<div class="layui-card qs-text">
    <div class='layui-card-header'>
        <ul class="layui-tab layui-tab-title layui-tab-brief ">
            <li @if(Route::currentRouteName() == 'frontend.setting.sending-assist.auto-markup') class = 'layui-this' @endif)><a href="{{ route('frontend.setting.sending-assist.auto-markup') }}">自动加价配置</a></li>
            <li @if(Route::currentRouteName() == 'frontend.setting.order-send-channel.index') class = 'layui-this'@endif><a href="{{ route('frontend.setting.order-send-channel.index') }}">发布渠道设置</a></li>
        </ul>
    </div>
    <div class="layui-card-body">
        <blockquote class="layui-elem-quote">
            操作提示：发布渠道设置可以控制发布的订单所能转单的平台，每种游戏至少选择一家平台。
        </blockquote>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show" id="show">
                @include('frontend.v1.setting.sending-assist.send-channel-list')
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        layui.use(['element', 'form'], function(){
            var $ = layui.jquery,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
            var form = layui.form, layer = layui.layer, table = layui.table;

            form.on('checkbox(set)', function (data) {
                var game_id = this.getAttribute('lay-game-id');
                var game_name = this.getAttribute('lay-game-name');
                var thirds=[];
                $("input:checkbox[name='third"+game_id+"']:checked").each(function() { // 遍历name=test的多选框
                    $(this).val();  // 每一个被选中项的值
                    thirds.push($(this).val());
                });

                $.post("{{ route('frontend.setting.order-send-channel.set') }}", {thirds:thirds, game_name:game_name, game_id:game_id}, function (result) {
                    layer.msg(result.message);
                    if (result.status == 1) {
                        $.get("{{ route('frontend.setting.order-send-channel.index') }}", function (result) {
                            $('#show').html(result);
                            form.render();
                        }, 'json');
                    } else {
                        $.get("{{ route('frontend.setting.order-send-channel.index') }}", function (result) {
                            $('#show').html(result);
                            form.render();
                        }, 'json');
                        form.render();
                    }
                })
                return false;
            })
        })

    </script>
@endsection