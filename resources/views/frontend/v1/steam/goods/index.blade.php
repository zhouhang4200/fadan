@extends('frontend.v1.layouts.app')

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

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
    <form class="layui-form" id="search-form" method="get">
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 200px;">
                 <input type="text" class="layui-input" name="name" placeholder="版本名" value="{{Request::input('name')}}">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
            </div>
            <a  href="{{ route('frontend.steam.goods.create') }}" class="layui-btn layui-btn-normal fr" >添加商品</a>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th width="6%">序号</th>
            <th>游戏名</th>
            <th>版本名</th>
            <th>面值</th>
            <th>添加时间</th>
            <th>更新时间</th>
            <th>是否审核</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($goods as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->game_name }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
                <td>
                    {{config('frontend.goods_is_examine')[$item->is_examine]}}
                </td>
                <td>
                    @if($item->is_examine == 0 )
                        <button class="layui-btn layui-btn-mini layui-btn-normal delete" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">删除</button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">您还没有添加商品</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {!! $goods->appends(Request::all())->links() !!}
</div>
</div>
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
                window.location.href =  "{{ route('frontend.steam.goods.create') }}";
                return false;
            });

            form.on('submit(delete-goods)', function (data) {
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('frontend.steam.goods.destroy') }}",
                        data:{id: data.elem.getAttribute('data-id')},
                        success: function (data) {
                            if (data.status.code == 1) {
                                layer.msg('删除成功', {icon: 6, time:1000});
                                setTimeout(function () {
                                    window.location.href = "{{ route('frontend.steam.goods.index') }}";
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