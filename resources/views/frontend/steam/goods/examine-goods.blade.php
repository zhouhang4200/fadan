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
        .layui-form-pane .layui-form-label{
            width:190px;
        }
        .layui-form-pane .layui-input-block{
            margin-left: 190px;
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
                <input type="text" class="layui-input" name="name" placeholder="版本名" value="{{Request::input('name')}}">
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
            <th>游戏</th>
            <th>商品名</th>
            <th>面值</th>
            <th>添加时间</th>
            <th>更新时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($goods as $item)
            <tr data-id="{{ $item->id }}">
                <td>{{ $item->id }}</td>
                <td>{{ $item->game_name }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
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
                <label class="layui-form-label">CDK有效期（不填默认一年）</label>
                <div class="layui-input-block">
                    <input type="text" name="effective_time" lay-verify="" placeholder="有效时间" id="test1" autocomplete="off"
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

            // 查看游戏
            form.on('submit(show)', function (data) {
                var id = $(this).parents('tr').attr('data-id');
                $.get(data.elem.getAttribute('data-route'), {id: id}, function (result) {
                    console.log(result)
                    $('.edit-game-box input[name="goods_id"]').val(result.id);
                    layer.open({
                        type: 1,
                        title: '生成CDK',
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

            // 生成CDK
            form.on('submit(edit)', function (data) {
                layer.confirm('确定添加吗?', {icon: 3, title:'提示'}, function(index){
                    var loading=layer.open({
                        type: 3,
                        shade: [0.2, '#000']
                    });
                    layer.close(index);
                    $.post("{{ route('frontend.cdkey.store') }}", {data:data.field}, function (result) {
                        var id ='/cdkeylibrary?id='+result.content.cdkey_id;
                        if(result.status == 1){
                            layer.open({
                                type: 1
                                ,title: '提示' //不显示标题栏
                                ,closeBtn: false
                                ,area: '300px;'
                                ,shade: 0.8
                                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                                ,btn: ['查看CDK', '继续添加']
                                ,btnAlign: 'c'
                                ,moveType: 1 //拖拽模式，0或者1
                                ,content: '<div style="padding: 25px; line-height: 22px; font-size: 16px;">'+result.message+'</div>'
                                ,success: function(layero){
                                    var btn = layero.find('.layui-layer-btn');
                                    btn.find('.layui-layer-btn0').attr({
                                        href: id
                                    });
                                    btn.find('.layui-layer-btn1').click(function () {
                                        layer.closeAll();
                                    })
                                }
                            });
                        }
                        layer.close(loading);
                    }, 'json');

                });
                return false;
            });
        });
    </script>
@endsection