@extends('backend.layouts.main')

@section('title', ' | 实名认证')

@section('css')
    <style>
       /* .layui-inline {
            width:220px !important;
        }*/

    </style>
@endsection

@section('content')
   <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">实名认证</li>
                        </ul>
                        <div class="layui-tab-content">
                        <form class="layui-form" method="" action="">
                            <div class="layui-block" >
                                <div class="layui-form-item" style="float: left">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">选择状态</label>
                                        <div class="layui-input-block">
                                            <select name="name" lay-verify="" lay-search="">
                                                <option value="">输入名字或直接选择</option>
                                                @foreach($idents as $ident)
                                                <option value="{{ $ident->user_id }}" {{ $name && $name == $ident->user_id ? 'selected' : '' }}>{{ $ident->user->name }}</option>
                                                @endforeach
                                            </select>                                                                              
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">选择状态</label>
                                        <div class="layui-input-block">
                                            <select name="status" lay-filter="aihao">
                                                <option value=""></option>
                                                <option value="0" {{ is_numeric($status) && '0' == $status ? 'selected' : '' }}>待审核</option>
                                                <option value="1" {{ $status && '1' == $status ? 'selected' : '' }}>审核通过</option>
                                                <option value="2" {{ $status && '2' == $status ? 'selected' : '' }}>审核不通过</option>
                                            </select>
                                        </div>
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
                                <div style="float: left;margin:0 0 15px 100px;">
                                <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                                <button  class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('admin-idents.index') }}" style="color:#fff">返回</a></button></div>
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
                                    <th>序号id</th>
                                    <th>用户名</th>
                                    <th>邮箱</th>
                                    <th>状态</th>
                                    <th>申请认证时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($idents as $ident)
                                    <tr class="ident-td">
                                        <td>{{ $ident->id }}</td>
                                        <td>{{ $ident->user->name }}</td>
                                        <td>{{ $ident->user->email }}</td>
                                        <td>
                                        @if ($ident->status == 0)
                                            待审核
                                        @elseif ($ident->status == 1)
                                            审核通过
                                        @else
                                            审核不通过
                                        @endif
                                        </td>
                                        <td>{{ $ident->created_at }}</td>
                                        <td>
                                            <div style="text-align: center">
                                            <button class="layui-btn layui-btn-normal layui-btn-small edit"><a href="{{ route('admin-idents.show', ['id' => $ident->id]) }}" style="color: #fff">详情</a></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                        {!! $idents->appends([
                            'status' => $status,
                            'name' => $name,
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                        ])->render() !!}
                </div>
            </div>
        </div>
    </div>



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

        layui.use('form', function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;

        var succ = "{{ session('succ') ?: '' }}";

        if(succ) {
            layer.msg(succ, {icon: 6, time:1500},);
        }
  
          //……
          
          //但是，如果你的HTML是动态生成的，自动渲染就会失效
          //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
            form.render();
        });  

    </script>
@endsection