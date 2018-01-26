@extends('frontend.layouts.app')

@section('title', 'CDK列表')

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
    @include('frontend.steam.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search-form" method="get" action="{{url('steam/cdkeylibrary/search')}}">
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 200px;">
                <input type="text" class="layui-input" name="cdk" placeholder="CDK" value="{{Request::input('cdk')}}">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
            </div>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th width="6%">序号</th>
            <th>游戏名</th>
            <th>版本名</th>
            <th>数量(张)</th>
            <th>备注</th>
            <th width="100">添加时间</th>
            <th width="100">到期时间</th>
            <th >操作</th>
        </tr>
        </thead>
        <tbody>

            @forelse($cdkies as $item)
                @if(!empty($item->goodses))
                    <tr data-id="{{$item->id}}">
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->goodses->game_name ?? '' }}</td>
                        <td>{{ $item->goodses->name ?? '' }}</td>
                        <td>{{ $item->number }}</td>
                        <td>
                            <input type="text" class="layui-input remarks" name="name" placeholder="备注" lay-filter="remarks"  value="{{$item->remarks}}">
                        </td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->end_time }}</td>
                        <td>
                            <a href="{{ route('frontend.steam.cdkeylibrary.index', ['id' => $item->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini edit">查看</a>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="10">您还没有添加商品</td>
                </tr>
            @endforelse

        </tbody>
    </table>

    {{ $cdkies->appends(Request::all())->links() }}
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

            $('.remarks').change(function () {
                var id =$(this).parents('tr').attr('data-id');
                var data={
                    id:id,
                    remarks: $(this).val()
                }
                $.ajax({
                    type: "post",
                    url: "{{ route('frontend.steam.cdkey.remarks') }}",
                    data: data,
                    success: function (data) {
                        if (data.status.code == 1) {
                            layer.msg(data.status.message, {icon: 6, time:1000});
                            return false;
                        }
                        layer.msg(data.status.message, {icon: 6, time:1000});
                    }
                });
            })

            form.on('submit(show-cdk)', function (data) {
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

            });
        });
    </script>
@endsection