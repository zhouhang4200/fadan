@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单-价格公式')

@section('content')
    <div class="main-box">
        <div class="main-box-body clearfix">
            <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                <ul class="layui-tab-title">
                    <li class="layui-this" lay-id="add">价格公式</li>
                </ul>

                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <form class="layui-form">
                            <input type="hidden" name="category_id" value="">
                            <div class="row">
                                <div class="col-md-2">

                                </div>
                                <div class="col-md-2">
                                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search">搜索</button>
                                    <a href="{{ route('game-leveling.channel.price.create', ['id' => request('id')]) }}" class="layui-btn layui-btn-normal" type="button" id="create" >新增</a>
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
                            <th>序号</th>
                            <th>到下一层级价格</th>
                            <th>到下一层级耗时</th>
                            <th>该层级安全保证金</th>
                            <th>该层级效率保证金</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr role="row" class="odd even">
                                <td>{{ $item->sort }}</td>
                                <td>{{ $item->level }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->hour }}</td>
                                <td>{{ $item->security_deposit }}</td>
                                <td>{{ $item->efficiency_deposit }} </td>
                                <td>
                                    <a href="{{ route('game-leveling.channel.price.edit', ['id' => $item->id]) }}" class="btn btn-success">编辑</a>
                                    <button type="button" class="btn btn-danger" data-url="{{ route('game-leveling.channel.price.delete', ['id' => $item->id]) }}" lay-submit lay-filter="delete-item">删除</button>
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
        layui.use(['layer', 'form'], function () {});
    </script>
@endsection