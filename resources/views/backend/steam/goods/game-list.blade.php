@extends('backend.layouts.main')

@section('title', '| Steam直充游戏名')

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
                    <div class="filter-block pull-right">
                        <a href="javascript:void (0)"
                           class="layui-btn layui-btn-samll layui-btn-normal" lay-submit  lay-filter="recharge-windows">添加游戏</a>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>游戏名称</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse(collect($data) as $item)
                                    <tr>
                                        <td>{{ $item->Tb_id }}</td>
                                        <td>{{ $item->GameName }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">无商品</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="recharge" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">游戏名</label>
                <div class="layui-input-block">
                    <input type="text" name="game_name" autocomplete="off" placeholder="请输入游戏名" class="layui-input" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="recharge">确定</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function () {
            var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit
                , laydate = layui.laydate;


            // 手动加款按钮
            form.on('submit(recharge-windows)', function(data){
                $('input[name=name]').val(data.elem.getAttribute('data-name'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加游戏',
                    content: $('#recharge')
                });
                return false;
            });

            form.on('submit(recharge)', function(data){
                layer.confirm('您确认要添加吗？', {icon: 3, title:'提示'}, function(index){
                    $.post('{{ route('backend.steam.goods.insertGameName') }}', {game_name:data.field.game_name}, function(result){
                        layer.msg(result.message)
                        reloadHref()

                    }, 'json');
                    layer.closeAll();
                });
                return false;
            });

        });
    </script>
@endsection