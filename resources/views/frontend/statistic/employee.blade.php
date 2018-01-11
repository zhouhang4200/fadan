@extends('frontend.layouts.app')

@section('title', '统计 - 员工统计')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>

    </style>
@endsection

@section('submenu')
    @include('frontend.statistic.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-input-inline">
            <div class="layui-form-item">
                <label class="layui-form-label" style="padding-left: 0px;width: 53px;">员工姓名</label> 
                    <div class="layui-input-inline">               
                        <select name="user_name" lay-verify="" lay-search="">
                            <option value="">请输入员工姓名</option>
                            @forelse($children as $child)
                                <option value="{{ $child->id }}" {{ $child->id == $userName ? 'selected' : '' }}>{{ $child->user_name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                <label class="layui-form-label">发布时间</label>
                    <div class="layui-input-inline">  
                        <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="start_date" id="test1" placeholder="年-月-日">
                    </div>
                    <div class="layui-input-inline">  
                        <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="end_date" id="test2" placeholder="年-月-日">
                    </div>
                    <div class="layui-inline" >
                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查询</button>
                        <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small" >导出</a>
                    </div>                 
            </div>
        </div>
    </form>

    <div class="layui-tab-item layui-show" lay-size="sm">
        <form class="layui-form" action="">
        <table class="layui-table" lay-size="sm" style="text-align:center;">
            <thead>
            <tr>
                <th>员工姓名</th>
                <th>账号</th>
                <th>已结算单数</th>
                <th>已结算单发单金额</th>
                <th>已撤销单数</th>
                <th>已仲裁单数</th>
                <th>利润</th>
            </tr>
            </thead>
            <tbody>
                @forelse($datas as $data)
                    <tr>
                        <td>{{ $data->user->user_name }}</td>
                        <td>{{ $data->user->name }}</td>
                        <td>{{ $data->complete_order_count ?? '--' }}</td>
                        <td>{{ number_format($data->send_order_amount, 2) ?? '--' }}</td>
                        <td>{{ $data->revoke_order_count ?? '--' }}</td>
                        <td>{{ $data->arbitrate_order_count ?? '--' }}</td>
                        <td>{{ number_format($data->profit, 2) ?? '--' }}</td>
                    </tr>
                @empty
                @endforelse
                    <tr style="color:red">
                        <td>总计</td>
                        <td>{{ $totalData->total_user_id_count ?? '--' }}</td>
                        <td>{{ $totalData->total_complete_order_count ?? '--' }}</td>
                        <td>{{ number_format($totalData->total_send_order_amount, 2) ?? '--' }}</td>
                        <td>{{ $totalData->total_revoke_order_count ?? '--' }}</td>
                        <td>{{ $totalData->total_arbitrate_order_count ?? '--' }}</td>
                        <td>{{ number_format($totalData->total_profit, 2) ?? '--' }}</td>
                    </tr>
            </tbody>
        </table>
        </form>
    </div>
    {!! $datas->appends([
        'user_name' => $userName,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ])->render() !!}

@endsection
<!--START 底部-->
@section('js')
    <script>
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
            // 账号启用禁用
            form.on('switch(open)', function(data){
                var id = data.elem.getAttribute('lay-data');
                console.log(id);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('staff-management.forbidden') }}",
                    data:{id:id},
                    success: function (data) {
                        if (data.status) {
                            layer.msg(data.message, {icon: 6, time:1000});
                            
                        } else {
                            layer.msg('启用失败', {icon: 5, time:1500}); 
                        }
                    }
                });
            });
            //页面显示修改结果
            var succ = "{{ session('succ') }}";
            var fail = "{{ session('fail') }}";
            var empty = "{{ session('empty') }}";

            if (succ) {
                layer.msg(succ, {icon: 6, time:1000});
            }
            if (fail) {
                layer.msg(fail, {icon: 5, time:1000});
            }
            if (empty) {
                layer.msg(empty, {icon: 5, time:1000});
            }
            // 删除
            form.on('submit(delete)', function (data) {
                var id = data.elem.getAttribute('lay-data');
                console.log(id);
                layer.confirm('确认删除吗？', {
                      btn: ['确认', '取消'] 
                      ,title: '提示'
                      ,icon: 3
                    }, function(index, layero){
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('staff-management.delete') }}",
                            data:{id:id},
                            success: function (data) {
                                if (data.status) {
                                    layer.msg(data.message, {icon: 6, time:1000});
                                    
                                } else {
                                    layer.msg(data.message, {icon: 5, time:1500}); 
                                }
                            }
                        });
                        layer.closeAll();
                        window.location.href="{{ route('staff-management.index') }}";
                    }, function(index){
                        layer.closeAll();
                    });
          
                return false;
            });
        });
    </script>
@endsection