@extends('backend.layouts.main')

@section('title', ' | 前台账号角色详情')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">前台账号角色详情</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>账号ID</th>
                                    <th>账号名称</th>
                                    <th>账号邮箱</th>
                                    <th>账号角色</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                        @if($user->roles)
                                            @foreach($user->roles as $role)
                                                {{ $role->alias }}
                                            @endforeach
                                        @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
       
    </script>
@endsection