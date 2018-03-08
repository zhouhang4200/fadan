@extends('backend.layouts.main')

@section('title', ' | 添加违规')

@section('css')
    <style>
        .layui-form-select .layui-edge {
            right:5px;
        }
        .layui-tab-content input {
            width: 200px;
        }
        .table {
            width:800px;
        }
        .layui-form-item .layui-input-inline {
            float: left;
            width: 220px;
            margin-right: 10px;
        }
        .layui-form-checkbox span {
            padding: 0 5px;
        }
        .layui-form-checked[lay-skin="primary"] i {
            color: #fff;
            background-color: #1E9FFF;
            border-color: #1E9FFF;
        }
        .layui-form-label {
            width: 200px;
        }

        .layui-select-title {
            margin-bottom: 20px;
        }
        .layui-input-item .layui-input{
            width:492px;
        }
        .layui-input-block .layui-input{
            width:492px;
        }
        .layui-textarea {
            width:492px;
            height:300px;
        }
        dd .layui-select-tips {
            width:492px;
        }
        .layui-anim .layui-anim-upbit{
            width:492px;
        }
        .layui-form-select dl {
            min-width: 492px;
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
                            <li class="layui-this" lay-id="add">游戏配置</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="">
                            {!! csrf_field() !!}
                                <div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">第三方平台</label>
                                            <div class="layui-input-inline">
                                            <select name="third_id" lay-verify="required" lay-filter="third" lay-search="">
                                                <option value="">请选择</option>
                                                <option value="1">91代练</option>
                                                <option value="2">代练妈妈</option>
                                            </select>
                                            </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">游戏名称</label>
                                        <div class="layui-input-inline">
                                            <select name="game_id" lay-verify="required" lay-search="">
                                                <option value="">请选择</option>
                                                @forelse($games as $k => $game)
                                                <option value="{{ $game->id }}-{{ $game->name }}">{{ $game->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">第三方游戏名称</label>
                                        <div class="layui-input-inline">
                                            <select name="third_game_id" id="third_game_name" lay-verify="required" lay-search="">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="config-game">立即提交</button>
                                        </div>
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
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            //常规用法
            laydate.render({
            elem: '#test1'
            });
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;
            form.on('submit(config-game)', function (data) {
                 $.post('{{ route('config.third-games') }}', {data:data.field}, function(result){
                        if (result.status == 1) {
                            layer.alert(result.message);
                        } else {
                            layer.alert(result.message);
                        }
                    }, 'json');
                return false;
            });

            form.on('select(third)', function (data) {
                $.post('{{ route('config.get-third-games') }}', {third_id:data.value}, function(result){
                    if (result.status == 1) {
                        var thirdgames = '';
                        $.each(result.third_games, function (i, item) {
                            itemvalue = item.replace(/[a-zA-Z0-9]+\-/i, '');
                            thirdgames += "<option value='"+item+"'>"+itemvalue+"</option>";
                        })
                        console.log(thirdgames);
                        $('#third_game_name').html('<option value="请选择"></option>' + thirdgames);
                        form.render('select');
                    } else {
                        layer.alert(result.message);
                    }
                }, 'json');
            });
          form.render();
    });  
    </script>
@endsection