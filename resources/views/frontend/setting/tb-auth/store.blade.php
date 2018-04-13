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

        <?php $callBack = route('frontend.setting.tb-auth.store') . '?id=' .  Auth::user()->id . '&sign=' . md5(Auth::user()->id . Auth::user()->name)  ?>
        @if(Auth::user()->store_wang_wang)
            <div class="layui-inline">
                <label class="layui-form-label">已绑定旺旺</label>
                <div class="layui-input-inline">
                    <input type="text" name="qq" autocomplete="off" class="layui-input" value="{{ Auth::user()->store_wang_wang }}">
                </div>
                <a  href="http://api.kamennet.com/API/CallBack/TOP/SiteInfo_New.aspx?SitID=90347&Sign=b7753b8d55ba79fcf2d190de120a5229&CallBack={{ urlencode($callBack) }}" target="_blank" class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">修改绑定</a>
            </div>
        @else
            <a  href="http://api.kamennet.com/API/CallBack/TOP/SiteInfo_New.aspx?SitID=90347&Sign=b7753b8d55ba79fcf2d190de120a5229&CallBack={{ urlencode($callBack) }}" target="_blank" class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">现在绑定</a>
        @endif

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
                <td>{{ $item->wnag_wang }}</td>
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

@endsection

@section('js')
    <script>
        layui.use(['form', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;

            @if($bindResult == 1)
                layer.msg('授权成功');
            @elseif($bindResult == 2)
                layer.msg('授权失败，该旺旺已经绑定其它账号');
            @endif
        });
    </script>
@endsection