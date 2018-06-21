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
                                        <label class="layui-form-label">*游戏</label>
                                        <div class="layui-input-block">
                                            <select name="game_id" lay-filter="game" lay-verify="required" lay-search="">
                                                <option value="">请选择</option>
                                                    @forelse($games as $game)
                                                    <option value="{{ $game->id }}" @if($data->game_id == $game->id) selected @endif>{{ $game->name }}</option>
                                                    @empty
                                                    @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练类型</label>
                                        <div class="layui-input-block">
                                            <select name="game_leveling_type" lay-filter="" lay-verify="required" lay-search="" id="type">
                                                <option value="">请选择</option>
                                                @forelse($types as $type)
                                                <option value="{{ $type }}" @if($data->game_leveling_type == $type) selected @endif>{{ $type }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练说明</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入内容" name="game_leveling_instructions" lay-verify="required" class="layui-textarea">{{ $data->game_leveling_instructions }}</textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练要求</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入内容" name="game_leveling_requirements" lay-verify="required" class="layui-textarea">{{ $data->game_leveling_requirements }}</textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*商户QQ</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="user_qq" lay-verify="required" value="{{ $data->user_qq }}" autocomplete="off" placeholder="请输入qq" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*发单价格固定比例</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="rebate" lay-verify="required" value="{{ $data->rebate }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-id="{{ $data->id }}" lay-submit="update" lay-filter="update">立即提交</button>
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
            $.post("{{ route('config.leveling.update') }}", {id:id, data:data.field}, function (result) {
                if (result.status == 1) {
                    layer.msg(result.message);
                    window.location.href="{{ route('config.leveling.index') }}";
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