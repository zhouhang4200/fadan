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
                                    <input type="text" class="layui-input" name="id"  placeholder="账号ID" value="{{ $id }}">
                                </div>
                                <div class=" col-xs-2">
                                    <input type="text" class="layui-input" name="nickname"  placeholder="别名" value="{{ $nickname }}">
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
                                    'user_id' => $id,
                                    'nickname' => $nickname,
                                ])->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="add" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">

            <div class="layui-form-item">
                <label class="layui-form-label">ID</label>
                <div class="layui-input-block">
                    <input type="text" name="id" autocomplete="off" class="layui-input layui-disabled" readonly value="">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">金额</label>
                <div class="layui-input-block">
                    <input type="text" name="amount" autocomplete="off" placeholder="请输入金额" class="layui-input" lay-verify="required|number">
                </div>
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="recharge">确定</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
   <script>
        layui.use(['form', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer;

            // 手动加款按钮
            form.on('submit(recharge-windows)', function(data){
                $('input[name=id]').val(data.elem.getAttribute('data-id'));
                $('input[name=name]').val(data.elem.getAttribute('data-name'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '扣保证金',
                    content: $('#add')
                });
                return false;
            });
            form.on('submit(recharge)', function(data){
                layer.confirm('您确认要扣用户ID为: ' + data.field.id  +' 商户保证金' + data.field.amount + ' 元吗？', {icon: 3, title:'提示'}, function(index){
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