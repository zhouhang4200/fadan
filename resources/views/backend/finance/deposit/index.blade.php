@extends('backend.layouts.main')

@section('title', ' | 商户押金管理')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class=""><span>首页</span></li>
            <li class=""><span>财务</span></li>
            <li class="active"><span>商户押金管理</span></li>
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
                                <input type="text" class="layui-input" name="no"  placeholder="单号" value="{{ Request::input('no') }}">
                            </div>
                            <div class=" col-xs-2">
                                <select name="type">
                                    <option value="">所有类型</option>
                                    @foreach ($config['type'] as $key => $value)
                                        <option value="{{ $key }}" {{ $key == Request::input('type') ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-xs-2">
                                <select name="status">
                                    <option value="">所有状态</option>
                                    @foreach ($config['status'] as $key => $value)
                                        <option value="{{ $key }}" {{ $key == Request::input('status') ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-xs-2">
                                <input type="text" class="layui-input" name="user_id"  placeholder="商户ID" value="{{ Request::input('user_id') }}">
                            </div>
                            <div class=" col-xs-4">
                                <button type="submit" class="layui-btn layui-btn-normal">查询</button>
                                <button type="button" class="layui-btn layui-btn-defalut" id="add-new">扣押金</button>
                            </div>
                        </div>

                    </form>
                </div>
            </header>
            <div class="main-box-body clearfix">
                <table class="layui-table layui-form" lay-size="sm">
                    <thead>
                    <tr>
                        <th>单号</th>
                        <th>商户ID</th>
                        <th>类型</th>
                        <th>状态</th>
                        <th>押金金额</th>
                        <th>备注</th>
                        <th>创建人</th>
                        <th>扣款审核</th>
                        <th>退款人</th>
                        <th>退款审核</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($dataList as $data)
                        <tr>
                            <td>{{ $data->no }}</td>
                            <td>{{ $data->user_id }}</td>
                            <td>{{ $config['type'][$data->type] }}</td>
                            <td>{{ $config['status'][$data->status] }}</td>
                            <td>{{ $data->amount }}</td>
                            <td>{{ $data->remark }}</td>
                            <td>{{ $data->created_by }}</td>
                            <td>{{ $data->deduct_audited_by }}</td>
                            <td>{{ $data->refunded_by }}</td>
                            <td>{{ $data->refunded_audited_by }}</td>
                            <td>{{ $data->created_at }}</td>
                            <td>{{ $data->updated_at }}</td>
                            <td>
                                @switch($data->status)
                                    @case(1)
                                        <button class="layui-btn layui-btn layui-btn-normal layui-btn-mini operate" lay-submit lay-filter="deduction" data-url="{{ route('finance.deposit.deduct-audit', $data->id) }}">财务扣款审核</button>
                                        <button class="layui-btn layui-btn layui-btn-normal layui-btn-mini operate" lay-submit lay-filter="deduction" data-url="{{ route('finance.deposit.deduct-cancel', $data->id) }}">取消扣款</button>
                                        @break
                                    @case(2)
                                        <button class="layui-btn layui-btn layui-btn-normal layui-btn-mini operate" lay-submit lay-filter="deduction" data-url="{{ route('finance.deposit.refund', $data->id) }}">申请退押金</button>
                                        @break
                                    @case(3)
                                        <button class="layui-btn layui-btn layui-btn-normal layui-btn-mini operate" lay-submit lay-filter="deduction" data-url="{{ route('finance.deposit.refund-audit', $data->id) }}">财务退款审核</button>
                                        <button class="layui-btn layui-btn layui-btn-normal layui-btn-mini operate" lay-submit lay-filter="deduction" data-url="{{ route('finance.deposit.refund-cancel', $data->id) }}">取消退押金</button>
                                        @break
                                    @case(4)
                                        --
                                        @break
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="99">没有数据</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-xs-3">
                        总数：{{ $dataList->total() }}　本页显示：{{$dataList->count()}}
                    </div>
                    <div class="col-xs-9">
                        <div class=" pull-right">
                            {!! $dataList->appends(Request::all())->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="add-new-form" style="display: none; padding: 20px 50px 20px 0">
    <form class="layui-form" lay-filter="add-new-form">
        <div class="layui-form-item">
            <label class="layui-form-label">商户ID</label>
            <div class="layui-input-block">
                <input type="text" id="user-id" required lay-verify="number" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">押金类型</label>
            <div class="layui-input-block">
                <select id="type">
                    @foreach ($config['type'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">押金金额</label>
            <div class="layui-input-block">
                <input type="text" id="amount" required lay-verify="number" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <input type="text" id="remark" placeholder="不能超过200字，可以为空" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit>立即提交</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
layui.use(['form', 'laytpl', 'element'], function(){
    var form = layui.form, layer = layui.layer;

    $('#add-new').click(function () {
        layer.open({
            type: 1,
            shade: 0.3,
            title: '发起扣款', //不显示标题
            area: ['450px', '350px'],
            content: $('#add-new-form')
        });
    });

    form.on('submit(add-new-form)', function (data) {
        var load = layer.load({shade: 0.2});

        $.ajax({
            url: "{{ route('finance.deposit.store') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                user_id: $('#user-id').val(),
                type: $('#type').val(),
                amount: $('#amount').val(),
                remark: $('#remark').val()
            },
            error: function (data) {
                layer.close(load);
                errors = data.responseJSON.errors;
                for (key in errors) {
                    layer.alert(errors[key][0], {icon: 5});
                    return false;
                }
            },
            success: function (data) {
                if (data.status == 1) {
                    layer.alert('操作成功', function () {
                        window.location.reload();
                    });
                } else {
                    layer.close(load);
                    layer.alert(data.message, {icon: 5});
                }
            }
        });

        return false;
    });

    // 订单的3种操作合一
    $('.operate').click(function () {
        var url = $(this).data('url');

        layer.confirm('再次确认', function (data) {
            var loading = layer.load(0, {shade: 0.3});

            $.post(url, function (data) {
                layer.close(loading);

                if (data.status == 1) {
                    layer.alert('操作成功', function () {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.message, {icon: 5});
                }
            }, 'json');
        });
    });
});
</script>
@endsection
