@extends('frontend.layouts.app')

@section('title', '工作台 - 代练 - 订单发布')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
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
        <div class="layui-col-md7">
            <div class="site-title">
                <fieldset><legend><a name="hr">订单信息</a></legend></fieldset>
            </div>
            <form class="layui-form" action="" id="form-order">
                <div class="layui-row form-group">
                    <div class="layui-col-md6">
                        <div class="layui-col-md3 layui-form-mid">*游戏</div>
                        <div class="layui-col-md8">
                            <select name="game_id" lay-verify="required" lay-search="" show-name="x" display-name="游戏" lay-filter="game">
                                @foreach($game as $key => $value)
                                    <option value="{{ $key }}" @if($gameId == $key) selected  @endif>{{ $value }}</option>
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
                    <div class="layui-btn layui-btn-normal layui-col-md12" lay-submit="" lay-filter="analysis-template" id="parse">解析模版</div>
                </div>
                <div class="layui-col-md4 layui-col-md-offset4">
                    <div class="layui-btn layui-btn-normal layui-col-md12" lay-submit="" lay-filter="instructions">使用说明</div>
                </div>
            </div>
            <div class="">
                <div class="layui-row form-group">
                    <textarea name="desc" class="layui-textarea" style="min-height: 800px" id="user-template"></textarea>
                </div>
            </div>
        </div>

        <div class="layui-col-md2">
            <div class="site-title">
                <fieldset><legend><a name="hr">订单来源</a></legend></fieldset>
            </div>

            <div class="layui-row form-group">
                <div class="layui-col-md5 text_right">店铺名：</div>
                <div class="layui-col-md7">{{ $taobaoTrade->seller_nick or '' }}</div>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md5 text_right">天猫单号：</div>
                <div class="layui-col-md7">{{ $taobaoTrade->tid or '' }}</div>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md5 text_right">买家旺旺：</div>
                <div class="layui-col-md7">
                    @if(!is_null($taobaoTrade) && $taobaoTrade->buyer_nick)
                        <a style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid={{ $taobaoTrade->buyer_nick}}&siteid=cntaobao&status=1&charset=utf-8"
                           class="btn btn-save buyer" target="_blank"><img src="/frontend/images/ww.gif" width="20px"> {{ $taobaoTrade->buyer_nick }}
                        </a>
                    @else
                    @endif
                </div>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md5 text_right">购买单价：</div>
                <div class="layui-col-md7">{{ $taobaoTrade->price or '' }}</div>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md5 text_right">购买数量：</div>
                <div class="layui-col-md7">{{ $taobaoTrade->num or '' }}</div>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md5 text_right">实付金额：</div>
                <div class="layui-col-md7">{{ $taobaoTrade->payment or '' }}</div>
            </div>
            <div class="layui-row form-group">
                <div class="layui-col-md5 text_right">下单时间：</div>
                <div class="layui-col-md7">{{ $taobaoTrade->created or '' }}</div>
            </div>

        </div>
    </div>
@endsection

