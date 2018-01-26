@extends('backend.layouts.main')

@section('title', ' | 商户保证金列表')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商户保证金列表</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <div class="filter-block">
                        <form class="layui-form">
                            <div class="row">
                                <div class=" col-xs-2">
                                    <input type="text" class="layui-input" name="user_id"  placeholder="账号ID" value="{{ $userId }}">
                                </div>
                                <div class=" col-xs-2">
                                    <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $cautionMoneys->total() }}　本页显示：{{$cautionMoneys->count()}}
                        </div>
                        <div class="col-xs-9">
                        </div>
                    </div>
                    <table class="layui-table layui-form" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>商户ID</th>
                                    <th>商户别名</th>
                                    <th>保证金类型</th>
                                    <th>保证金</th>
                                    <th>扣款时间</th>
                                    <th style="text-align: center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($cautionMoneys as $item)
                                    <tr>
                                        <td>{{ $item->user_id }}</td>
                                        <td>{{ optional($item->user)->nickname }}</td>
                                        <td>{{ config('cautionmoney')[$item->type] }}</td>
                                        <td>{{ $item->amount }}</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td style="text-align: center;">
                                            <button  class="layui-btn layui-btn layui-btn-normal layui-btn-mini" lay-submit lay-filter="refund" data-id="{{ $item->id }}" data-user_id="{{ $item->user_id }}" data-amount="{{ $item->amount }}">退返保证金</button>
                                            <button  class="layui-btn layui-btn layui-btn-normal layui-btn-mini" lay-submit lay-filter="deduction" data-id="{{ $item->id }}" data-user_id="{{ $item->user_id }}" data-amount="{{ $item->amount }}">扣除保证金</button>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $cautionMoneys->total() }}　本页显示：{{$cautionMoneys->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $cautionMoneys->appends([
                                    'user_id' => $userId,
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
        layui.use(['form', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer;

            // 手动加款按钮
            form.on('submit(refund)', function(data){
                var id  = data.elem.getAttribute('data-id');
                var userId  = data.elem.getAttribute('data-user_id');

                layer.confirm('您确认要将保证金:' +  + data.field.amount + '退给商户' + userId   + ' 吗?', {icon: 3, title:'提示'}, function(index){
                    $.post('{{ route('frontend.user.recharge') }}', {id:id}, function(result){
                        layer.msg(result.message)
                    }, 'json');
                    layer.closeAll();
                });
                return false;
            });
            // 扣除
            form.on('submit(deduction)', function(data){
                var id  = data.elem.getAttribute('data-id');

                layer.confirm('您确认要扣除商户ID为: ' + data.field.id  +'的商户保证金 ' + data.field.amount + ' 元吗？', {icon: 3, title:'提示'}, function(index){
                    $.post('{{ route('frontend.user.recharge') }}', {id:data.field.id, amount:data.field.amount}, function(result){
                        layer.msg(result.message)
                    }, 'json');
                    layer.closeAll();
                });
                return false;
            });
        });
    </script>
@endsection