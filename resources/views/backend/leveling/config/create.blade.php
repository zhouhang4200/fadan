@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单-新增')

@section('css')
    <style>
        .layui-form-label {
            width:140px;
        }

        .layui-form-select dl {
            position: relative; 
            min-width: 0px; 
            top:0px;
            width: 300px;
        }

        .layui-edge {
            display: none;
        }

        .layui-input, .layui-textarea {
            display: block;
            width:300px;
            /* width: 100%; */
            padding-left: 10px;
        }
        .tips {
            position: absolute;
            width: 10%;
            height: 30px;
            right: -35px;
            top: 5px;
            text-align: center
        }

        .tips .iconfont {
            left: -5px;
            font-size: 25px;
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
                                                    <option value="{{ $game->id }}">{{ $game->name }}</option>
                                                    @empty
                                                    @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练类型</label>
                                        <div class="layui-input-block">
                                            <select name="game_leveling_type" lay-filter="" lay-verify="" lay-search="" id="type">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练说明</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入内容" name="game_leveling_instructions" lay-verify="required" class="layui-textarea"></textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练要求</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入内容" name="game_leveling_requirements" lay-verify="required" class="layui-textarea"></textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*商户QQ</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="user_qq" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*发单价格固定比例</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="rebate" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-submit="add" lay-filter="add">立即提交</button>
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
            $.post("{{ route('config.leveling.store') }}", {data:data.field}, function (result) {
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

        form.on('select(game)', function (data) {
            $.post('{{ route('config.leveling.type') }}', {game_id:data.value}, function(result){
                if (result.status == 1) {
                    var types = '';
                    $.each(result.types, function (i, item) {
               
                        types += "<option value='"+item+"'>"+item+"</option>";
                    })
                    $('#type').html('<option value="请选择"></option>' + types);
                    form.render('select');
                } else {
                    layer.alert(result.message);
                }
            }, 'json');
        });
    });
</script>
@endsection