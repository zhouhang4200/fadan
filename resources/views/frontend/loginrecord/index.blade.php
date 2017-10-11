@extends('frontend.layouts.app')

@section('title', '商家后台')

@section('content')
<!--START 主体-->
    <div class="main">
        <div class="wrapper">
            <div class="left">
                <div class="column-menu">
                    <ul class="seller_center_left_menu">
                        <li class="current"><a href=""> 登录历史列表 </a><div class="arrow"></div></li>
                        <li><a href=""> 登录历史 </a><div class="arrow"></div></li>
                    </ul>
                </div>
            </div>

            <div class="right">
                <div class="content">

                    <div class="path"><span>登录历史记录</span></div>

                    <div class="layui-tab">
                        <ul class="layui-tab-title">
                            <li class="layui-this">登录历史记录</li>
                        </ul>
                        <div class="layui-tab-content">
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
                            'user_id' => $userId,
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