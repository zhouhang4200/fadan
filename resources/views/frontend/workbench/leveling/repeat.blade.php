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
                        <form class="layui-form" action="" id="form-order">
                            <input type="hidden" name="creator_user_id" value="{{ $detail['creator_user_id'] ?? '' }}">
                            <input type="hidden" name="seller_nick" value="{{ $detail['seller_nick'] ?? '' }}">
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


                                                    @if($item->field_name == 'user_phone')
                                                        @foreach($contact as $v)
                                                            @if($v->type == 1)
                                                                <option data-id="{{ $v->content  }}"  value="{{ $v->content }}" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] ==  $v->content) selected  @endif>{{ $v->name }}-{{ $v->content }}</option>
                                                            @endif
                                                        @endforeach
                                                    @elseif($item->field_name == 'user_qq')
                                                        @foreach($contact as $v)
                                                            @if($v->type == 2)
                                                                <option data-id="{{ $v->content  }}"  value="{{ $v->content }}" @if(isset($detail[$item->field_name]) && $detail[$item->field_name] ==  $v->content) selected  @endif>{{ $v->name }}-{{ $v->content }}</option>
                                                            @endif
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
                                <div class="layui-btn layui-btn-normal  layui-col-md2" lay-submit="" lay-filter="order">确定</div>
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
                    <input type="text" name="@{{ item.field_name }}"  autocomplete="off" class="layui-input" lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}|@{{ item.verify_rule }}" display-name="@{{item.field_display_name}}">
                    @{{# } }}

                    @{{# if(item.field_type == 2) {  }}
                    <select name="@{{ item.field_name }}"  lay-search="" lay-verify="@{{# if (item.field_required == 1) { }}required@{{# } }}"  display-name="@{{item.field_display_name}}">
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
                    <textarea name="@{{ item.field_name }}" placeholder="请输入内容" class="layui-textarea"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}"  display-name="@{{item.field_display_name}}"></textarea>
                    @{{# } }}

                    @{{# if(item.field_type == 5) {  }}
                    <input type="checkbox" name="@{{ item.field_name }}" lay-skin="primary"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# }  }}"  display-name="@{{item.field_display_name}}">
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

        // 切换游戏时加截新的模版
        form.on('select(game)', function (data) {
            loadTemplate(data.value)
        });
        // 加载默认模板
        loadTemplate({{ $detail['game_id'] }});
        // 加载模板
        function loadTemplate(id) {
            var getTpl = goodsTemplate.innerHTML, view = $('#template');
            $.post('{{ route('frontend.workbench.leveling.get-template') }}', {game_id:id}, function (result) {
                var template = '游戏：\r\n';
                $.each(result.content.template, function(index,element){
                    template += element.field_display_name + '：\r\n'
                });
                $('#user-template').val(template);

                layTpl(getTpl).render(result.content, function(html){
                    view.html(html);
                    layui.form.render();
                });
            }, 'json');
        }

        // 下单
        form.on('submit(order)', function (data) {

            //自定义验证规则
            form.verify({
                zero: function(value){
                    if(value <= 0){
                        return '该数值需大于0';
                    }
                },
                money:function (value) {
                    if (value.indexOf(".") > -1) {
                        var temp  = value.split(".");
                        if (temp.length > 2) {
                            return '请输入合法的金额';
                        }
                        if (temp[1].length > 2) {
                            return '输入的小数请不要大于两位';
                        }
                    }
                },
                gt5:function (value) { // 大于5
                    if (value < 1) {
                        return '输入金额需大于或等于1元';
                    }
                }
            });

            layer.confirm('用哪一个客服身份重发？', {
            btn: ['首次发单客服', '当前发单客服'] //可以无限个按钮
            }, function(index, layero){
                $.post('{{ route('frontend.workbench.leveling.create') }}', {data: data.field, value: '1'}, function (result) {

                    if (result.status == 1) {
                        layer.open({
                            content: '发布成功!',
                            btn: ['继续发布', '订单列表'],
                            btn1: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            }
                        });
                    } else {
                        layer.msg(result.message);
                    }

                }, 'json');
                return false;
            }, function(index){
                $.post('{{ route('frontend.workbench.leveling.create') }}', {data: data.field}, function (result) {

                    if (result.status == 1) {
                        layer.open({
                            content: '发布成功!',
                            btn: ['继续发布', '订单列表'],
                            btn1: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(index, layero){
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

    });
</script>
@endsection
