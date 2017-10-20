@extends('frontend.layouts.app')

@section('title', '财务 - 我的资产')

@section('submenu')
@include('frontend.workbench.submenu')
@endsection

@section('main')
<table class="layui-table">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>时间</th>
            <th>渠道</th>
            <th>单号</th>
            <th>账号</th>
            <th>游戏</th>
            <th>商品</th>
            <th>数量</th>
            <th>单价</th>
            <th>合计</th>
            <th>备注</th>
            <th>备注</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
@endsection