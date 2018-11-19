@extends('backend.layouts.main')

@section('title', ' | 代练类型添加')

@section('css')
    <style>
        .layui-table th, td{
            text-align: center;
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
                            <li class="layui-this" lay-id="add">代练类型添加</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="" action="">
                                {!! csrf_field() !!}
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*游戏名</label>
                                    <div class="layui-input-block">
                                        <select name="game_id" lay-verify="" lay-search="">
                                            <option value="">输入或选择</option>
                                            @forelse($games as $game)
                                                <option value="{{ $game->id }}">{{ $game->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*代练类型名</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="name" lay-verify="required" value="" autocomplete="off" placeholder="添加多个区请用英文逗号隔开，如：陪玩,代练,等级,金币..." class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*手续费</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="poundage" lay-verify="required" value="" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="store">确认</button>
                                        <button type="button" class="layui-btn layui-btn-normal cancel" >取消</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'upload'], function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;
            var $ = layui.jquery;
            var upload = layui.upload;

            form.verify({
                number: [
                    /^[0-9]+$/
                    ,'填写格式不正确，必须为数字'
                ]
            });

            // 取消按钮
            $('.cancel').click(function () {
                window.location.href="{{ route('admin.leveling.index') }}";
            });
            // 新增
            form.on('submit(store)', function (data) {
                $.post("{{ route('admin.leveling.store') }}", {
                    name:data.field.name,
                    game_id:data.field.game_id,
                    poundage:data.field.poundage
                }, function (result) {
                    layer.msg(result.message);
                });
                return false;
            });
        });
    </script>
@endsection