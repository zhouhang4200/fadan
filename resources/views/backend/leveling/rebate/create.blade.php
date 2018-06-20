@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单-新增')

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
                            <li class="layui-this" lay-id="">新增</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="" action="">
                                {!! csrf_field() !!}
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*提升层级</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="level_count" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练价格折扣</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="rebate" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" game-id="{{ $gameId }}" game-name="{{ $gameName }}" type="{{ $type }}" lay-submit="add" lay-filter="add">立即提交</button>
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

        form.on('submit(add)', function (data) {
            var game_id=this.getAttribute('game-id');
            var game_name=this.getAttribute('game-name');
            var type=this.getAttribute('type');
            $.post("{{ route('config.leveling.rebate.store') }}", {data:data.field, game_id:game_id, game_name:game_name, type:type}, function (result) {
                if (result.status == 1) {
                    layer.msg(result.message);
                    window.location.href="{{ route('config.leveling.rebate.index') }}?game_id="+game_id+'&game_name='+game_name+'&type='+type;
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