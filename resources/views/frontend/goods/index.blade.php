@extends('frontend.layouts.app')

@section('title', '商品 - 商品列表')

@section('submenu')
    @include('frontend.goods.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search-form">
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 100px;">
                <select name="service_id">
                    <option value="">所有类型</option>
                    @foreach ($services as $key => $value)
                        <option value="{{ $key }}" {{ $key == $serviceId ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-input-inline" style="width: 100px;">
                <select name="game_id">
                    <option value="">所有游戏</option>
                    @foreach ($games as $key => $value)
                        <option value="{{ $key }}" {{ $key == $gameId ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <input type="text" class="layui-input" name="foreign_goods_id" placeholder="外部商品ID" value="{{ $foreignGoodsId  }}">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
            </div>
            <button class="layui-btn layui-btn-normal fr" lay-submit="" lay-filter="add-goods">添加商品</button>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
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
            <th>单价</th>
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
                <td>{{ $item->price }}</td>
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

    {{ $goods->appends([
    'service_id' => $serviceId,
    'game_id' => $gameId,
    'foreign_goods_id' => $foreignGoodsId,
    ])->links() }}
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
                    ,layer = layui.layer
                    ,layedit = layui.layedit
                    ,laydate = layui.laydate;

            //监听指定开关
            form.on('switch(switchTest)', function(data){
                layer.msg('开关checked：'+ (this.checked ? 'true' : 'false'), {
                    offset: '6px'
                });
            });

            //监听提交
            form.on('submit(add-goods)', function(data){
                window.location.href =  "{{ route('frontend.goods.create') }}";
                return false;
            });
        });
    </script>
@endsection