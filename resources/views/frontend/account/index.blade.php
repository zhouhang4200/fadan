@extends('frontend.layouts.app')

@section('title', '商家后台')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('content')
<!--START 主体-->
    <div class="main">
        <div class="wrapper">
            @include('frontend.layouts.account-left')

            <div class="right">
                <div class="content">

                    <div class="path"><span>子账号列表</span></div>

                    <div class="layui-tab">
                        <div class="layui-tab-content">
                        <form class="layui-form" method="" action="">
                            <div class="layui-inline" >
                                <div class="layui-form-item" style="float: left">
                                    <label class="layui-form-label">账号名</label>
                                    <div class="layui-input-inline">
                                    <input type="text" name="name" value="{{ $name ?: '' }}" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                                    </div> 

                                      <label class="layui-form-label">开始时间</label>
                                      <div class="layui-input-inline">
                                            <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="年-月-日">
                                      </div>

                                      <label class="layui-form-label">结束时间</label>
                                      <div class="layui-input-inline">
                                            <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="年-月-日">
                                      </div>
                                </div>
                                <div style="float: left">
                                <button class="layui-btn" lay-submit="" lay-filter="demo1">查找</button>
                                <button  class="layui-btn"><a href="{{ route('accounts.index') }}" style="color:#fff">返回</a></button></div>
                            </div>                     
                        </form>
                            <div class="layui-tab-item layui-show" lay-size="sm">
                                <table class="layui-table" lay-size="sm">
                                    <colgroup>
                                        <col width="150">
                                        <col width="200">
                                        <col>
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>用户ID</th>
                                        <th>用户名</th>
                                        <th>邮箱</th>
                                        <th>注册时间</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($accounts as $account)
                                        <tr class="account-td">
                                            <td>{{ $account->id }}</td>
                                            <td>{{ $account->name }}</td>
                                            <td>{{ $account->email }}</td>
                                            <td>{{ $account->created_at }}</td>
                                            <td>
                                                <div class="layui-btn-group">
                                                <button class="layui-btn edit"><a href="{{ route('accounts.edit', ['id' => $account->id]) }}" style="color: #fff">编辑</a></button>
                                                <button class="layui-btn delete" onclick="del({{ $account->id }})">删除</button>
<!--                                                 <button class="layui-btn show"><a href="{{ route('accounts.show', ['id' => $account->id]) }}" style="color: #fff">详情</a></button> -->
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>                               
                            </div>
                        </div>
                        {!! $accounts->appends([
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

    // 时间插件
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

    // 登出
   function logout() {    
        $.post("{{ route('admin.logout') }}", function (data) {
            top.location='/admin/login'; 
        });
    };
    
    // 删除
    function del(id)
    {
        $.ajax({
            type: 'DELETE',
            url: '/accounts/'+id,
            success: function (data) {
                console.log(data);
                var obj = eval('(' + data + ')');
                if (obj.code == 1) {
                    window.location.href = '/accounts';                    
                } else {
                    alert('删除失败');
                }
            }
        })
    };

</script>
@endsection