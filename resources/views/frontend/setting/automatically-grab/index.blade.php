@extends('frontend.layouts.app')

@section('title', '设置 - 抓单商品配置')

@section('css')

@endsection

@section('submenu')
    @include('frontend.setting.submenu')
@endsection

@section('main')
    <div class="explanation">
        <div class="ex_tit" style="margin-bottom: 10px;"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示" class=""></span></div>
        <ul>
            <li>用途：获取您授权后的淘宝店铺订单。</li>
        </ul>
    </div>

    <form class="layui-form" id="goods-form">
        <div class="layui-form-item">
            <div class="layui-input-inline">
                <select name="goods_id"   lay-search="">
                    <option value="">所有类型</option>

                </select>
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <input type="text" class="layui-input" name="other_user_id" placeholder="淘宝商品ID" value="{{ $foreignGoodsId  }}">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="category-search">查询</button>
            </div>
            <button class="layui-btn layui-btn-normal fr" lay-submit lay-filter="goods-add">添加商品</button>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>服务类型</th>
            <th>淘宝商品ID</th>
            <th>备注</th>
            <th>状态</th>
            <th>添加时间</th>
            <th>更新时间</th>
            <th width="7%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($AutomaticallyGrabGoods as $item)
            <tr>
                <td>{{ $item->service_id }}</td>
                <td>{{ $item->foreign_goods_id }}</td>
                <td>{{ $item->remark }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
                <td>
                    <button class="layui-btn layui-btn-normal layui-btn-small" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">删除</button>
                </td>
            </tr>
        @empty

        @endforelse
        </tbody>
    </table>

    {{ $AutomaticallyGrabGoods->links() }}
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;
            {{--// 按用户搜索--}}
            {{--form.on('submit(user-search)', function (data) {--}}
                {{--var par = '?type=' + type + '&other_user_id=' + data.field.other_user_id;--}}
                {{--loadUserList('{{ route('frontend.setting.receiving-control.get-control-user') }}' + par);--}}
                {{--return false;--}}
            {{--});--}}
          {{----}}
            {{--// 点击页码翻页--}}
            {{--$(document).on('click', '.pagination a', function (e) {--}}
                {{--e.preventDefault();--}}
                {{--if (type == 1 && white == 1) {--}}
                    {{--loadUserList($(this).attr('href'))--}}
                {{--} else {--}}
                    {{--loadCategoryList($(this).attr('href'))--}}
                {{--}--}}
                {{--return false;--}}
            {{--});--}}
        });
    </script>
@endsection