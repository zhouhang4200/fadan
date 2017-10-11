@extends('backend.layouts.app')

@section('title', '商家后台')

@section('content')
<!--START 主体-->
    <div class="main">
        <div class="wrapper">
            <div class="left">
                <div class="column-menu">
                    <ul class="seller_center_left_menu">
                        <li class="current"><a href=""> 子账号列表 </a><div class="arrow"></div></li>
                        <li><a href=""> 子账号列表 </a><div class="arrow"></div></li>
                    </ul>
                </div>
            </div>

            <div class="right">
                <div class="content">

                    <div class="path"><span>子账号列表</span></div>

                    <div class="layui-tab">
                        <ul class="layui-tab-title">
                            <li class="layui-this">子账号列表</li>
                        </ul>
                        <div class="layui-tab-content">
                        <form class="layui-form h5-join-form" method="get" action="">
                            <div class="inc">
                                <input class="form-control laydate-icon input" type="text" placeholder="开始时间" id="starttime" name="startDate" autocomplete="off" value="">
                                <span style="color:#28bd8b"> - </span>
                                <input class="form-control laydate-icon input" type="text" placeholder="结束时间" id="end-time" name="endDate" autocomplete="off" value="">

                                账号名：<input type="text" class="input" name="order_id" placeholder="请输入订单号" value="" id="order_id">
                                <input type="submit" class="bg" value="查询">
                                <a href="{{ route('accounts.index') }}"><input type="button" class="bg" value="返回"></a>
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
                                        <th>用户ID</th>
                                        <th>用户名</th>
                                        <th>邮箱</th>
                                        <th>注册时间</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($accounts as $account)
                                        <tr>
                                            <td>{{ $account->id }}</td>
                                            <td>{{ $account->name }}</td>
                                            <td>{{ $account->email }}</td>
                                            <td>{{ $account->created_at }}</td>
                                            <td>编辑|删除|详情</td>
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

   function logout() {    
        $.post("{{ route('admin.logout') }}", function (data) {
            top.location='/admin/login'; 
        });
    };
        
    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function(){
        var element = layui.element;
    });
</script>
@endsection