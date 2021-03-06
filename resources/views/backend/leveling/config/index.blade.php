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
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                                <label class="layui-form-label" style="width:55px;">游戏</label>
                                <div class="form-group col-xs-2">
                                    <select  name="game_id"  lay-search="">
                                        <option value="">请选择</option>
                                        @forelse($games as $game)
                                            <option value="{{ $game->id }}" @if($game->id == $gameId) selected  @endif>{{ $game->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            <button lbuttony-submit="search"  lay-filter="search" class="layui-btn layui-btn-normal">查询</button>
                            <a lay-submit="add" href="{{ route('config.leveling.create') }}" lay-filter="add" class="layui-btn layui-btn-normal">新增</a>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <div id="notice">
                        @include('backend.leveling.config.list', ['datas' => $datas])
                    </div>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $datas->total() }}　本页显示：{{$datas->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $datas->appends([
                                    'game_id' => $gameId,
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

        // 删除单个
        form.on('submit(delete)', function (data) {
            var id=this.getAttribute('data-id');
            var s = window.location.search;
            var page=s.getAddrVal('page'); 
            layer.confirm('确认删除吗？', {icon: 3, title:'提示'}, function(index){
                $.post("{{ route('config.leveling.delete') }}", {id:id}, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        if (page) {
                            $.get("{{ route('config.leveling.index') }}?page="+page, function (result) {
                                $('#notice').html(result);
                                form.render();
                            }, 'json');
                        } else {
                            $.get("{{ route('config.leveling.index') }}", function (result) {
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