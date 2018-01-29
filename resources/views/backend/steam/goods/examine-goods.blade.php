@extends('backend.layouts.main')

@section('title', '商品 - 商品列表')

@section('css')
    <style>
        .user-td td div{
            text-align: center;width: 320px;
        }
        .layui-table tr th {
            text-align: center;
        }
        .layui-form-pane .layui-form-label{
            width:175px;
        }
        .layui-form-pane .layui-input-block{
            margin-left: 175px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.goods.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search-form">
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 200px;">
                <select name="game_id" lay-search>
                    <option value="">所有游戏</option>
                    @foreach ($games as $key => $value)
                        <option value="{{ $key }}" {{ $key == Request::input('game_id') ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
            </div>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th width="6%">商品ID</th>
            <th>游戏</th>
            <th>商品名</th>
            <th>面值</th>
            <th>添加时间</th>
            <th>更新时间</th>
            <th>是否审核</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($goods as $item)
            <tr data-id="{{ $item->id }}">
                <td>{{ $item->id }}</td>
                <td>{{ $item->game->name }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
                <td>
                    {{config('frontend.goods_is_examine')[$item->is_examine]}}
                </td>
                <td>
                    <button class="layui-btn layui-btn-mini layui-btn-normal" lay-submit=""
                            lay-filter="show"
                            data-route="{{ route('frontend.cdkey.show', ['id' => $item->id]) }}">生成CDK
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">您还没有添加商品</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $goods->appends(Request::all())->links() }}

    <div class="edit-game-box" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <input type="hidden" name="goods_id">
            <div class="layui-form-item">
                <label class="layui-form-label">生成数量</label>
                <div class="layui-input-block">
                    <input type="text" name="number" lay-verify="required" placeholder="请输入生成数量" autocomplete="off"
                           class="layui-input" value="10">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">CDK有效期（默认一年）</label>
                <div class="layui-input-block">
                    <input type="text" name="effective_time" lay-verify="" placeholder="有效时间" id="test1" autocomplete="off"
                           class="layui-input" value="">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <input type="text" name="remarks" lay-verify="required" placeholder="备注" autocomplete="off"
                           class="layui-input" value="">
                </div>
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="edit">生成CDK</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
                    ,layer = layui.layer
                    ,$ =layui.jquery
                    ,layedit = layui.layedit
                    ,laydate = layui.laydate;
            //时间选择器
            laydate.render({
                elem: '#test1'
                ,type: 'datetime'
            });
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

            form.on('submit(add)', function (data) {
                var id = $(this).parents('tr').attr('data-id');
                layer.confirm('确定添加吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('frontend.goods.goods-bind-user') }}",
                        data:{id: id},
                        success: function (data) {
                            if (data.status.code == 1) {
                                layer.msg(data.status.message, {icon: 6, time:1000});
                            } else {
                                layer.msg(data.status.message, {icon: 5, time:1000});
                            }
                        }
                    });
                    layer.close(index);
                });
            });

            // 查看游戏
            form.on('submit(show)', function (data) {
                var id = $(this).parents('tr').attr('data-id');
                $.get(data.elem.getAttribute('data-route'), {id: id}, function (result) {
                    console.log(result)
                    $('.edit-game-box input[name="goods_id"]').val(result.id);
                    layer.open({
                        type: 1,
                        title: '修改密码',
                        closeBtn: 2,
                        area: ['600px', '350px'],
                        shift: 4,
                        moveType: 2,
                        shadeClose: false,
                        content: $('.edit-game-box')
                    });
                }, 'json');
                return false;
            });

            // 修改游戏
            form.on('submit(edit)', function (data) {
                layer.confirm('确定添加吗?', {icon: 3, title:'提示'}, function(index){
                    var loading=layer.open({
                        type: 3,
                        shade: [0.2, '#000']
                    });
                    $.post("{{ route('frontend.cdkey.store') }}", {data:data.field}, function (result) {
                        if(result.status == 1){
                            layer.alert(result.message);
                        }
                        layer.close(loading);
                    }, 'json');

                });
                return false;
            });
        });
    </script>
@endsection