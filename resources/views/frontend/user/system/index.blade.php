@extends('frontend.layouts.app')

@section('title', '账号 - 系统日志')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-inline" style="float:left">
        <div class="layui-form-item">
              <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="开始时间">
              </div>
      
              <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="结束时间">
              </div>
            </div>
        </div>
        <div style="float: left">
            <div class="layui-inline" >
                <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                <a href="{{ route('home-system-logs.index') }}" style="color:#fff" class="layui-btn layui-btn-normal layui-btn-small">返回</a>
            </div>
        </div>                     
    </form>

    <div class="layui-tab-item layui-show" lay-size="sm">

        <table class="layui-table" lay-size="sm" style="text-align:center;">
            <thead>
            <tr>
                <th>序号</th>
                <th>创建时间</th>
                <th>详情</th>
                <th>字段</th>
                <th>变更前</th>
                <th>变更后</th>
            </tr>
            </thead>
            <tbody>
                 @foreach($systemLogs as $systemLog)
                    <tr>
                        <td>{{ $systemLog->id }}</td>
                        <td>{{ $systemLog->created_at }}</td>
                        <td>账号：{{  \App\Models\User::find($systemLog->user_id)->name }}</td>
                        <td>{{ $systemLog->key }}</td>
                        <td>{{ $systemLog->old_value }}</td>
                        <td>{{ $systemLog->new_value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {!! $systemLogs->appends([
        'startDate' => $startDate,
        'endDate' => $endDate,
    ])->render() !!}

@endsection
<!--START 底部-->
@section('js')
    <script>
        // 时间插件
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            //常规用法
            laydate.render({
            elem: '#test1'
            });

            //常规用法
            laydate.render({
            elem: '#test2'
            });
        });

        // 删除
        function del(id)
        {
             layui.use(['form', 'layedit', 'laydate',], function(){
                var form = layui.form
                ,layer = layui.layer;
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'DELETE',
                        url: '/users/'+id,
                        success: function (data) {
                            if (data.code == 1) {
                                layer.msg(data.message, {icon: 6, time:1000});                                window.location.href = "{{ route('users.index') }}";
                            } else {
                                layer.msg('删除失败', {icon: 5, time:1500});                            }
                        }
                    });
                    layer.close(index);
                });

            });
        };

        layui.use('form', function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;

        var succ = "{{ session('succ') ?: '' }}";

        if(succ) {
            layer.msg(succ, {icon: 6, time:1500});        }
  
          //……
          
          //但是，如果你的HTML是动态生成的，自动渲染就会失效
          //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
            form.render();
        });  

    </script>
@endsection