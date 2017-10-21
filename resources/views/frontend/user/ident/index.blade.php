@extends('frontend.layouts.app')

@section('title', '账号 - 实名认证')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-form-item .layui-input-inline {
            float: left;
            width: 120px;
            margin-right: 10px;
        }
        .layui-form-label {
            width:60px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <div class="layui-tab-item layui-show" lay-size="sm">
        <table class="layui-table">
            <colgroup>
                <col width="150">
                <col width="200">
                <col>
            </colgroup>
            @if ($ident->type == 1)
            <thead>
            <tr>
                <th>用户名</th>
                <th>电话</th>
                <th>身份证号</th>
                <th>申请认证时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $ident->user->name }}</td>
                    <td>{{ $ident->phone_number }}</td>
                    <td>{{ $ident->identity_card }}</td>
                    <td>{{ $ident->created_at }}</td>
                    <td style="text-align: center"><button class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('idents.edit', ['id' => $ident->id]) }}" style="color: #fff">编缉</a></td>
                </tr>
            </tbody>
            @else 
            <thead>
            <tr>
                <th>用户名</th>
                <th>法人</th>
                <th>营业执照号</th>
                <th>申请认证时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $ident->user->name }}</td>
                    <td>{{ $ident->corporation }}</td>
                    <td>{{ $ident->license_number }}</td>
                    <td>{{ $ident->created_at }}</td>
                    <td style="text-align: center"><button class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('idents.edit', ['id' => $ident->id]) }}" style="color: #fff">编缉</a></td>
                </tr>
            </tbody>
            @endif
        </table>
    </div>
@endsection

@section('js')
    <script>
        layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            var succ = "{{ session('succ') ?: '' }}";
            var updateError = "{{ session('updateError') ?: '' }}";

            if(succ) {
                layer.msg(succ, {icon: 6, time:1500},);
            } else if(updateError) {
                layer.msg(updateError, {icon: 5, time:1500},);
            }
            form.render();
        });
    </script>
@endsection