@extends('frontend.layouts.app')

@section('title', '首页')

@section('submenu')
<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.index' ? 'current' : '' }}"><a href="{{ route('frontend.index') }}">首页</a><div class="arrow"></div></li>
</ul>
@endsection

@section('content')
<div class="layui-tab">
    <ul class="layui-tab-title">
        <li class="layui-this">网站设置</li>
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
                    <th>昵称</th>
                    <th>加入时间</th>
                    <th>签名</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>贤心</td>
                    <td>2016-11-29</td>
                    <td>人生就像是一场修行</td>
                </tr>
                <tr>
                    <td>许闲心</td>
                    <td>2016-11-28</td>
                    <td>于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
