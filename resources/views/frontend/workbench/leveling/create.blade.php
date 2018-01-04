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
            position: relative;
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
        .layui-layer-btn .layui-layer-btn0,.layui-layer-btn .layui-layer-btn1,.layui-layer-btn .layui-layer-btn2   {
            border-color: #1E9FFF;
            background-color: #1E9FFF;
            color:#ffffff;
        }
        .layui-layer-dialog .layui-layer-content {
            text-align: center;
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
                <div class="layui-col-md-offset2">
                    <div class="layui-btn layui-btn-normal  layui-col-md2" lay-submit="" lay-filter="order">确定</div>
                </div>
            </form>
        </div>

        <div class="layui-col-md3">
            <div class="site-title">
                <fieldset><legend><a name="hr">发单模版</a></legend></fieldset>
            </div>
            <div class="layui-row " style="margin-bottom: 15px">
                <div class="layui-col-md4">
                    <div class="layui-btn layui-btn-normal layui-col-md12" lay-submit="" lay-filter="analysis-template">解析模版</div>
                </div>
                <div class="layui-col-md4 layui-col-md-offset4">
                    <div class="layui-btn layui-btn-normal layui-col-md12" lay-submit="" lay-filter="instructions">使用说明</div>
                </div>
            </div>
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
                        <input type="text" name="@{{ item.field_name }}"  autocomplete="off" class="layui-input" lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}|@{{ item.verify_rule }}">
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
                    @{{# } }}

                    @{{# if(item.field_type == 4) {  }}
                        <textarea name="@{{ item.field_name }}" placeholder="请输入内容" class="layui-textarea"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}"></textarea>
                    @{{# } }}

                    @{{# if(item.field_type == 5) {  }}
                    <input type="checkbox" name="@{{ item.field_name }}" lay-skin="primary"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# }  }}">
                    @{{# } }}

                    @{{# if(item.help_text != null || item.help_text != undefined) {  }}
                        <a href="#" class="tooltip">
                            <i class="iconfont icon-wenhao" id="recharge"></i>
                            <span>@{{ item.help_text }}</span>
                        </a>
                    @{{# }  }}

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

        //自定义验证规则
        form.verify({
            price: function(value){
                if(value == 0){
                    return '代练价格不能为0';
                }
            }
        });

        var getTpl = goodsTemplate.innerHTML, view = $('#template');
        $.post('{{ route('frontend.workbench.leveling.get-template') }}', {game_id:1}, function (result) {
            layTpl(getTpl).render(result.content, function(html){
                view.html(html);
                layui.form.render();
            });
        }, 'json');

        form.on('submit(instructions)', function () {
            layer.open({
                type: 1
                ,title: '使用说明' //不显示标题栏
                ,closeBtn: false
                ,area: '470px;'
                ,shade: 0.2
                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                ,btn: ['确定']
                ,btnAlign: 'c'
                ,content: '<div style="padding: 10px 15px; line-height: 22px;   font-weight: 300;">1.选择“游戏”后会自动显示对应模板。<br/>2.将模版复制，发给号主填写。<br/>3.粘贴号主填写好的模版，粘贴至模板输入框内。<br/>4.点击“解析模板”按钮将资料导入至左侧表格内，点击“发布”按钮，即可创建订单。</div>'
            });
        });

        // 下单
        form.on('submit(order)', function (data) {
            $.post('{{ route('frontend.workbench.leveling.create') }}', {data: data.field}, function (result) {

                if (result.status == 1) {
                    layer.open({
                        content: '发布成功!',
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