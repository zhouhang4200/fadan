@extends('frontend.layouts.app')

@section('title', '财务 - 资金流水')

@section('submenu')
@include('frontend.asset.submenu')
@endsection

@section('main')
<table class="layui-table">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>相关单号</th>
            <th>类型</th>
            <th>金额</th>
            <th>说明</th>
            <th>时间</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataList as $data)
            <tr>
                <td>{{ $data->trade_no }}</td>
                <td>{{ config('tradetype.user')[$data->trade_type] }}</td>
                <td>{{ $data->fee + 0 }}</td>
                <td>{{ $data->remark }}</td>
                <td>{{ $data->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection