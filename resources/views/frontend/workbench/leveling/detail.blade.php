@extends('frontend.layouts.app')

@section('title', '工作台 - 代练 - 订单详情')

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


    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li class="layui-this">详情</li>
            <li>留言/截图</li>
            <li>操作记录</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-row  layui-col-space20">
                    <div class="layui-col-md6">
                        <div class="site-title">
                            <fieldset><legend><a name="hr">订单信息</a></legend></fieldset>
                        </div>
                        <form class="layui-form" action="">
                            <input type="hidden" name="no" value="{{ $detail['no'] }}">
                            <div class="layui-row form-group">
                                <div class="layui-col-md6">
                                    <div class="layui-col-md3 layui-form-mid">*游戏</div>
                                    <div class="layui-col-md8">
                                        <select name="game_id" lay-verify="required" lay-search="" @if(!in_array($detail['status'], [1, 23]))  disabled="disabled"  @endif>
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
                                            @if($item->field_type == 1 && in_array($detail['status'], [1, 23]))
                                                <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                            @elseif($item->field_type == 1)

                                                @if(in_array($detail['status'], [13, 17]) && in_array($item->field_name, ['game_leveling_amount', 'password', 'game_leveling_day' , 'game_leveling_hour']))
                                                    <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                                @elseif(in_array($detail['status'], [14]) && $item->field_name == 'game_leveling_amount')
                                                    <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                                @elseif(in_array($detail['status'], [18]) && $item->field_name == 'password')
                                                    <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input  " lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}">
                                                @else
                                                    <input type="text" name="{{ $item->field_name }}"  autocomplete="off" class="layui-input layui-disabled" lay-verify="@if ($item->field_required == 1) required @endif" value="{{ $detail[$item->field_name] ?? '' }}"  readonly="readonly" disabled="disabled">
                                                @endif

                                            @endif

                                            @if($item->field_type == 2 && in_array($detail['status'], [1, 23]))
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
                                            @elseif($item->field_type == 2)
                                                <select name="{{ $item->field_name }}"  lay-search="" lay-verify="@if ($item->field_required == 1) required @endif" class="layui-disabled" disabled>
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

                                            @if($item->field_type == 3 && in_array($detail['status'], [1, 23]))

                                            @endif

                                            @if($item->field_type == 4 && in_array($detail['status'], [1, 23]))
                                                <textarea name="{{ $item->field_name }}"  class="layui-textarea"  lay-verify="@if($item->field_required == 1) required @endif">{{ $detail[$item->field_name] }}</textarea>
                                            @elseif($item->field_type == 4)
                                                <textarea name="{{ $item->field_name }}" class="layui-textarea"  lay-verify="@if($item->field_required == 1) required @endif"  class="layui-disabled" disabled>{{ $detail[$item->field_name] }}</textarea>
                                            @endif

                                            @if($item->field_type == 5 && in_array($detail['status'], [1, 23]))
                                                <input type="checkbox" name="{{ $item->field_name }}" lay-skin="primary"  lay-verify="@if($item->field_required == 1) require @endif" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] == 1) checked @endif>
                                            @elseif($item->field_type == 5)
                                                <input type="checkbox" name="{{ $item->field_name }}" lay-skin="primary"  lay-verify="@if($item->field_required == 1) require @endif" class="layui-disabled" disabled @if(isset($detail[$item->field_name]) && $detail[$item->field_name] == 1) checked @endif>
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

                    <div class="layui-col-md3">
                        <div class="site-title">
                            <fieldset><legend><a name="hr">订单数据</a></legend></fieldset>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">平台单号：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">订单状态：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">支付金额：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">手续费：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">利润：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">打手呢称：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">剩余代练时间：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">发布时间：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">接单时间：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">提验时间：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">结算时间：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                        <div class="layui-row form-group">
                            <div class="layui-col-md4 text_right">发单客服：</div>
                            <div class="layui-col-md8">淘宝</div>
                        </div>
                    </div>

                    <div class="layui-col-md3">
                        <div class="site-title">
                            <fieldset><legend><a name="hr">订单来源</a></legend></fieldset>
                        </div>

                    </div>
                </div>
            </div>
            <div class="layui-tab-item">2</div>
            <div class="layui-tab-item">3</div>
        </div>
    </div>

@endsection

<!--START 底部-->
@section('js')
<script>
    layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
        var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;


        form.on('checkbox', function(data){
            if (data.elem.checked) {
                $(data.elem).val(1);
            } else {
                $(data.elem).remove();
                $('.layui-form').append('<input type="hidden" name="' + $(data.elem).attr("name") + '" value="0"/>');
            }
        });

        // 修改
        form.on('submit(save-update)', function (data) {
            $.post('{{ route('frontend.workbench.leveling.update') }}', {data: data.field}, function (result) {
                if (result.status == 1) {
                    layer.open({
                        content: '修改成功!',
                        btn: ['继续发布', '订单列表', '待发订单'],
                        btn1: function(index, layero){
                            location.reload();
                        },
                        btn2: function(index, layero){
                            window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                        },
                        btn3: function(index, layero){
                            window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                        }
                    });
                } else {
                    layer.msg(result.message);
                }
            }, 'json');
            return false;
        });
    });
</script>
@endsection