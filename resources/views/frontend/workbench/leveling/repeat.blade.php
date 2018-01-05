@extends('frontend.layouts.app')

@section('title', '工作台 - 代练 - 重新下单')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .wrapper {
            width: 1600px;
        }
        .main .right {
            width: 1430px;
        }
        .layui-input-block{
            margin-left: 50px;
        }
        .form-group {
            margin-bottom: 7px;
        }

        .layui-form-mid {
            text-align: right;
        }

        .site-title {
            margin: 10px 0 20px;
        }
        .site-title fieldset {
            border: none;
            padding: 0;
            border-top: 1px solid #eee;
        }
        .site-title fieldset legend {
            font-size: 17px;
            font-weight: 300;
        }
        .layui-form-checkbox[lay-skin=primary] {
            height: 6px !important;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')

    <div class="layui-tab layui-tab-brief" lay-filter="myFilter">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-row  layui-col-space20">
                    <div class="layui-col-md6">
                        <div class="site-title">
                            <fieldset><legend><a name="hr">订单信息</a></legend></fieldset>
                        </div>
                        <form class="layui-form" action="">
                            <div class="layui-row form-group">
                                <div class="layui-col-md6">
                                    <div class="layui-col-md3 layui-form-mid">*游戏</div>
                                    <div class="layui-col-md8">
                                        <select name="game_id" lay-verify="required" lay-search="">
                                            @foreach($game as $key => $value)
                                                <option value="{{ $key }}" @if($value == $detail['game_name']) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-row form-group">
                                <?php $row = 0 ?>
                                @forelse($template as $item)

                                    @if($row == 0)
                                        <?php  $row = $item->display_form ?>
                                    @endif

                                    <div class="layui-col-md6">
                                        <div class="layui-col-md3 layui-form-mid"> @if ($item->field_required == 1) <span style="color: orangered;">*</span> @endif
                                            {{ $item->field_display_name  }}
                                        </div>
                                        <div class="layui-col-md8">

                                            <!--订单状态为 没有接单 已下架时可以编辑该属性-->
                                            @if($item->field_type == 1)
                                                <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                            @endif

                                            @if($item->field_type == 2)
                                                <select name="{{ $item->field_name }}"  lay-search="" lay-verify="@if ($item->field_required == 1) required @endif">
                                                    <option value=""></option>
                                                    @if(count($item->user_values) > 0)

                                                        @foreach($item->user_values as $v)
                                                            <option value="{{ $v->field_value }}" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] ==  $v->field_value) selected  @endif>{{ $v->field_value }}</option>
                                                        @endforeach

                                                    @else

                                                        @if(count($item->values) > 0)
                                                            @foreach($item->values as $v)
                                                                <option value="{{ $v->field_value }}" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] ==  $v->field_value) selected  @endif>{{ $v->field_value }}</option>
                                                            @endforeach
                                                        @endif

                                                    @endif
                                                </select>
                                            @endif

                                            @if($item->field_type == 3)

                                            @endif

                                            @if($item->field_type == 4)
                                                <textarea name="{{ $item->field_name }}"  class="layui-textarea"  lay-verify="@if($item->field_required == 1) required @endif">{{ $detail[$item->field_name] ?? '' }}</textarea>
                                            @endif

                                            @if($item->field_type == 5)
                                                <input type="checkbox" name="{{ $item->field_name }}" lay-skin="primary"  lay-verify="@if($item->field_required == 1) require @endif" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] == 1) checked @endif>
                                            @endif

                                        </div>
                                    </div>

                                    <?php $row--; ?>

                                    @if($row == 0)
                            </div>
                            <div class="layui-row form-group">
                                @endif

                                @empty

                                @endforelse
                            </div>

                            <div class="layui-col-md-offset2">
                                <div class="layui-btn layui-btn-normal  layui-col-md2" lay-submit="" lay-filter="save-update">确定</div>
                            </div>
                        </form>
                    </div>
                    <div class="layui-col-md6">
                        <div class="site-title">
                            <fieldset><legend><a name="hr">订单来源</a></legend></fieldset>
                        </div>

                    </div>
                </div>
            </div>
            <div class="layui-tab-item">&nbsp;</div>
            <div class="layui-tab-item">
                <div class="layui-row  layui-col-space20" id="history"></div>
            </div>
        </div>
    </div>

@endsection

<!--START 底部-->
@section('js')
<script>
    layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
        var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;

        $('.cancel').click(function(){
            layer.closeAll();
        });

        form.on('checkbox', function(data){
            if (data.elem.checked) {
                $(data.elem).val(1);
            } else {
                $(data.elem).remove();
                $('.layui-form').append('<input type="hidden" name="' + $(data.elem).attr("name") + '" value="0"/>');
            }
        });

        // 监听Tab切换
        element.on('tab(myFilter)', function(){
            switch (this.getAttribute('lay-id')) {
                case 'history':
                    // 加载订单操作记录
                    $.get("{{ route('frontend.workbench.leveling.history', ['order_no' => $detail['no']]) }}", function (data) {
                        if (data.status === 1) {
                            $('#history').html(data.content);
                        } else {
                            layer.alert(data.message);
                        }
                    });

                    break;

                case 'leave-word':
                    // 加载留言截图
                    layer.msg('建设中');
                    break;
                default:
                    break;
            }
        });

    });
</script>
@endsection
