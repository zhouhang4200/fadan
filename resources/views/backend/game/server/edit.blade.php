@extends('backend.layouts.main')

@section('title', ' | 游戏服修改')

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
                            <li class="layui-this" lay-id="add">游戏服修改</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="" action="">
                                {!! csrf_field() !!}
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*游戏</label>
                                    <div class="layui-input-block">
                                        <select name="game_id" lay-verify="" lay-filter="game_id"  lay-search="">
                                            <option value="">输入或选择</option>
                                            @forelse($games as $game)
                                                <option value="{{ $game->id }}" {{ $server->gameRegion->game->id == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*游戏区</label>
                                    <div class="layui-input-block">
                                        <select name="game_region_id" lay-verify="" lay-search="" id="game_region_id">
                                            @forelse($regions as $region)
                                                <option value="{{ $region->id }}" {{ $server->gameRegion->id == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*游戏服</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="name" lay-verify="required" value="{{ $server->name }}" autocomplete="off" class="layui-input">
                                        <input type="hidden" name="id"  value="{{ $server->id }}" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="update">确认</button>
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

            // 取消按钮
            $('.cancel').click(function () {
                window.location.href="{{ route('admin.server.index') }}";
            });
            // 游戏区
            form.on('select(game_id)', function (data) {
                $.post('{{ route('admin.server.region') }}', {game_id:data.value}, function(result){
                    if (result.status == 1) {
                        var region = '';
                        $.each(result.message, function (i, item) {
                            region += "<option value='"+i+"'>"+item+"</option>";
                        })
                        $('#game_region_id').html('<option value="请选择"></option>' + region);
                        form.render('select');
                    } else {
                        layer.alert(result.message);
                    }
                }, 'json');

            });
            // 修改
            form.on('submit(update)', function (data) {
                $.post("{{ route('admin.server.update') }}", {
                    name:data.field.name,
                    game_region_id:data.field.game_region_id,
                    id:data.field.id
                }, function (result) {
                    layer.msg(result.message);
                });
                return false;
            });
        });
    </script>
@endsection