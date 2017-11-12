@extends('frontend.layouts.app')

@section('title', '商品 - 商品列表')

@section('css')
    <style>
        .user-td td div{
            text-align: center;width: 320px;
        }
        .layui-table tr th {
            text-align: center;
        }
    </style>
@endsection

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
            <a  href="{{ route('frontend.goods.create') }}" class="layui-btn layui-btn-normal fr" >添加商品</a>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th width="6%">商品ID</th>
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
                <td><a class="layui-btn layui-btn-normal layui-btn-small edit"  href="{{ route('frontend.goods.edit', ['id' => $item->id]) }}">编辑</a>
                <button class="layui-btn layui-btn-normal layui-btn-small delete" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">删除</button></td>
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

            form.on('submit(delete-goods)', function (data) {
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('frontend.goods.destroy') }}",
                        data:{id: data.elem.getAttribute('data-id')},
                        success: function (data) {
                            if (data.status.code == 1) {
                                layer.msg('删除成功', {icon: 6, time:1000});
                                setTimeout(function () {
                                    window.location.href = "{{ route('frontend.goods.index') }}";
                                }, 1000);
                            } else {
                                layer.msg('删除失败', {icon: 5, time:1000});
                            }
                        }
                    });
                    layer.close(index);
                });
            });
        });
    </script>
@endsection