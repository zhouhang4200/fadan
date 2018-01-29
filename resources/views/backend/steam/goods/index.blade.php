@extends('backend.layouts.main')

@section('title', '| 商品列表')

@section('css')
    <style>
        .user-td td div {
            text-align: center;
            width: 320px;
        }

        .layui-table tr th {
            text-align: center;
        }

        .redbackend {
            background-color: #dd514c;
        }

        .greenbackend {
            background-color: #5eb95e;
        }

        .yellowbackend {
            background-color: #F37B1D;
        }

    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商品列表</span></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <div class="filter-block pull-left">
                        <form class="layui-form">
                            <div class="row">
                                <div class="form-group col-xs-8">
                                    <input type="text" class="layui-input" name="name" placeholder="版本名"
                                           value="{{Request::input('name')}}">
                                </div>
                                <div class="form-group col-xs-1">
                                    <button type="submit" class="layui-btn layui-btn-normal layui-btn-small pull-left">
                                        搜索
                                    </button>
                                </div>


                            </div>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <a href="{{ route('backend.steam.goods.create') }}"
                           class="layui-btn layui-btn-samll layui-btn-normal">添加商品</a>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th width="6%">商品ID</th>
                                <th>游戏名</th>
                                <th>版本名</th>
                                <th>面值</th>
                                <th>subid</th>
                                <th>游戏URL</th>
                                <th>备注</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th>状态</th>
                                <th width="200">是否通过审核</th>
                                <th width="100">是否显示</th>
                                <th width="130">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($goods as $item)
                                <tr data-id="{{ $item->id }}">
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        <input type="text" class="layui-input edit_something" name="game_name"
                                               placeholder="游戏名" lay-filter="edit_something"
                                               value="{{ $item->game_name }}">
                                    </td>
                                    <td>
                                        <input type="text" class="layui-input edit_something" name="name"
                                               placeholder="版本名" lay-filter="edit_something" value="{{ $item->name }}">
                                    </td>
                                    <td>
                                        <input type="text" class="layui-input edit_something" name="price"
                                               placeholder="面值" lay-filter="edit_something" value="{{ $item->price }}">
                                    </td>
                                    <td>
                                        <input type="text" class="layui-input edit_something" name="subid"
                                               placeholder="subid" lay-filter="edit_something"
                                               value="{{ $item->subid }}">
                                    </td>
                                    <td>
                                        <input type="text" class="layui-input edit_something" name="game_url"
                                               placeholder="游戏url" lay-filter="edit_something"
                                               value="{{ $item->game_url }}">
                                    </td>
                                    <td>
                                        <input type="text" class="layui-input edit_something" name="description"
                                               placeholder="备注" lay-filter="edit_something"
                                               value="{{ $item->description }}">
                                    </td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->updated_at }}</td>
                                    <td>{{config('frontend.goods_is_examine')[$item->is_examine]}}</td>
                                    <td>
                                        @if($item->is_examine == 0)
                                            <button class="layui-btn redbackend layui-btn-small" data-value="2"
                                                    data-attr="is_examine" lay-submit=""
                                                    lay-filter="is_something">不通过
                                            </button>
                                            <button class="layui-btn greenbackend layui-btn-small" data-value="1"
                                                    data-attr="is_examine" lay-submit=""
                                                    lay-filter="is_something">通过
                                            </button>
                                        @elseif($item->is_examine == 2)
                                            <button class="layui-btn greenbackend redbackend layui-btn-small"
                                                    data-attr="is_examine" lay-submit=""
                                                    lay-filter="is_something">通过
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->is_show == 0)
                                            <button class="layui-btn greenbackend layui-btn-small" data-attr="is_show"
                                                    lay-submit=""
                                                    lay-filter="is_something">上架
                                            </button>
                                        @else
                                            <button class="layui-btn redbackend layui-btn-small" data-attr="is_show"
                                                    lay-submit=""
                                                    lay-filter="is_something">下架
                                            </button>
                                        @endif

                                    </td>
                                    <td>
                                        {{--<button class="btn btn-success delete" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">修改</button>--}}
                                        <a href="{{ route('backend.steam.goods.edit', ['id' => $item->id]) }}"
                                           class="btn btn-success">查看</a>


                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">无商品</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $goods->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>

        layui.use(['form', 'layedit', 'laydate'], function () {
            var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit
                , laydate = layui.laydate;

            $('.edit_something').change(function () {
                var _this =$(this);
                var id = $(this).parents('tr').attr('data-id');
                var data = {
                    id: id,
                    attr: $(this).attr('name'),
                    value: $(this).val(),
                }

                $.ajax({
                    type: "post",
                    url: "{{ route('backend.steam.goods.edit-something') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 1) {
                            layer.msg(data.message, {time: 1000});
                            return false;
                        }
                        layer.msg(data.message, {time: 1000});
                        reloadHref();
                    }
                });

            })

            form.on('submit(is_something)', function (data) {
                var _this = $(this);
                var gameName = $(this).parents('tr').find('.edit_something').val();
                var data = {
                    id: _this.parents("tr").attr('data-id'),
                    attr: _this.attr('data-attr'),
                    value: _this.attr('data-value'),
                    gameName: gameName
                }
                if(_this.attr('data-attr') == 'is_examine' && _this.attr('data-value') != 2){
                    if(_this.parents('tr').find('input[name=subid]').val() == 0){
                        layer.msg('subid不能为空', {time: 1000});
                        return false;
                    }
                }

                $.ajax({
                    type: "PATCH",
                    url: "{{ route('backend.steam.goods.isSomething') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 1) {
                            layer.msg(data.message, {time: 1000});
                            _this.toggleClass('redbackend greenbackend');
                            reload();
                            return false;
                        }
                        layer.msg(data.message, {time: 1000});
                    }
                });
            });


        });
    </script>
@endsection