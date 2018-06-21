@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单')

@section('css')
    <style>
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>配置管理</span></li>
                <li class="active"><span>标品下单</span></li>
                <li class="active"><span>价格公式</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <input type="hidden" class="layui-input" name="game_id"  placeholder="请输入" value="{{ $gameId }}">
                        <input type="hidden" class="layui-input" name="game_name"  placeholder="请输入" value="{{ $gameName }}">
                        <input type="hidden" class="layui-input" name="type"  placeholder="请输入" value="{{ $type }}">
                        <div class="row">
                                <label class="layui-form-label">代练层级</label>
                                <div class="form-group col-xs-2">
                                    <input type="text" class="layui-input" name="game_leveling_number"  placeholder="请输入" value="{{ $gameLevelingNumber }}">
                                </div>
                            <button lbuttony-submit="search"  lay-filter="search" class="layui-btn layui-btn-normal">查询</button>
                            <a lay-submit="add" href="{{ route('config.leveling.price.create', ['game_id' => $gameId, 'game_name' => $gameName, 'type' => $type]) }}" lay-filter="add" class="layui-btn layui-btn-normal">新增</a>
                            <button type="button" class="layui-btn layui-btn-normal" id="test3">导入</button>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <div id="notice">
                        @include('backend.leveling.price.list', ['datas' => $datas])
                    </div>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $datas->total() }}　本页显示：{{$datas->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $datas->appends([
                                    'game_leveling_number' => $gameLevelingNumber,
                                  ])->render() !!}
                            </div>
                        </div>
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

        upload.render({
            elem: '#test3'
            ,url: '{{ Route('config.leveling.price.import') }}'
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

        // 删除单个
        form.on('submit(delete)', function (data) {
            var id=this.getAttribute('data-id');
            var s = window.location.search;
            var page=s.getAddrVal('page'); 
            var game_id = this.getAttribute('data-game-id');
            var game_name = this.getAttribute('data-game-name');
            var type = this.getAttribute('data-type');
            layer.confirm('确认删除吗？', {icon: 3, title:'提示'}, function(index){
                $.post("{{ route('config.leveling.price.delete') }}", {id:id}, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        if (page) {
                            $.get("{{ route('config.leveling.price.index') }}?page="+page, {game_id:game_id, game_name:game_name, type:type}, function (result) {
                                $('#notice').html(result);
                                form.render();
                            }, 'json');
                        } else {
                            $.get("{{ route('config.leveling.price.index') }}", {game_id:game_id, game_name:game_name, type:type}, function (result) {
                                $('#notice').html(result);
                                form.render();
                            }, 'json');
                        }
                    }
                });
                layer.close(index);
            });
            return false;
        });

        String.prototype.getAddrVal = String.prototype.getAddrVal||function(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var data = this.substr(1).match(reg);
            return data!=null?decodeURIComponent(data[2]):null;
        }
    });

</script>
@endsection