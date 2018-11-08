@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单')

@section('content')
    <div class="main-box">
        <div class="main-box-body clearfix">
            <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                <ul class="layui-tab-title">
                    <li class="layui-this" lay-id="add">标品下单</li>
                </ul>

                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <form class="layui-form">
                            <input type="hidden" name="category_id" value="">
                            <div class="row">
                                <div class="col-md-2">
                                    <select name="game_id" lay-search="">
                                        <option value="">请选择</option>
                                        @forelse($games as $game)
                                            <option value="{{ $game->id }}"
                                                    @if($game->id) selected @endif>{{ $game->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search">搜索</button>
                                    <a href="{{ route('game-leveling.channel.game.create') }}" class="layui-btn layui-btn-normal" type="button" id="create" >新增</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="layui-tab-content">
                    <table id="data-table" class="table table-hover dataTable no-footer  layui-form" role="grid"
                           aria-describedby="data-table_info">
                        <thead>
                        <tr role="row">
                        <tr>
                            <th>游戏</th>
                            <th>代练类型</th>
                            <th>发单价格固定比例</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr role="row" class="odd even">
                                <td>{{ $item->game_name }}</td>
                                <td>{{ $item->game_leveling_type_name }}</td>
                                <td>{{ $item->rebate }} </td>
                                <td>
                                    <a href="{{ route('game-leveling.channel.game.edit', ['id' => $item->id]) }}" class="btn btn-success">编辑</a>
                                    <a href="{{ route('game-leveling.channel.price.index', ['id' => $item->id]) }}" class="btn btn-success">价格公式</a>
                                    <a href="{{ route('game-leveling.channel.discount.index', ['id' => $item->id]) }}" class="btn btn-success">折扣</a>
                                    <button type="button" class="btn btn-danger" data-url="{{ route('game-leveling.channel.game.delete', ['id' => $item->id]) }}" lay-submit lay-filter="delete-item">删除</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $items->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('#export').click(function () {
            var url = "?export=1&" + $('#search-flow').serialize();
            window.location.href = url;
        });
        layui.use(['layer', 'form'], function () {});
    </script>
@endsection