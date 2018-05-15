@extends('frontend.v1.layouts.app')

@section('title', '设置 - 店铺授权')

@section('css')

@endsection

@section('main')
<div class="layui-card-body">
    <div class="explanation">
    <div class="ex_tit" style="margin-bottom: 10px;"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示" class=""></span></div>
    <ul>
    <li>该绑定用于抓取您淘宝店铺的订单。绑定成功后您店铺订单会自动同步到平台中。</li>
    </ul>
    </div>
    <form class="layui-form layui-form-pane" action="">
        <div class="layui-inline">
            @php
                $callBack = route('frontend.setting.tb-auth.store') . '?id=' .  auth()->user()->id . '&sign=' . md5(auth()->user()->id . auth()->user()->name);
                $url = 'http://api.kamennet.com/API/CallBack/TOP/SiteInfo_New.aspx?SitID=90347&Sign=b7753b8d55ba79fcf2d190de120a5229&CallBack=' . urlencode($callBack);
            @endphp
            <a href="{{ $url }}" class="qs-btn" lay-submit="" lay-filter="auth">授权</a>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>店铺旺旺</th>
            <th>添加时间</th>
            <th width="13%">操作</th>
        </tr>
        </thead>
        <tbody>

        @forelse($taobaoShopAuth as $item)
            <tr>
                <td>{{ $item->wang_wang }}</td>
                <td>{{ $item->created_at }}</td>
                <td>
                    <button class="qs-btn layui-btn-normal layui-btn-small" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">删除</button>
                </td>
            </tr>
        @empty

        @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;

            @if($bindResult == 1)
                reload();
            @endif

            // 删除
            form.on('submit(delete-goods)', function (data) {
                layer.confirm('您确定要删除吗?', {icon: 3, title:'提示'}, function(){
                    $.post('{{ route('frontend.setting.tb-auth.destroy') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        if (result.status == 1) {
                            setTimeout(function () {
                                location.reload();
                            }, 700);
                        }
                    }, 'json');
                });
                return false;
            });
        });
    </script>
@endsection