@extends('frontend.v1.layouts.app')

@section('title', '账号 - 打手黑名单')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-layer-btn{
            text-align:center !important;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
        <form class="layui-form" method="" action="" >
                <div class="layui-inline" style="float:left">
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: 50px; padding-left: 0px;">打手昵称</label>
                    <div class="layui-input-inline">               
                        <select name="hatchet_man_name" lay-verify="" lay-search="">
                            <option value="">输入或选择</option>
                            @forelse($hatchetMans as $hatchetMan)
                                <option value="{{ $hatchetMan->hatchet_man_name }}" {{ $hatchetMan->hatchet_man_name == $hatchetManName ? 'selected' : '' }}>{{ $hatchetMan->hatchet_man_name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <label class="layui-form-label" style="width: 45px; padding-left: 0px;">电话</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" value="{{ $hatchetManPhone ?? '' }}" name="hatchet_man_phone" placeholder="请输入">
                    </div>
                    <label class="layui-form-label" style="width: 45px; padding-left: 0px;">QQ</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" value="{{ $hatchetManQq ?? '' }}" name="hatchet_man_qq" placeholder="请输入">
                    </div>
                </div>
            </div>
            <div style="float: left">
                <div class="layui-inline" >
                    <button class="qs-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px"><i class="iconfont icon-search"></i><span style="padding-left: 3px">查询</span></button>
                    &nbsp;
                    <a href="{{ route('hatchet-man-blacklist.create') }}" style="color:#fff; float:right;" class="qs-btn layui-btn-normal layui-btn-small"><i class="iconfont icon-add"></i><span style="padding-left: 3px">新增</span></a>
                </div>
            </div>                     
        </form>
        <div class="layui-tab-item layui-show" lay-size="sm" id="staff">
        @include('frontend.v1.user.hatchet-man-blacklist.list')
            {!! $hatchetManBlacklists->appends([
                'hatchetManName' => $hatchetManName,
                'hatchetManPhone' => $hatchetManPhone,
                'hatchetManQq' => $hatchetManQq,
            ])->render() !!}
        </div>
    </div>
</div>
@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            var form = layui.form;
            // 获取路由后面的参数
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
                            url: "{{ route('hatchet-man-blacklist.delete') }}",
                            data:{id:id},
                            success: function (data) {
                                layer.msg(data.message);
                                if (page) {
                                    $.get("{{ route('hatchet-man-blacklist.index') }}?page="+page, function (result) {
                                        $('#staff').html(result);
                                        form.render();
                                    }, 'json');
                                } else {
                                    $.get("{{ route('hatchet-man-blacklist.index') }}", function (result) {
                                        $('#staff').html(result);
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
    </script>
@endsection