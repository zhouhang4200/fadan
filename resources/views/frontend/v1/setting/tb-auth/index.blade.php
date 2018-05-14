@extends('frontend.v1.layouts.app')

@section('title', '设置 - 店铺授权')

@section('css')

@endsection

@section('main')
<div class="layui-card qs-text">
<div class="layui-card-body">
    <div class="explanation">
    <div class="ex_tit" style="margin-bottom: 10px;"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示" class=""></span></div>
    <ul>
    <li>绑定该旺旺用于自动加款。绑定成功后，您拍下的加款卡金额会自动加到您的平台账号余额中。</li>
    </ul>
    </div>
    <form class="layui-form layui-form-pane" action="">

        <?php $callBack = route('frontend.setting.tb-auth.index') . '?id=' .  Auth::user()->id . '&sign=' . md5(Auth::user()->id . Auth::user()->name)  ?>
        @if(Auth::user()->wang_wang)
            <div class="layui-inline">
                <label class="layui-form-label">已绑定旺旺</label>
                <div class="layui-input-inline">
                    <input type="text" name="qq" autocomplete="off" class="layui-input" value="{{ Auth::user()->wang_wang }}">
                </div>
                <a  href="http://api.kamennet.com/API/CallBack/TOP/SiteInfo_New.aspx?SitID=90347&Sign=b7753b8d55ba79fcf2d190de120a5229&CallBack={{ urlencode($callBack) }}" target="_blank" class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">修改绑定</a>
            </div>
        @else
            <a  href="http://api.kamennet.com/API/CallBack/TOP/SiteInfo_New.aspx?SitID=90347&Sign=b7753b8d55ba79fcf2d190de120a5229&CallBack={{ urlencode($callBack) }}" target="_blank" class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">现在绑定</a>
        @endif

    </form>
</div>
</div>
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