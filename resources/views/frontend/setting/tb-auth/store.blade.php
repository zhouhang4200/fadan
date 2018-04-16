@extends('frontend.layouts.app')

@section('title', '设置 - 店铺授权')

@section('css')

@endsection

@section('submenu')
    @include('frontend.setting.submenu')
@endsection

@section('main')
    <div class="explanation">
    <div class="ex_tit" style="margin-bottom: 10px;"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示" class=""></span></div>
    <ul>
    <li>该绑定用于抓取您淘宝店铺的订单。绑定成功后您店铺订单会自动同步到平台中。</li>
    </ul>
    </div>
    <form class="layui-form layui-form-pane" action="">
        <div class="layui-inline">
            <label class="layui-form-label">店铺旺旺</label>
            <div class="layui-input-inline">
                <input type="text" name="wang_wang" autocomplete="off" lay-verify="required" class="layui-input" value=""  placeholder="请输入店铺旺旺">
            </div>
            <button class="layui-btn" lay-submit="" lay-filter="auth">授权</button>
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
                    <button class="layui-btn layui-btn-normal layui-btn-small" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">删除</button>
                </td>
            </tr>
        @empty

        @endforelse
        </tbody>
    </table>

@endsection

@section('js')
    <script>
        layui.use(['form', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;

            //监听提交
            form.on('submit(auth)', function(data){
                $.post('{{ route('frontend.setting.tb-auth.store-auth') }}', { wang_wang:data.field.wang_wang }, function (result) {
                    if (result.status == 1) {
                        layer.alert('授权成功', {
                            title: '最终的提交信息'
                        });

                    } else {
                        window.open(result.content.url);
                    }
                }, 'json');
                return false;
            });

            @if($bindResult == 1)
                reload();
            @endif
        });
    </script>
@endsection