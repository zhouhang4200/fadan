@extends('backend.layouts.main')

@section('title', ' | 旺旺黑名单')

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
                <li class=""><span>首页</span></li>
                <li class="active"><span>旺旺黑名单</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="form-group col-xs-3">
                                <input type="text" class="layui-input" name="wang_wang"  placeholder="旺旺号" value="{{ $wangWang }}">
                            </div>

                            <div class="form-group col-xs-2">
                                <button type="submit" class="layui-btn layui-btn-normal">搜索</button>
                            </div>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">

                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $wangWangBlacklist->total() }}　本页显示：{{$wangWangBlacklist->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $wangWangBlacklist->appends([
                                      'wang_wang' => $wangWang,
                                  ])->render() !!}
                            </div>
                        </div>
                    </div>
                    <table class="layui-table layui-form" lay-size="sm">
                        <thead>
                        <tr>
                            <th>旺旺号</th>
                            <th>添加时间</th>
                            <th>添加人</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($wangWangBlacklist as $item)
                            <tr>
                                <td>{{ $item->wang_wang }}</td>
                                <td>{{ $item->adminUser->name }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>
                                    <button class="layui-btn layui-btn-normal">删除</button>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $wangWangBlacklist->total() }}　本页显示：{{$wangWangBlacklist->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $wangWangBlacklist->appends([
                                      'wang_wang' => $wangWang,
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

            // 订单操作
            form.on('select(create)', function (data) {
                eval(data.value + "('" + data.elem.getAttribute('data-no')  + "',"+data.elem.getAttribute('data-id')+")");
            });
            form.on('select(delete)', function (data) {
                eval(data.value + "('" + data.elem.getAttribute('data-no')  + "',"+data.elem.getAttribute('data-id')+")");
            });
        });

    </script>
@endsection