<!--START 底部-->
@section('js')
    <script id="goodsTemplate" type="text/html">
        <input type="hidden" name="id" value="@{{ d.id }}">
        <input type="hidden" name="seller_nick" value="">
        <input type="hidden" name="pre_sale" value="" display-name="接单客服">
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

                    <input type="text" name="@{{ item.field_name }}"  autocomplete="off" class="layui-input" lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}|@{{ item.verify_rule }}" display-name="@{{item.field_display_name}}" value="@{{# if (item.field_default_value != "null") { item.field_default_value  } }}">

                    @{{# } }}

                    @{{# if(item.field_type == 2) {  }}


                        <select name="@{{ item.field_name }}"  lay-search="" lay-verify="@{{# if (item.field_required == 1) { }}required@{{# } }}"  display-name="@{{item.field_display_name}}"  lay-filter="change-select" data-id="@{{ item.id }}" id="select-parent-@{{ item.field_parent_id }}">
                            <option value=""></option>
                            @{{#  if(item.user_values.length > 0){ }}
                            @{{#  layui.each(item.user_values, function(i, v){ }}
                            <option value="@{{ v.field_value }}" data-id="@{{ v.id  }}">@{{ v.field_value }}</option>
                            @{{#  }); }}
                            @{{#  } else { }}
                            @{{#  if(item.values.length > 0){ }}
                            @{{#  layui.each(item.values, function(i, v){ }}
                            <option value="@{{ v.field_value }}" data-id="@{{ v.id  }}">@{{ v.field_value }}</option>
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

                    @{{# if(item.help_text != '无') {  }}
                    <a href="#" class="tooltip">
                        <i class="iconfont icon-iconset0143" id="recharge"></i>
                        <span>@{{ item.help_text }}</span>
                    </a>
                    @{{# }  }}

                    @{{# if(item.field_name == 'game_leveling_requirements_template') {  }}
                    <a href="#" class="tooltip" id="game_leveling_requirements_template">
                        <i class="iconfont icon-plus-bd" id="recharge"></i>
                    </a>
                    @{{# }  }}

                    @{{# if(item.field_name == 'user_phone') {  }}
                    <a href="#" class="tooltip" id="user_phone">
                        <i class="iconfont icon-plus-bd" id="recharge"></i>
                    </a>
                    @{{# }  }}

                    @{{# if(item.field_name == 'user_qq') {  }}
                    <a href="#" class="tooltip" id="user_qq">
                        <i class="iconfont icon-plus-bd" id="recharge"></i>
                    </a>
                    @{{# }  }}

                </div>
            </div>

            @{{#  row--; }}

            @{{# if(row == 0) { }}
        </div>
        <div class="layui-row form-group @{{ item.field_display_name  }}">
            @{{# }  }}

            @{{# })  }}
        </div>

    </script>
    <script>
        layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
            var gameId = '{{ $gameId }}';
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
                    if (value < 5) {
                        return '输入金额需大于或等于5元';
                    }
                }

            });
            // 模板使用说明
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

                if(data.field.game_leveling_day == 0 && data.field.game_leveling_hour == 0) {
                    layer.msg('代练时间不能都为0');
                    return false;
                }

                if(data.field.game_leveling_hour > 24) {
                    layer.msg('代练小时不能大于24小时');
                    return false;
                }

                var load = layer.load(0, {
                    shade: [0.2, '#000000']
                });

                $.post('{{ route('frontend.workbench.leveling.create') }}', {data: data.field}, function (result) {

                    if (result.status == 1) {
                        layer.open({
                            content: result.message,
                            btn: ['继续发布', '订单列表', '待发订单'],
                            btn1: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            },
                            btn3: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            }
                        });
                    } else {
                        layer.open({
                            content: result.message,
                            btn: ['继续发布', '订单列表', '待发订单'],
                            btn1: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            },
                            btn3: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            }
                        });
                        // layer.msg(result.message);
                    }
                    layer.close(load);

                }, 'json');
                return false;
            });
            // 切换游戏时加截新的模版
            form.on('select(game)', function (data) {
                gameId = data.value;
                loadTemplate(gameId)
            });
            // 模版预览 下拉框值
            form.on('select(change-select)', function(data){
                var subordinate = "#select-parent-" + data.elem.getAttribute('data-id');
                var choseId = $(data.elem).find("option:selected").attr("data-id");
                if($(subordinate).length > 0){
                    $.post('{{ route('frontend.workbench.get-select-child') }}', {parent_id:choseId}, function (result) {
                        $(subordinate).html(result);
                        $(result).each(function (index, value) {
                            $(subordinate).append('<option value="' + value.field_value + '">' + value.field_value + '</option>');
                        });
                        layui.form.render();
                    }, 'json');
                }
                return false;
            });
            // 加载默认模板
            loadTemplate(gameId);
            // 加载模板
            function loadTemplate(id) {
                var getTpl = goodsTemplate.innerHTML, view = $('#template');
                $.post('{{ route('frontend.workbench.leveling.get-template') }}', {game_id:id, tid:'{{ $tid  }}'}, function (result) {
                    var template;
                    if (result.content.sellerMemo) {

                        var temp  = result.content.sellerMemo  + '\r\n';
                        // 替换所有半角除号为全角
                        template = temp.replace(/:/g, '：');
                        template += '商户电话：'+ result.content.businessmanInfoMemo.phone  + '\r\n';
                        template += '商户QQ：'+ result.content.businessmanInfoMemo.qq  + '\r\n';
                        @if(isset($taobaoTrade->tid))

                            try {
                                template = template.replace(/(?<=\u53f7\u4e3b\u65fa\u65fa\uff1a).*\b/, '{{ $taobaoTrade->buyer_nick }}');
                                template = template.replace(/(?<=\u6765\u6e90\u4ef7\u683c\uff1a).*\b/, '{{ $taobaoTrade->payment }}');
                                template = template.replace(/(?<=\u6765\u6e90\u8ba2\u5355\u53f7\uff1a).*\b/, '{{ $taobaoTrade->tid }}');
                                template = template.replace(/(?<=\u8ba2\u5355\u6765\u6e90\uff1a).*\b/, '天猫');
                            } catch(err){
                                $('input[name=source_order_no]').val({{ $taobaoTrade->tid }});
                                $('input[name=order_source]').val('天猫');
                                $('input[name=source_price]').val('{{ $taobaoTrade->payment }}');
                                $('input[name=client_wang_wang]').val('{{ $taobaoTrade->buyer_nick }}');
                            }
                            if (template.indexOf('订单来源') == -1) {
                                template += '订单来源：天猫'  + '\r\n';
                            }
                            if (template.indexOf('来源订单号') == -1) {
                                template += '来源订单号：{{ $taobaoTrade->tid }}'  + '\r\n';
                            }
                            if (template.indexOf('来源价格') == -1) {
                                template += '来源价格：{{ $taobaoTrade->payment }}'  + '\r\n';
                            }
                            if (template.indexOf('号主旺旺') == -1) {
                                template += '号主旺旺：{{ $taobaoTrade->buyer_nick }}'  + '\r\n';
                            }
                        @endif
                        $('#user-template').val(template);
                    } else {
                        template = '游戏：\r\n';
                        $.each(result.content.template, function(index,element){
                            if (element.field_display_name != '商户电话' || element.field_display_name != '商户QQ') {
                                template += element.field_display_name + '：\r\n'
                            }
                        });
                        @if(isset($taobaoTrade->tid))

                        try {
                            $('input[name=source_order_no]').val('{{ $taobaoTrade->tid }}');
                            $('input[name=order_source]').val('天猫');
                            $('input[name=source_price]').val('{{ $taobaoTrade->payment }}');
                            $('input[name=client_wang_wang]').val('{{ $taobaoTrade->buyer_nick }}');
                        } catch(err){

                        }
                        @endif
                        $('#user-template').val(template);
                    }
                    layTpl(getTpl).render(result.content, function(html){
                        view.html(html);
                        layui.form.render();
                    });
                    setDefaultValueOption();
                    loadGameLevelingTemplate(id);
                    loadBusinessmanContactTemplate(id);
                    analysis()
                }, 'json');
            }
            // 解析模板
            $('#parse').click(function () {
                analysis();
            });
            function analysis() {

                var fieldArrs = $('[name="desc"]').val().split('\n');

                for (var i = fieldArrs.length - 1; i >= 0; i--) {
                    var arr = fieldArrs[i].split('：');

                    // 跳过格式不对的行或空行
                    if (typeof arr[1] == "undefined") {
                        continue;
                    }

                    // 去两端空格
                    var name = $.trim(arr[0]);
                    var value = $.trim(arr[1]);

                    // 获取表单dom
                    var $formDom = $('#form-order').find('[display-name="' + name + '"]');

                    // 填充表单
                    switch ($formDom.prop('type')) {
                        case 'select-one':
                            $formDom.find('option').each(function () {
                                if ($(this).text() == value && $(this).text() != '') {
                                    $formDom.val($(this).val());
                                    return false;
                                }
                            });
                            break;
                        case 'checkbox':
                            if (value == 1) {
                                $formDom.prop('checked', true);
                            } else {
                                $formDom.prop('checked', false);
                            }
                            break;
                        default:
                            if (value != '') {
                                $formDom.val(value);
                            }
                            break;
                    }
                }
                layui.form.render();
            }
            // 设置默认选中填充的值
            function setDefaultValueOption() {
                $("select[name=game_leveling_type]").val('排位');
                $('select[name=user_phone]').val('{{ $businessmanInfo->phone }}');
                $('select[name=user_qq]').val('{{ $businessmanInfo->qq }}');
                @if(isset($taobaoTrade->tid))
                    $('input[name=source_order_no]').val('{{ $taobaoTrade->tid }}');
                    $('input[name=order_source]').val('天猫');
                    $('input[name=source_price]').val('{{ $taobaoTrade->payment }}');
                    $('input[name=client_wang_wang]').val('{{ $taobaoTrade->buyer_nick }}');
                    $('input[name=seller_nick]').val('{{ $taobaoTrade->seller_nick }}');
                @endif
                layui.form.render();
            }
            // 加载代练要求模板
            function loadGameLevelingTemplate(gameId) {
                $.post('{{ route("frontend.workbench.leveling.game-leveling-template") }}', {game_id:gameId}, function (result) {
                    var optionsHtml = '<option value="">请选择模板</option>';
                    $.each(result, function (index, value) {
                        if (value.status  == 1) {
                            optionsHtml += '<option value="'  + value.content + '" data-content="' + value.content +  ' "  selected> ' + value.name  + '</option>';
                            $('textarea[name=game_leveling_requirements]').val(value.content);
                        } else {
                            optionsHtml += '<option value="'  + value.content + '" data-content="' + value.content +  '"> ' + value.name  +'</option>';
                        }
                    });
                    $('select[name=game_leveling_requirements_template]').html(optionsHtml);
                    layui.form.render();
                }, 'json');
            }
            // 加载代练要求模板
            function loadBusinessmanContactTemplate() {
                $.get('{{ route("frontend.setting.setting.businessman-contact.index") }}', {id:gameId}, function (result) {
                    var qqTemplate = '<option value="">请选择</option>';
                    var phoneTemplate = '<option value="">请选择</option>';
                    $.each(result, function (index, value) {
                        var chose = 0;
                        if (value.type == 1 && (value.game_id == 0 || gameId == value.game_id)) {

                            if (value.status == 1 && value.game_id == 0 && chose == 0) {
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected> ' + value.name + '-' + value.content  +'</option>';
                            } else if (gameId == value.game_id && value.status == 1) {
                                chose = 1;
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected> ' + value.name + '-' + value.content  +'</option>';
                            } else {
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '"> ' + value.name + '-' + value.content  +'</option>';
                            }

                        } else if (value.type == 2 && (value.game_id == 0 || gameId == value.game_id)) {

                            if (gameId == value.game_id && value.status == 1) {
                                chose = 1;
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected>' + value.name + '-' + value.content  +'</option>';
                            } else if (value.status == 1 && value.game_id == 0 && chose == 0) {
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected>' + value.name + '-' + value.content  +'</option>';
                            } else {
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" >' + value.name + '-' + value.content  +'</option>';
                            }
                        }
                        chose = 0;
                    });
                    $('select[name=user_qq]').html(qqTemplate);
                    $('select[name=user_phone]').html(phoneTemplate);
                    layui.form.render();
                }, 'json');
            }

            form.on('select', function(data){
                var fieldName = $(data.elem).attr("name"); //得到被选中的值
                // 选择要求模后自动填充模板内容
                if (fieldName == 'game_leveling_requirements_template') {
                    $('textarea[name=game_leveling_requirements]').val(data.value);
                }
            });
            // 添加代练要求模板
            $('.layui-form').on('click', '#game_leveling_requirements_template', function () {
                layer.open({
                    type: 2,
                    area: ['700px', '400px'],
                    content: '{{ route('frontend.setting.sending-assist.require.pop') }}',
                    cancel: function(index, layero){
                        loadGameLevelingTemplate(gameId);
                    }
                });
            });
            // 添加商户电话模版
            $('.layui-form').on('click', '#user_phone', function () {
                layer.open({
                    type: 2,
                    area: ['700px', '400px'],
                    content: '{{ route('frontend.setting.setting.businessman-contact.index', ['type' => 1]) }}',
                    cancel: function(index, layero){
                        loadBusinessmanContactTemplate(gameId);
                    }
                });
            });
            // 添加商户QQ模版
            $('.layui-form').on('click', '#user_qq', function () {
                layer.open({
                    type: 2,
                    area: ['700px', '400px'],
                    content: '{{ route('frontend.setting.setting.businessman-contact.index', ['type' => 2]) }}',
                    cancel: function(index, layero){
                        loadBusinessmanContactTemplate(gameId);
                    }
                });
            });
        });
    </script>
@endsection
