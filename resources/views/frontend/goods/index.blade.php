@extends('frontend.layouts.app')

@section('title', '商品 - 商品列表')

@section('submenu')
    @include('frontend.goods.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search-form">
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 100px;">
                <select name="trade_type">
                    <option value="">所有类型</option>
                    @foreach ($services as $key => $value)
                        <option value="{{ $key }}" {{ $key == $serviceId ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <input type="text" class="layui-input" name="trade_no" placeholder="外部商品ID">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
            </div>
        </div>
    </form>

    <table class="layui-table">
        <colgroup>
            <col width="150">
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>商品ID</th>
            <th>类型</th>
            <th>游戏</th>
            <th>商品名</th>
            <th>外部商品ID</th>
            <th>添加时间</th>
            <th>更新时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($goods as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->service->name }}</td>
                <td>{{ $item->game->name }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->foreign_goods_id }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
                <td>/</td>
            </tr>
        @empty
            <tr>
                <td colspan="10">您还没有添加商品</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection