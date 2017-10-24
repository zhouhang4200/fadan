@extends('frontend.layouts.app')

@section('title', '财务 - 我的资产')

@section('submenu')
@include('frontend.finance.submenu')
@endsection

@section('main')
<table class="layui-table" lay-size="sm">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>名称</th>
            <th>金额</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>账户余额</td>
            <td>{{ $asset->balance + 0 }}</td>
        </tr>
        <tr>
            <td>冻结金额</td>
            <td>{{ $asset->frozen + 0 }}</td>
        </tr>
        <tr>
            <td>累计加款</td>
            <td>{{ $asset->total_recharge + 0 }}</td>
        </tr>
        <tr>
            <td>累计提现</td>
            <td>{{ $asset->total_withdraw + 0 }}</td>
        </tr>
        <tr>
            <td>累计收入</td>
            <td>{{ $asset->total_refund + $asset->total_income }}</td>
        </tr>
        <tr>
            <td>累计支出</td>
            <td>{{ $asset->total_consume + $asset->total_expend }}</td>
        </tr>
    </tbody>
</table>
@endsection