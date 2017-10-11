@extends('frontend.layouts.app')

@section('title', '商家后台')

@section('content')
<!--START 主体-->
    <div class="main">
        <div class="wrapper">
            @include('frontend.layouts.account-left')

            <div class="right">
                <div class="content">

                    <div class="path"><span>登录历史记录</span></div>

                    <div class="layui-tab">

                        <div class="layui-tab-content">
                            <form class="layui-form" method="" action="">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">账号名</label>
                                    <div class="layui-input-inline">
                                    <input type="text" name="name" value="{{ $name ?: '' }}" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                                    </div> 
                                    <div class="layui-inline">
                                      <label class="layui-form-label">开始时间</label>
                                      <div class="layui-input-inline">
                                            <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="年-月-日">
                                      </div>
                                    </div>
                                    <div class="layui-inline">
                                      <label class="layui-form-label">结束时间</label>
                                      <div class="layui-input-inline">
                                            <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="年-月-日">
                                      </div>
                                    </div>
                                </div>
                                <div class="layui-inline" >
                                    <button class="layui-btn" lay-submit="" lay-filter="demo1">查找</button>
                                    <button  class="layui-btn"><a href="{{ route('loginrecord.index') }}" style="color:#fff">返回</a></button>
                                </div>                     
                            </form>
                            <div class="layui-tab-item layui-show" lay-size="sm">
                                <table class="layui-table">
                                    <colgroup>
                                        <col width="150">
                                        <col width="200">
                                        <col>
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>用户ID</th>
                                        <th>用户名</th>
                                        <th>登录IP</th>
                                        <th>登录城市</th>
                                        <th>登录时间</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($loginRecords as $loginRecord)
                                        <tr>
                                            <td>{{ $loginRecord->id }}</td>
                                            <td>{{ $loginRecord->user_id }}</td>
                                            <td>{{ $loginRecord->user->name }}</td>
                                            <td>{{ long2ip($loginRecord->ip) }}</td>
                                            <td>{{ $loginRecord->city ? $loginRecord->city->name : '' }}</td>
                                            <td>{{ $loginRecord->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                        {!! $loginRecords->appends([
                            'name' => $name,
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                        ])->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--END 主体-->
@endsection
<!--START 底部-->
@section('js')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    });

   function logout() {    
        $.post("{{ route('admin.logout') }}", function (data) {
            top.location='/admin/login'; 
        });
    };

     layui.use('laydate', function(){
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
        
    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function(){
        var element = layui.element;
    });
</script>
@endsection