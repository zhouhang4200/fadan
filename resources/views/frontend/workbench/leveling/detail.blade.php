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
                                <option value="{{ $key }}">{{ $value }}</option>
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
                                @elseif(in_array($detail['status'], [14, 18]) && $item->field_name == 'game_leveling_amount' || $item->field_name == 'password')
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
                                <input type="checkbox" name="{{ $item->field_name }}" lay-skin="primary" lay-verify="@if($item->field_required == 1) require @endif">
                            @elseif($item->field_type == 3)
                                <input type="checkbox" name="{{ $item->field_name }}" lay-skin="primary" lay-verify="@if($item->field_required == 1) require @endif" class="layui-disabled" disabled>
                            @endif


                            @if($item->field_type == 4 && in_array($detail['status'], [1, 23]))
                                <textarea name="{{ $item->field_name }}" placeholder="请输入内容" class="layui-textarea"  lay-verify="@if($item->field_required == 1) required @endif"></textarea>
                            @elseif($item->field_type == 4)
                                <textarea name="{{ $item->field_name }}" placeholder="请输入内容" class="layui-textarea"  lay-verify="@if($item->field_required == 1) required @endif"  class="layui-disabled" disabled></textarea>
                            @endif

                            @if($item->field_type == 5 && in_array($detail['status'], [1, 23]))

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

                <div class="layui-btn layui-btn-normal  layui-col-md12" lay-submit="" lay-filter="order">确定</div>
            </form>
        </div>

        <div class="layui-col-md3">
            <div class="site-title">
                <fieldset><legend><a name="hr">发单模版</a></legend></fieldset>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md3 text_right">账号：</div>
                <div class="layui-col-md8">淘宝</div>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md3 text_right">来源价格：</div>
                <div class="layui-col-md8">淘宝</div>
            </div>
        </div>

        <div class="layui-col-md3">
            <div class="site-title">
                <fieldset><legend><a name="hr">订单来源</a></legend></fieldset>
            </div>

            <div class="layui-row form-group">
                <div class="layui-col-md3 text_right">账号：</div>
                <div class="layui-col-md8">淘宝</div>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md3 text_right">来源价格：</div>
                <div class="layui-col-md8">淘宝</div>
            </div>

        </div>
    </div>
@endsection

<!--START 底部-->
@section('js')
<script>
    layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
        var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;

        var getTpl = goodsTemplate.innerHTML, view = $('#template');
        $.post('{{ route('frontend.workbench.leveling.get-template') }}', {game_id:1}, function (result) {
            layTpl(getTpl).render(result.content, function(html){
                view.html(html);
                layui.form.render();
            });
        }, 'json');

        // 下单
        form.on('submit(order)', function (data) {
            $.post('{{ route('frontend.workbench.leveling.create') }}', {data: data.field}, function (result) {
                layer.msg(result.message)
            }, 'json');
            return false;
        });

    });
</script>
@endsection