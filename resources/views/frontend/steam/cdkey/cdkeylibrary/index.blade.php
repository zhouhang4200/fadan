@extends('frontend.layouts.app')

@section('title',$cdkey->goodses->game_name.' - '.$cdkey->goodses->name)

@section('css')

@endsection

@section('submenu')
    @include('frontend.steam.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search-form">
        <input type="hidden" name="id" value="{{Request::input('id')}}"/>
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 200px;">
                <input type="text" class="layui-input" name="cdk" placeholder="cdk" value="{{Request::input('cdk')}}">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
                <a href="{{url('steam/cdkeylibrary?export=1&'.http_build_query(Request::all()))}}" class="layui-btn layui-btn-normal">导出</a>
            </div>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th width="6%">序号</th>
            <th>cdk</th>
            <th>添加时间</th>
            <th>到期时间</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($cdkeyLibraries as $item)
            <tr data-id="{{ $item->id }}">
                <td>{{ $item->id }}</td>
                <td>{{ $item->cdk }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->effective_time }}</td>
                <td class="status">
                    {{config('frontend.cdkeyLibraries_status')[$item->status]}}

                </td>
                <td>
                    @if($item->status != 0 && $item->status != 3)
                        @if($item->is_frozen == 0)
                            <button class="layui-btn red layui-btn-small" data-attr="is_frozen" lay-submit=""
                                    lay-filter="is_something">冻结
                            </button>
                        @else
                            <button class="layui-btn green layui-btn-small" data-attr="is_frozen" lay-submit=""
                                    lay-filter="is_something">解冻
                            </button>
                        @endif
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
    {!! $cdkeyLibraries->appends(Request::all())->links() !!}
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'upload'], function () {
            var form = layui.form, $ = layui.jquery, upload = layui.upload, layer = layui.layer;
            //监听提交
            form.on('submit(add)', function (data) {
                layer.confirm('确定添加吗?', {icon: 3, title: '提示'}, function (index) {
                    var loading = layer.open({
                        type: 3,
                        shade: [0.2, '#000']
                    });
                    $.post("{{ route('frontend.steam.cdkey.store') }}", {data: data.field}, function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message);
                        }
                        layer.close(loading);
                    }, 'json');

                });
                return false;
            });
            form.on('submit(is_something)', function (data) {
                var _this = $(this);
                var data = {
                    id: _this.parents("tr").attr('data-id'),
                    attr: _this.attr('data-attr')
                }
                $.ajax({
                    type: "PATCH",
                    url: "{{ route('frontend.steam.cdkeylibrary.isSomething') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 1) {
                            layer.msg(data.message, {icon: 6, time:1000});
                            _this.toggleClass('red green');
                            if(_this.hasClass('red')){
                                _this.html('冻结')
                                _this.parents('tr').find('.status').html('正常')
                            }else {
                                _this.html('解冻')
                                _this.parents('tr').find('.status').html('已冻结')
                            }
                            return false;
                        }
                        layer.msg(data.message, {icon: 6, time:1000});
                    }
                });
            });

        });
    </script>
@endsection