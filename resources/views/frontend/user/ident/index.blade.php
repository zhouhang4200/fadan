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
    <div class="layui-tab-item layui-show">
        <table class="layui-table"  lay-size="sm">
            @if (! empty($ident) && $ident->type == 1)
            <thead>
            <tr>
                <th>用户名</th>
                <th>电话</th>
                <th>身份证号</th>
                <th>申请认证时间</th>
                <th>审核状态</th>
                @if($ident->status == 2)
                    <th>原因</th>
                @endif
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $ident->user->name }}</td>
                    <td>{{ $ident->phone_number }}</td>
                    <td>{{ $ident->identity_card }}</td>
                    <td>{{ $ident->created_at }}</td>
                    <td>{{ config('frontend.status')[$ident->status] }}</td>
                    @if($ident->status == 2)
                        <th>{{ $ident->message }}</th>
                    @endif
                    @if ($ident->status == 1)
                        <td>完成</td>
                    @else
                        <td style="text-align: center"><a href="{{ route('idents.edit', ['id' => $ident->id]) }}" class="layui-btn layui-btn-normal layui-btn-small">编缉</a></td>
                    @endif
                </tr>
            </tbody>
            @elseif (! empty($ident) && $ident->type == 2)
            <thead>
            <tr>
                <th>用户名</th>
                <th>法人</th>
                <th>营业执照号</th>
                <th>申请认证时间</th>
                <th>审核状态</th>
                @if($ident->status == 2)
                    <th>原因</th>
                @endif
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $ident->user->name }}</td>
                    <td>{{ $ident->corporation }}</td>
                    <td>{{ $ident->license_number }}</td>
                    <td>{{ $ident->created_at }}</td>
                    <td>{{ config('frontend.status')[$ident->status] }}</td>
                    @if($ident->status == 2)
                        <th>{{ $ident->message }}</th>
                    @endif
                    @if ($ident->status == 1)
                        <td>完成</td>
                    @else
                        <td style="text-align: center"><a href="{{ route('idents.edit', ['id' => $ident->id]) }}" class="layui-btn layui-btn-normal layui-btn-small">编缉</a></td>
                    @endif
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