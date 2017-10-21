@extends('backend.layouts.main')

@section('content')
    <table class="layui-table" lay-size="sm">
        <colgroup>
            <col width="150">
            <col width="200">
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>ID</th>
            <th>版本</th>
            <th>单价</th>
            <th>34</th>
        </tr>
        </thead>
        <tbody>

        @foreach($orders as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->goodsTemplateValue->pluck('field_value', 'field_name')['version'] }}</td>
                <td>{{ $item->goodsTemplateValue->pluck('field_value', 'field_name')['账号'] }}</td>
                <td>{{ $item->price }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>


@endsection