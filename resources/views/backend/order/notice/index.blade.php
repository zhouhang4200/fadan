@extends('backend.layouts.main')

@section('title', ' | 接口报警订单列表')

@section('css')
    <style>
        .layui-form-pane .layui-form-label {
            width: 120px;
            padding: 8px 15px;
            height: 36px;
            line-height: 20px;
            border-radius: 2px 0 0 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            box-sizing: border-box;
        }
        blockquote:before{
            content: ""
        }
        .theme-whbl blockquote, .theme-whbl blockquote.pull-right{
            border-color: #e6e6e6;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>订单管理</span></li>
                <li class="active"><span>接口失败报警</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <label class="layui-form-label">内部单号</label>
                            <div class="form-group col-xs-2">
                                <input type="text" name="order_no" autocomplete="off" class="layui-input" placeholder="请输入" value="{{ $orderNo }}">
                            </div>
                                <label class="layui-form-label">内部状态</label>
                            <div class="form-group col-xs-2">
                                <select name="status"  lay-search="">
                                    <option value="">请选择</option>
                                    @foreach(config('order.status_leveling') as $key => $value)
                                        <option value="{{ $key }}" @if($key == $status && is_numeric($status)) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="layui-form-label">发布时间</label>
                            <div class="form-group col-xs-2">
                                <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ $endDate }}">
                            </div>
                                <button lay-submit="" lay-filter="search" class="layui-btn layui-btn-normal ">搜索</button>
                                <button lay-submit="" lay-filter="delete-all" class="layui-btn layui-btn-normal ">删除全部</button>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <div id="notice">
                        @include('backend.order.notice.list', ['orders' => $orders])
                    </div>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $orders->total() }}　本页显示：{{$orders->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $orders->appends([
                                    'order_no' => $orderNo,
                                    'status' => $status,
                                    'startDate' => $startDate,
                                    'endDate' => $endDate,
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

        //日期
        laydate.render({
            elem: '#startDate'
        });
        laydate.render({
            elem: '#endDate'
        });

         // 删除所有
        form.on('submit(delete-all)', function (data) {
            var id=this.getAttribute('lay-id');
            var s = window.location.search;
            var page=s.getAddrVal('page'); 
            layer.confirm('确认删除所有报警记录吗？', {icon: 3, title:'提示'}, function(index){
                $.post("{{ route('order.notice.delete-all') }}", {id:id}, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        $.get("{{ route('order.notice.index') }}?page="+page, function (result) {
                            $('#notice').html(result);
                            form.render();
                        }, 'json');
                    }
                });
                layer.close(index);
            });
            return false;
        });

        // 删除单个
        form.on('submit(delete)', function (data) {
            var id=this.getAttribute('lay-id');
            var s = window.location.search;
            var page=s.getAddrVal('page'); 
            layer.confirm('确认删除吗？', {icon: 3, title:'提示'}, function(index){
                $.post("{{ route('order.notice.delete') }}", {id:id}, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        $.get("{{ route('order.notice.index') }}?page="+page, function (result) {
                            $('#notice').html(result);
                            form.render();
                        }, 'json');
                    }
                });
                layer.close(index);
            });
            return false;
        });

         // 重发
        form.on('submit(repeat)', function (data) {
            var id=this.getAttribute('lay-id');
            var s = window.location.search;
            var page=s.getAddrVal('page'); 
            $.post("{{ route('order.notice.repeat') }}?page="+page, {id:id}, function (result) {
                layer.msg(result.message);
                if (result.status > 0) {
                    $.get("{{ route('order.notice.index') }}", function (result) {
                        $('#notice').html(result);
                        form.render();
                    }, 'json');
                }
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