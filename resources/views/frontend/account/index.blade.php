@extends('frontend.layouts.app')

@section('title', '商家后台')

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
                            <div class="layui-form-item">
                                <label class="layui-form-label">账号名</label>
                                <div class="layui-input-inline">
                                <input type="text" name="name" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                          
                            <div class="layui-form">
                              <div class="layui-form-item">
                                <div class="layui-inline">
                                  <label class="layui-form-label">开始时间</label>
                                  <div class="layui-input-inline">
                                        <input type="text" class="layui-input" id="test1" placeholder="年-月-日">
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="layui-form">
                              <div class="layui-form-item">
                                <div class="layui-inline">
                                  <label class="layui-form-label">结束时间</label>
                                  <div class="layui-input-inline">
                                        <input type="text" class="layui-input" id="test2" placeholder="年-月-日">
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button class="layui-btn" lay-submit="" lay-filter="demo1">查找</button>
                                    <button type="reset" class="layui-btn layui-btn-primary">返回</button>
                                </div>
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
                                            <td><fieldset class="layui-elem-field site-demo-button">
                                                <div class="layui-btn-group">
                                                <button class="layui-btn">编辑</button>
                                                <button class="layui-btn ">删除</button>
                                                <button class="layui-btn">详情</button>
                                                </div>
                                            </fieldset></td>
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