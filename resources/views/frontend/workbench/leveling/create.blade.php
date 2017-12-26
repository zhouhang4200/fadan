@extends('frontend.layouts.app')

@section('title', '工作台 - 代练 - 订单发布')

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
                <div id="template">

                </div>
                <div class="layui-btn layui-btn-normal  layui-col-md12" lay-submit="" lay-filter="order">确定</div>
            </form>
        </div>

        <div class="layui-col-md3">
            <div class="site-title">
                <fieldset><legend><a name="hr">发单模版</a></legend></fieldset>
            </div>
            <div class=""></div>
            <div class="">
                <div class="layui-row form-group">
                    <textarea name="desc" class="layui-textarea" style="min-height: 800px"></textarea>
                </div>
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
<script id="goodsTemplate" type="text/html">
        <input type="hidden" name="id" value="@{{ d.id }}">
        <div class="layui-row form-group">
        @{{# var row = 0;}}
        @{{#  layui.each(d.template, function(index, item){ }}

            @{{#  if(row == 0) { row = item.display_form;  }  }}

            <div class="layui-col-md6">
                <div class="layui-col-md3 layui-form-mid">
                    @{{# if (item.field_required == 1) {  }}<span style="color: orangered;">*</span>@{{# }  }} @{{ item.field_display_name  }}
                </div>
                <div class="layui-col-md8">

                    @{{# if(item.field_type == 1) {  }}
                        <input type="text" name="@{{ item.field_name }}"  autocomplete="off" class="layui-input" lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}">
                    @{{# } }}

                    @{{# if(item.field_type == 2) {  }}
                        <select name="@{{ item.field_name }}"  lay-search="" lay-verify="@{{# if (item.field_required == 1) { }}required@{{# } }}">
                                <option value=""></option>
                                @{{#  if(item.user_values.length > 0){ }}
                                    @{{#  layui.each(item.user_values, function(i, v){ }}
                                    <option value="@{{ v.field_value }}">@{{ v.field_value }}</option>
                                    @{{#  }); }}
                                @{{#  } else { }}
                                    @{{#  if(item.values.length > 0){ }}
                                        @{{#  layui.each(item.values, function(i, v){ }}
                                        <option value="@{{ v.field_value }}">@{{ v.field_value }}</option>
                                        @{{#  }); }}
                                    @{{#  }  }}
                                @{{#  }  }}
                        </select>
                    @{{# } }}

                    @{{# if(item.field_type == 3) {  }}
                        <input type="checkbox" name="@{{ item.field_name }}" lay-skin="primary" lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# }  }}">
                    @{{# } }}

                    @{{# if(item.field_type == 4) {  }}
                        <textarea name="@{{ item.field_name }}" placeholder="请输入内容" class="layui-textarea"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}"></textarea>
                    @{{# } }}

                    @{{# if(item.field_type == 5) {  }}

                    @{{# } }}

                </div>
            </div>

            @{{#  row--; }}

            @{{# if(row == 0) { }}
                </div>
                <div class="layui-row form-group">
            @{{# }  }}

        @{{# })  }}
        </div>

    </script>
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