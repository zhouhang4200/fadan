@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单-修改')

@section('css')
<style>
        .layui-form-label {
            width:140px;
        }

        .layui-input, .layui-textarea {
            display: block;
            width:300px;
            /* width: 100%; */
            padding-left: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="">修改</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="" action="">
                                {!! csrf_field() !!}
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*序号</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="game_leveling_number" lay-verify="required|number" value="{{ $data->game_leveling_number }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练层级</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="game_leveling_level" lay-verify="required" value="{{ $data->game_leveling_level }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*到下一层级价格</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="level_price" lay-verify="required|number" value="{{ $data->level_price+0 }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*到下一层级耗时</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="level_hour" lay-verify="required|number" value="{{ $data->level_hour }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*该层级安全保证金</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="level_security_deposit" lay-verify="required|number" value="{{ $data->level_security_deposit+0 }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*该层级效率保证金</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="level_efficiency_deposit" lay-verify="required|number" value="{{ $data->level_efficiency_deposit+0 }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-submit="update" lay-filter="update" lay-id="{{ $data->id }}" game-id="{{ $data->game_id }}" game-name="{{ $data->game_name }}" type="{{ $data->game_leveling_type }}">立即提交</button>
                                        </div>
                                    </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    //Demo
    layui.use(['form', 'layedit', 'laytpl', 'element', 'laydate', 'table', 'upload'], function(){
        var form = layui.form, layer = layui.layer, laydate = layui.laydate, layTpl = layui.laytpl,
                element = layui.element, table=layui.table, upload = layui.upload;

        form.on('submit(update)', function (data) {
            var id=this.getAttribute('lay-id');
            var game_id=this.getAttribute('game-id');
            var game_name=this.getAttribute('game-name');
            var type=this.getAttribute('type');
            $.post("{{ route('config.leveling.price.update') }}", {id:id, data:data.field}, function (result) {
                if (result.status == 1) {
                    layer.msg(result.message);
                    window.location.href="{{ route('config.leveling.price.index') }}?game_id="+game_id+'&game_name='+game_name+'&type='+type;
                } else {
                    layer.msg(result.message);
                }
                return false;
            });
                return false;
        });
    });
</script>
@endsection