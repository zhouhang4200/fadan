@extends('backend.layouts.main')

@section('title', ' | 游戏代练类型列表')

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
                            <li class="layui-this" lay-id="add">游戏代练类型列表</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="" action="" >
                                <div class="layui-inline" style="float:left">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label" style="width: 100px; padding-left: 0px;">代练类型名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" value="{{ $name }}" autocomplete="off"  class="layui-input">
                                        </div>
                                    </div>
                                </div>
                                <div style="float: left">
                                    <div class="layui-inline" >
                                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查询</button>
                                        <a href="{{ route('admin.leveling.create') }}" style="color:#fff; float:right;" class="layui-btn layui-btn-normal layui-btn-small">新增</a>
                                    </div>
                                </div>
                            </form>

                            <div class="layui-tab-item layui-show" lay-size="sm" id="leveling">
                                @include('backend.game.gamelevelingtype.list')
                                {!! $types->appends([
                                    'name' => $name
                                ])->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<!--START 底部-->
@section('js')
    <script>
        // 时间插件
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            var form = layui.form;
            //常规用法
            laydate.render({
                elem: '#test1'
            });

            //常规用法
            laydate.render({
                elem: '#test2'
            });

            String.prototype.getAddrVal = String.prototype.getAddrVal||function(name){
                var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
                var data = this.substr(1).match(reg);
                return data!=null?decodeURIComponent(data[2]):null;
            }

            // 删除
            form.on('submit(delete)', function (data) {
                var id = data.elem.getAttribute('lay-data');
                var s=window.location.search; //先截取当前url中“?”及后面的字符串
                var page=s.getAddrVal('page');

                layer.confirm('确认删除吗？', {
                    btn: ['确认', '取消']
                    ,title: '提示'
                    ,icon: 3
                }, function(index, layers){
                    $.ajax({
                        type: 'post',
                        url: "{{ route('admin.leveling.delete') }}",
                        data:{id:id},
                        success: function (data) {
                            layer.msg(data.message);
                            if (page) {
                                $.get("{{ route('admin.leveling.index') }}?page="+page, function (result) {
                                    $('#leveling').html(result);
                                    form.render();
                                }, 'json');
                            } else {
                                $.get("{{ route('admin.leveling.index') }}", function (result) {
                                    $('#leveling').html(result);
                                    form.render();
                                }, 'json');
                            }
                        }
                    });
                    layer.closeAll();
                }, function(index){
                    layer.closeAll();
                });
                return false;
            });
        });

        layui.use('form', function(){
            var form = layui.form;
            var layer = layui.layer;

            var succ = "{{ session('succ') ?: '' }}";

            if(succ) {
                layer.msg(succ, {icon: 6, time:1500});        }
            form.render();
        });
    </script>
@endsection