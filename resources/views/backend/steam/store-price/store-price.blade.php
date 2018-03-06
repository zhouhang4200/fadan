@extends('backend.layouts.main')

@section('title', '| 添加商户密价')

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
                <li class="active"><span>商户密价列表</span></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <div class="filter-block pull-right">
                        <a href="javascript:void (0)"
                           class="layui-btn layui-btn-samll layui-btn-normal" lay-submit  lay-filter="recharge-windows">添加商户密价</a>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>商户ID</th>
                                <th>密价</th>
                                <th>修改人</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($steamStorePrices as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->user_id }}</td>
                                        <td>
                                            <input type="text" class="layui-input edit_something" name="clone_price"
                                                   placeholder="商户密价" lay-filter="edit_something"
                                                   value="{{ $item->clone_price }}">
                                        </td>
                                        <td>{{ $item->username }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">无密价</td>
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
            <label class="layui-form-label">商户号</label>
            <div class="layui-input-block">
                <select name="user_id" lay-search="" lay-verify="required">
                    <option value="">请选择商户号</option>
                    @forelse($userNames as $key => $value)
                        <option value="{{$key}}">{{$key}}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密价</label>
                <div class="layui-input-block">
                    <input type="text" name="clone_price" autocomplete="off" placeholder="密价" class="layui-input" lay-verify="required">
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
            var form = layui.form, layer = layui.layer, layedit = layui.layedit, laydate = layui.laydate;

            // 手动加款按钮
            form.on('submit(recharge-windows)', function(data){
                $('input[name=name]').val(data.elem.getAttribute('data-name'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加游戏',
                    area: ['400px', '500px'],
                    content: $('#recharge')
                });
                return false;
            });

            form.on('submit(recharge)', function(data){
                layer.confirm('您确认要添加吗？', {icon: 3, title:'提示'}, function(index){
                    $.post('{{ route('backend.steam.store-price.insertStorePrice') }}', {clone_price:data.field.game_name,user_id:data.field.user_id}, function(result){
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