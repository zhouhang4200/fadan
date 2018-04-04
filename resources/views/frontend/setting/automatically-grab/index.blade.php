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
            <li>用途：添加了某一淘宝/天猫商品，则会自动获取该商品对应的订单，未添加商品则无法获取商品对应订单。请确保添加商品之前，已进行店铺旺旺绑定。</li>
        </ul>
    </div>

    <div id="data">
        @include('frontend.setting.automatically-grab.list')
    </div>

    <div id="goods-add" style="display: none;padding: 20px">
        <form class="layui-form" action="" id="goods-add-form">
            <input type="hidden" name="type" value="">
            <div class="layui-form-item">
                <select name="service_id" lay-verify="required">
                    <option value="">类型</option>
                    @foreach ($services as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <div class="layui-form-item">
                <input type="text" name="foreign_goods_id" required lay-verify="required" placeholder="淘宝链接" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-item layui-form-text">
                <textarea name="remark" placeholder="备注信息" class="layui-textarea"></textarea>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="goods-add-save">确定添加</button>
                <button  type="button" class="layui-btn layui-btn-danger cancel">取消添加</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;
            // 按用户搜索
            form.on('submit(user-search)', function (data) {
                var par = '?id=' + data.field.other_user_id;
                loadData('{{ route('frontend.automatically-grab.goods') }}' + par);
                return false;
            });

            // 点击页码翻页
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                loadData($(this).attr('href'));
                return false;
            });

            // 按用户ID添加
            form.on('submit(goods-add)', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加商品',
                    area: ['500px'],
                    content: $('#goods-add')
                });
                return false;
            });

            $('.content').on('blur', 'input[name=foreign_goods_id]', function () {
                var url = $(this).val();
                var goodsId = getQueryString(url, 'id');
                if (isNaN($(this).val())) {
                    $(this).val($.trim(goodsId));
                }
            });

            $('#goods-form').on('blur', 'input[name=other_user_id]', function () {
                var url = $(this).val();
                var goodsId = getQueryString(url, 'id');
                if (isNaN($(this).val())) {
                    $(this).val($.trim(goodsId));
                }
            });

            // 按用户加载数据
            function loadData(url) {
                $.get(url, function (result) {
                    $('#data').html(result);
                    layui.form.render();
                }, 'json');
            }

            // 保存按商品添加
            form.on('submit(goods-add-save)', function (data) {
                $.post('{{ route('frontend.automatically-grab.add') }}', {
                    service_id:data.field.service_id,
                    foreign_goods_id:data.field.foreign_goods_id,
                    remark:data.field.remark
                }, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        loadData('{{ route('frontend.automatically-grab.goods') }}');
                    }
                }, 'json');
                return false;
            });

            // 删除用户名单中的用户ID
            form.on('submit(delete-goods)', function (data) {
                layer.confirm('您确定要删除吗?', {icon: 3, title:'提示'}, function(){
                    $.post('{{ route('frontend.automatically-grab.delete') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        if (result.status == 1) {
                            loadData('{{ route('frontend.automatically-grab.goods') }}');
                        }
                    }, 'json');
                });
                return false;
            });

            $('.cancel').click(function () {
               layer.closeAll();
            });
        });
    </script>
@endsection