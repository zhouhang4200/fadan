@extends('backend.layouts.main')

@section('title', ' | 手动调整')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>用户权重</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <div class="filter-block pull-left">
                        <form class="form-inline" role="form">
                            <div class="form-group">
                                <input type="text" class="form-control" name="user_id"  placeholder="用户ID" value="{{ $userId }}">
                            </div>
                            <button type="submit" class="btn btn-success">搜索</button>
                        </form>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>用户ID</th>
                                <th>别名</th>
                                <th>初始值</th>
                                <th>小于等于6元订单</th>
                                <th>订单成功率</th>
                                <th>订单用时</th>
                                <th>手动调整</th>
                                <th>最终权重</th>
                                <th>手动值开始时间</th>
                                <th>手动值结束时间</th>
                                <th>修改管理员</th>
                                <th>更新时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($userWeight as $item)
                                <tr>
                                    <td>{{ $item->user_id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->less_than_six_percent }}</td>
                                    <td>{{ $item->success_percent }}</td>
                                    <td>{{ $item->use_time_percent }}</td>
                                    <td>{{ $item->manual_percent }}</td>
                                    <td><?php

                                        $percent = $item->less_than_six_percent + $item->success_percent + $item->use_time_percent;

                                        // 判断手动加的百分比是否在有效期
                                        $currentDate = strtotime(date('Y-m-d'));
                                        if ($currentDate >= strtotime($item->start_date)  && $currentDate <= strtotime($item->end_date)) {
                                            $percent +=  $item->manual_percent;
                                        }
                                        // 得到当前用户最终的权重值，四舍五入
                                        echo  round($item->weight + bcmul($item->weight, bcdiv($percent, 100)));

                                        ?></td>
                                    <td>{{ $item->start_date }}</td>
                                    <td>{{ $item->end_date }}</td>
                                    <td>{{ $item->updatedAdmin->name ?? '无' }}</td>
                                    <td>{{ $item->updated_at }}</td>
                                    <td>
                                        <button class="layui-btn layui-btn-mini layui-btn-normal" lay-submit="" lay-filter="show" data-route="{{ route('frontend.user.weight.show', ['id' => $item->id]) }}">修改</button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        {{ $userWeight->appends(['user_id' => $userId])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="edit-weight-box" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <input type="hidden" name="id">
            <div class="layui-form-item">
                <label class="layui-form-label">调整百分比</label>
                <div class="layui-input-inline">
                    <input type="text" name="manual_percent" lay-verify="required" placeholder="请输入游戏名称" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">开始时间</label>
                <div class="layui-input-inline">
                    <input type="text" name="start_date"  id="startDate" lay-verify="required"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">结束时间</label>
                <div class="layui-input-inline">
                    <input type="text" name="end_date" id="endDate" lay-verify="required"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="edit">保存修改</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'laydate'], function(){
            var form = layui.form, layer = layui.layer, laydate = layui.laydate;

            //日期
            laydate.render({
                elem: '#startDate'
            });
            laydate.render({
                elem: '#endDate'
            });

            // 查看
            form.on('submit(show)', function(data){
                $.get(data.elem.getAttribute('data-route'), {id:data.elem.getAttribute('data-id')}, function (result) {
                    $('.edit-weight-box input[name="id"]').val(result.id);
                    $('.edit-weight-box input[name="manual_percent"]').val(result.manual_percent);
                    $('.edit-weight-box input[name="start_date"]').val(result.start_date);
                    $('.edit-weight-box input[name="end_date"]').val(result.end_date);
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '调整权重',
                        content: $('.edit-weight-box')
                    });
                }, 'json');
                return false;
            });
            //修改
            form.on('submit(edit)', function(data){
                $.post('{{ route('frontend.user.weight.edit') }}', {id:data.field.id, data:data.field}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
        });
    </script>
@endsection