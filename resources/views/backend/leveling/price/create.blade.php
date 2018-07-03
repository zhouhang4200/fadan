@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单-新增')

@section('css')
    <style>
        .layui-form-label {
            width:140px;
        }

        .layui-input, .layui-textarea {
            display: inline;
            width:300px;
            /* width: 100%; */
            padding-left: 10px;
        }
        .tips {
            position: absolute;
            width: 10%;
            height: 30px;
            right: -130px;
            top: 5px;
            text-align: center
        }

        .tips .iconfont {
            left: -5px;
            font-size: 25px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="">新增</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="" action="">
                                {!! csrf_field() !!}
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*序号</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="game_leveling_number" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                            <div class="tips" lay-tips="此表按照序号排序，修改序号时，相同序号及更大序号对应的配置，序号+1">
                                                <i class="iconfont icon-exclamatory-mark-r"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*代练层级</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="game_leveling_level" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                            <div class="tips" lay-tips="该序号的描述，可以是段位/等级或其他，前端读取此配置作为用户的代练目标选项">
                                                <i class="iconfont icon-exclamatory-mark-r"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*到下一层级价格</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="level_price" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                            <div class="tips" lay-tips="该序号到下一序号的代练价格，没有下一序号则找到最近的序号，前端读取用户所选的代练层级区间，计算出对应价格">
                                                <i class="iconfont icon-exclamatory-mark-r"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*到下一层级耗时</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="level_hour" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                            <div class="tips" lay-tips="该序号到下一序号的代练时间，没有下一序号则找到最近的序号，前端读取用户所选的代练层级区间，计算出对应代练时间">
                                                <i class="iconfont icon-exclamatory-mark-r"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*该层级安全保证金</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="level_security_deposit" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                            <div class="tips" lay-tips="账号当前属于该层级，则安全保证金等于此金额">
                                                <i class="iconfont icon-exclamatory-mark-r"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">*该层级效率保证金</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="level_efficiency_deposit" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                            <div class="tips" lay-tips="账号当前属于该层级，则效率保证金等于此金额">
                                                <i class="iconfont icon-exclamatory-mark-r"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-inline">
                                            <button class="layui-btn layui-btn-normal" game-id="{{ $gameId }}" game-name="{{ $gameName }}" type="{{ $type }}" lay-submit="add" lay-filter="add">立即提交</button>
                                        </div>
                                    </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    //Demo
    layui.use(['form', 'layedit', 'laytpl', 'element', 'laydate', 'table', 'upload'], function(){
        var form = layui.form, layer = layui.layer, laydate = layui.laydate, layTpl = layui.laytpl,
                element = layui.element, table=layui.table, upload = layui.upload;

        form.on('submit(add)', function (data) {
            var game_id=this.getAttribute('game-id');
            var game_name=this.getAttribute('game-name');
            var type=this.getAttribute('type');
            $.post("{{ route('config.leveling.price.store') }}", {data:data.field, game_id:game_id, game_name:game_name, type:type}, function (result) {
                if (result.status == 1) {
                    layer.msg(result.message);
                    window.location.href="{{ route('config.leveling.price.index') }}?game_id="+game_id+'&game_name='+game_name+'&type='+type;
                } else {
                    layer.msg(result.message);
                }
                return false;
            });
                return false;
        });
    });
    // 提示
    $(document).on("mouseenter", "*[lay-tips]", function () {
        var e = $(this);
        if (!e.parent().hasClass("layui-nav-item") || u.hasClass(g)) {
            var t = e.attr("lay-tips"),
                i = e.attr("lay-offset"),
                n = e.attr("lay-direction"),
                s = layer.tips(t, this, {
                    tips: n || 1,
                    time: -1,
                    success: function (e, a) {
                        i && e.css("margin-left", i + "px")
                    }
                });
            e.data("index", s)
        }
    }).on("mouseleave", "*[lay-tips]", function () {
        layer.close($(this).data("index"))
    });
</script>
@endsection