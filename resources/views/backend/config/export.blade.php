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
                            <li class="layui-this" lay-id="add">建立第三方区对应关系</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="get" action="{{ route('config.export') }}">
                            {!! csrf_field() !!}
                                <div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">输入或选择游戏名</label>
                                        <div class="layui-input-inline">
                                            <select name="game_id" lay-verify="required" lay-filter="game_id" lay-search="">
                                                <option value="">请选择</option>
                                            @forelse($games as $game)
                                                <option value="{{ $game->id }}">{{ $game->name }}</option>
                                            @empty
                                            @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="">导出</button>
                                            <button type="button" class="layui-btn layui-btn-normal" id="test3">导入</button>
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
        layui.use(['form', 'layedit', 'laydate', 'upload'], function(){
            var laydate = layui.laydate;
            var form = layui.form; //只有执行了这一步,部分表单元素才会自动修饰成功
            var layer = layui.layer;
            var upload = layui.upload; //得到 upload 对象
            upload.render({
                elem: '#test3'
                ,url: '{{ Route('config.import') }}'
                ,accept: 'file' //普通文件
                ,exts: 'xls'
                ,done: function(res){
                    if (res.status == 1) {
                        layer.alert(res.message);
                    } else {
                        layer.alert(res.message);
                    }
                }
            });            
        form.render();
    });  
    </script>
@endsection