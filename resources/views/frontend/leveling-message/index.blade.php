<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>千手平台</title>
    <meta name="_token" content="{{ csrf_token() }}" >
    <link rel="stylesheet" href="/vendor/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/css/layui-rewrit.css">
    <style>
        .opt{
            cursor: default;
            margin: 0 10px;
        }
    </style>
</head>
<body>
<div class="layui-form">
    <table class="layui-table" lay-skin="line" lay-size="sm" style="margin: 0 0 40px 0; ">
        <tbody>
        @forelse($message as $item)
            <tr data-no="{{ $item->order_no }}">
                <td width="30%">天猫单号：{{ $item->foreign_order_no }}</td>
                <td>{{ $item->created_at }}  留言：{{ str_limit( $item->contents, 58) }}</td>
                <td width="15%">
                    <a href="{{ route('frontend.workbench.leveling.detail') }}?no={{ $item->order_no }}&tab=1" class="opt"  data-no="{{ $item->order_no }}" target="_blank" lay-submit=""  lay-filter="detail">详情</a> <span class="opt" lay-submit=""  lay-filter="del" data-id="{{ $item->id }}">删除</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" align="center">暂时没有留言</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    @if(count($message))
    <div id="del-all" style="background:#fff;position: fixed;bottom: 0;height: 40px;width:100%;border-top:1px solid #ccc;line-height: 40px;text-align: center">
        <button class="layui-btn layui-btn-normal layui-btn-custom-mini" lay-submit=""  lay-filter="del-all">全部删除</button>
    </div>
    @endif
</div>
</body>
<script src="/vendor/layui/layui.js"></script>
<script src="/js/jquery-1.11.0.min.js"></script>
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});
    layui.use(['form',  'layer', 'element'], function(){
        var layer = layui.layer, form = layui.form, elem = layui.element;

        form.on('submit(del)', function (data) {
            var currentObj =  $(this);
            layer.confirm('您确定要删除留言吗?' , function(index){
                $.post('{{ route("frontend.message-del") }}',{id:data.elem.getAttribute('data-id')}, function (result) {
                    layer.msg(result.message);
                    currentObj.parent().parent().remove();
                    if ($('.layui-table').find("tr").length == 0) {
                        $('#del-all').hide();
                        $('.layui-table').html('<tr><td colspan="5" align="center">暂时没有留言</td></tr>');
                    }
                }, 'json');
                layer.close(index);
            });
        });

        form.on('submit(del-all)', function (data) {
            layer.confirm('您确定要删除所有留言吗?' , function(index){
                $.post('{{ route("frontend.message-del-all") }}',{id:1}, function (result) {
                    layer.msg(result.message);
                    $('.layui-table').html('<tr><td colspan="5" align="center">暂时没有留言</td></tr>');
                }, 'json');
                layer.close(index);
            });
        });

        form.on('submit(detail)', function (data) {
            var no =  data.elem.getAttribute('data-no');
            $.post('{{ route("frontend.message-del") }}',{no:no}, function () {
                $('tr[data-no='+ no +']').remove();
                if ($('.layui-table').find("tr").length == 0) {
                    $('#del-all').hide();
                    $('.layui-table').html('<tr><td colspan="5" align="center">暂时没有留言</td></tr>');
                }
            }, 'json');
        });

    });
</script>
</html>
