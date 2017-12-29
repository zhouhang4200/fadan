@extends('backend.layouts.main')

@section('title', '| 模版配置')

@section('css')
    <style>
        .layui-tab {
            margin: 0;
        }
        .layui-tab-content {
            padding: 25px;
        }
        .layui-tab-content{
            padding: 25px 0;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>模版配置</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">模版预览</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <form class="layui-form layui-form-pane" id="goods-template">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">组件添加</li>
                            <li lay-id="edit">组件编缉</li>
                            <li lay-id="edit-option">组件选项编缉</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <form class="layui-form layui-form-pane"   name="create-form">
                                    <input type="hidden" name="goods_template_id" value="{{ Route::input('templateId')}}">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">组件类型</label>
                                        <div class="layui-input-block">
                                            <select name="field_type_and_name" lay-verify="required" lay-filter="widget-type" lay-search="">
                                                <option value=""></option>
                                                @foreach($filedName as  $value)
                                                    <option value="{{ $value->name }}-{{ $value->type }}" data-type="{{ $value->type }}">{{ $value->display_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="show-select-widget">

                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">显示名称</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="field_display_name" autocomplete="off" placeholder="请输入名称" class="layui-input"  lay-verify="required">
                                        </div>
                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">显示方式</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="display_form" autocomplete="off" placeholder="显示方式(如果写1则达表该组件独占一行，2 则表示两个组件一行)" class="layui-input"  lay-verify="required">
                                        </div>
                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">提示信息</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="help_text" autocomplete="off" placeholder="请输入提示信息(可不填写)" class="layui-input"  lay-verify="">
                                        </div>
                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">排序</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="field_sortord" autocomplete="off" placeholder="请输序号" class="layui-input"  lay-verify="required">
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">组件默认值</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="field_default_value" autocomplete="off" placeholder="当类型为【下拉选】时，此项无效" class="layui-input">
                                        </div>
                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">是否必填</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox"  name="field_required" lay-skin="switch"  title="开关" value="1">
                                        </div>
                                    </div>

                                    <div class="widget-value">
                                        <div class="layui-form-item layui-form-text">
                                            <label class="layui-form-label">组件可选值（多个值之间用 | 分隔 ）</label>
                                            <div class="layui-input-block">
                                                <textarea placeholder="请输入内容" class="layui-textarea" name="field_value" lay-verify="field_value"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="add-widget">确认添加</button>
                                </form>
                            </div>
                            <div class="layui-tab-item">
                                <form class="layui-form layui-form-pane" action=""   name="edit-form">
                                </form>
                            </div>
                            <div class="layui-tab-item">
                                <form class="layui-form layui-form-pane" action=""   name="edit-option-form"  id="nestable">

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="add-option" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">组件值 （多个值之前用 | 分隔 ）</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" class="layui-textarea" name="value"  lay-verify="field_value"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="add-option">确定添加</button>
            </div>
        </form>
    </div>

@endsection

@section('js')
<script src="/backend/js/jquery.nestable.js"></script>
<!--预览组件 模版-->
<script id="goodsTemplate" type="text/html">
    @{{#  layui.each(d, function(index, item){ }}

        <!--输入框-->
        @{{#  if(item.field_type === 1){ }}
            <div class="layui-form-item">
                <label class="layui-form-label">@{{ item.field_display_name }}</label>
                <div class="layui-input-inline">
                    @{{#  if(item.field_name == 'password'){ }}
                        <input type="password" name="@{{ item.field_name }}"  placeholder="请输入@{{ item.field_display_name }}" autocomplete="off" class="layui-input" value="">
                    @{{#  } else { }}
                        <input type="text" name="@{{ item.field_name }}"  placeholder="请输入@{{ item.field_display_name }}" autocomplete="off" class="layui-input" value="">
                    @{{#  } }}
                </div>
                <button class="layui-btn layui-btn-normal layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="show-widget"><i class="layui-icon">&#xe642;</i> </button>
                <button class="layui-btn layui-btn-normal layui-btn-danger layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="destroy-widget"><i class="layui-icon">&#xe640;</i> </button>
            </div>
        @{{#  } }}

        <!--下拉项-->
        @{{#  if(item.field_type === 2){ }}
            <div class="layui-form-item">
                <label class="layui-form-label">@{{ item.field_display_name }}</label>
                <div class="layui-input-inline">
                    <select name="@{{ item.field_name }}" lay-filter="change-select" data-id="@{{ item.id }}" id="select-parent-@{{ item.field_parent_id }}">
                        {{--@{{#  if(item.field_parent_id  == 0){ }}--}}
                                {{--@{{#  layui.each(item.values, function(i, v){ }}--}}
                                    {{--<option value="@{{ v.id }}">@{{ v.field_value }}</option>--}}
                                {{--@{{#  }); }}--}}
                        {{--@{{#  } else { }}--}}
                                {{--<option value="请选上级">请选上级</option>--}}
                        {{--@{{#  } }}--}}
                    </select>
                </div>
                <button class="layui-btn layui-btn-normal layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="show-widget"><i class="layui-icon">&#xe642;</i></button>
                <button class="layui-btn layui-btn-normal layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="edit-widget-option"><i class="fa fa-plus-square-o"></i></button>
                <button class="layui-btn layui-btn-danger layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="destroy-widget"><i class="layui-icon">&#xe640;</i></button>
            </div>
        @{{#  } }}

        <!--单选项-->
        @{{#  if(item.field_type === 3){ }}
            <div class="layui-form-item" pane="">
                <label class="layui-form-label">@{{ item.field_display_name }}</label>
                <div class="layui-input-block" >
                    @{{# var option = (item.field_value).split("|") }}
                    @{{#  layui.each(option, function(i, v){ }}
                        @{{#  if(item.field_default_value ==  v ){  }}
                            <input type="radio" name="field_type" value="@{{ v }}" title="@{{ v }}" checked="">
                        @{{#  } else { }}
                            <input type="radio" name="field_type" value="@{{ v }}" title="@{{ v }}">
                         @{{#  } }}
                    @{{#  }); }}
                    <button class="layui-btn layui-btn-normal layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="show-widget"><i class="layui-icon">&#xe642;</i></button>
                    <button class="layui-btn layui-btn-normal layui-btn-danger layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="destroy-widget"><i class="layui-icon">&#xe640;</i></button>
                </div>
            </div>
        @{{#  } }}

    @{{#  }); }}

    @{{#  if(d.length === 0){ }}
        没有组件
    @{{#  } }}
</script>
<!--编缉组件 模版-->
<script id="editWidgetTemplate" type="text/html">
    <input type="hidden" name="id" value="@{{ d.id }}">
    {{--<div class="layui-form-item">--}}
        {{--<label class="layui-form-label">组件类型</label>--}}
        {{--<div class="layui-input-block">--}}
            {{--<select name="field_type_and_name" lay-verify="required" lay-filter="widget-type" readonly="readonly">--}}
                {{--<option value=""></option>--}}
                {{--@{{#  layui.each(d.type, function(i, v){ }}--}}
                    {{--@{{# if(d.field_name == v.name){ }}--}}
                        {{--<option value="@{{ v.name }}-@{{ v.type }}" data-type="@{{ v.type }}" selected>@{{ v.display_name }}</option>--}}
                    {{--@{{#  } else { }}--}}
                        {{--<option value="@{{ v.name }}-@{{ v.type }}" data-type="@{{ v.type }}">@{{ v.display_name }}</option>--}}
                    {{--@{{#  } }}--}}
                {{--@{{#  }); }}--}}
            {{--</select>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<div class="show-select-widget">--}}
        {{--@{{#  if(d.field_type === 2 && d.field_parent_id != 0){ }}--}}
        {{--<div class="layui-form-item">--}}
            {{--<label class="layui-form-label">父级组件</label>--}}
            {{--<div class="layui-input-block">--}}
                {{--<select name="field_parent_id"  lay-filter="parent-widget" disabled>--}}
                    {{--<option value="0">无</option>--}}
                    {{--@{{# if(d.select.length >  0){ }}--}}
                    {{--@{{#  layui.each(d.select, function(i, v){ }}--}}
                    {{--@{{# if(d.field_parent_id == v.id){ }}--}}
                    {{--<option value="@{{ v.id }}" selected>@{{ v.field_display_name }}</option>--}}
                    {{--@{{#  } else if (v.id != d.id) { }}--}}
                    {{--<option value="@{{ v.id }}">@{{ v.field_display_name }}</option>--}}
                    {{--@{{#  } }}--}}
                    {{--@{{#  }); }}--}}
                    {{--@{{#  }  }}--}}
                {{--</select>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--@{{#  } }}--}}
    {{--</div>--}}

    <div class="layui-form-item" pane="">
        <label class="layui-form-label">显示名称</label>
        <div class="layui-input-block">
            <input type="text" name="field_display_name" autocomplete="off" placeholder="请输入名称" class="layui-input"  lay-verify="required" value="@{{ d.field_display_name }}">
        </div>
    </div>

    <div class="layui-form-item" pane="">
        <label class="layui-form-label">显示方式</label>
        <div class="layui-input-block">
            <input type="text" name="display_form" autocomplete="off" placeholder="显示方式(如果写1则达表该组件独占一行，2 则表示两个组件一行)" class="layui-input"  lay-verify="required" value="@{{ d.display_form }}">
        </div>
    </div>

    <div class="layui-form-item" pane="">
        <label class="layui-form-label">提示信息</label>
        <div class="layui-input-block">
            <input type="text" name="help_text" autocomplete="off" placeholder="请输入提示信息(可不填写)" class="layui-input"  lay-verify="" value="@{{ d.help_text }}">
        </div>
    </div>

    <div class="layui-form-item" pane="">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block">
            <input type="text" name="field_sortord" autocomplete="off" placeholder="请输序号" class="layui-input"  lay-verify="required" value="@{{ d.field_sortord }}">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">组件默认值</label>
        <div class="layui-input-block">
            <input type="text" name="field_default_value" autocomplete="off" placeholder="当类型为【下拉选】时，此项无效" class="layui-input" value="@{{ d.field_default_value }}">
        </div>
    </div>

    <div class="layui-form-item" pane="">
        <label class="layui-form-label">是否必填</label>
        <div class="layui-input-block">
            @{{#  if(d.field_required ==  1 ){  }}
            <input type="checkbox"  name="field_required" lay-skin="switch"  title="开关" value="1" checked="">
            @{{#  } else { }}
            <input type="checkbox"  name="field_required" lay-skin="switch"  title="开关" value="1">
            @{{#  } }}
        </div>
    </div>

    {{--<div class="widget-value">--}}
        {{--@{{# if(d.value_group.length >  1){ }}--}}
            {{--@{{#  layui.each(d.value_group, function(i, v){ }}--}}
            {{--<div class="layui-form-item layui-form-text">--}}
                {{--<label class="layui-form-label"> @{{ v.parent_name }} （多个值之前用 | 分隔 ）</label>--}}
                {{--<div class="layui-input-block">--}}
                    {{--<textarea placeholder="请输入内容" class="layui-textarea" name="field_value_@{{ v.parent_id  }}">@{{ v.value  }}</textarea>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--@{{#  }); }}--}}
        {{--@{{#  } else if (d.value_group.length == 1) {  }}--}}
        {{--<div class="layui-form-item layui-form-text">--}}
            {{--<label class="layui-form-label">@{{ d.value_group[0].parent_name }} （多个值之前用 | 分隔 ）</label>--}}
            {{--<div class="layui-input-block">--}}
                {{--<textarea placeholder="请输入内容" class="layui-textarea" name="field_value"  lay-verify="field_value">@{{ d.value_group[0].value }}</textarea>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--@{{#  } else {  }}--}}
        {{--<div class="layui-form-item layui-form-text">--}}
            {{--<label class="layui-form-label">组件值 （多个值之前用 | 分隔 ）</label>--}}
            {{--<div class="layui-input-block">--}}
                {{--<textarea placeholder="请输入内容" class="layui-textarea" name="field_value"  lay-verify="field_value"></textarea>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--@{{#  }  }}--}}
    {{--</div>--}}

    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="edit-save">保存修改</button>
</script>
<!--编辑组件选项 模版-->
<script id="editWidgetOptionTemplate" type="text/html">
    @{{# if(d.top == 1){  }}
        <button class="layui-btn layui-btn-normal layui-btn-small add-option" data-id="@{{ d.widget_id }}" data-parent="0">添加选项</button>
    @{{# }  }}
    <input type="hidden" name="widget_id" value="@{{ d.widget_id }}">
    <ol class="dd-list">

        @{{#  layui.each(d.options, function(i, v){ }}

            <li class="dd-item">
                <div class="dd-handle">
                    @{{ v.field_value }}
                    <div class="nested-links">
                        @{{# if(v.child.length > 0 || d.top == 0) { }}
                            <a href="#" class="add-option"  data-id="@{{  d.widget_id }}" data-parent="@{{ v.parent_id  }}">
                                <i class="fa fa-plus-square"></i>
                            </a>
                        @{{# } else {  }}
                            <a href="#" class="nested-link edit-option" data-id="@{{  d.id }}">
                                <i  class="fa fa-pencil"></i>
                            </a>
                            <a href="" class="del-option" data-id="@{{  v.parent_id }}">
                                <i class="fa fa-times"></i>
                            </a>
                        @{{# }   }}
                    </div>
                </div>

                @{{# if(v.child.length > 0) { }}
                    <ol class="dd-list">
                        @{{#  layui.each(v.child, function(a, j){ }}
                            <li class="dd-item" data-id="@{{ j.id}}">
                                <div class="dd-handle">
                                    @{{ j.field_value  }}
                                    <div class="nested-links">
                                        <a href="#" class="nested-link">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#" class="del-option" data-id="@{{  j.id }}">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @{{# })  }}
                    </ol>
                @{{# } }}

            </li>
        @{{# })  }}
    </ol>
</script>

<!--父级组件 模版-->
<script id="parentWidgetTemplate" type="text/html">
    <div class="layui-form-item">
        <label class="layui-form-label">父级组件</label>
        <div class="layui-input-block">
            {{--<select name="field_parent_id" lay-verify="required" lay-filter="field-parent-id">--}}
            <select name="field_parent_id" lay-verify="required" lay-filter="parent-widget">
                <option value="0">无</option>
                @{{#  if(d.length > 0){ }}
                    @{{#  layui.each(d, function(i, v){ }}
                    <option value="@{{ v.id }}">@{{ v.field_display_name }}</option>
                    @{{#  }); }}
                @{{#  } }}
            </select>
        </div>
    </div>
</script>
<!--父级组件值改动时 模版-->
<script id="parentTemplate" type="text/html">
    @{{#  if(d.length > 0){ }}
        @{{#  layui.each(d, function(i, v){ }}
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">@{{ v.parent_name }} （多个值之前用 | 分隔 ）</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" class="layui-textarea" name="field_value_@{{ v.parent_id  }}"  lay-verify="field_value"></textarea>
                </div>
            </div>
        @{{#  }); }}
    @{{#  } else { }}
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">组件值 （多个值之前用 | 分隔 ）</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" class="layui-textarea" name="field_value"  lay-verify="field_value"></textarea>
                </div>
            </div>
    @{{#  } }}
</script>
<script>
    layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
        reloadTemplate();
        var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
        var currentWidgetType = 1;

        //自定义验证规则
        form.verify({
            field_value: function(value){
                if(currentWidgetType > 1 && value.length == 0){
                    return '这种展示方式必须填入可选值呢！';
                }
            }
        });

        // 监听 选择组件类型
        form.on('select(widget-type)', function(data){
           currentWidgetType = $(data.elem).find("option:selected").attr("data-type");

            // 获取当前form
            var currentForm = data.elem.closest('form').name;
            // 为名字赋值
            $('form[name='+ currentForm +']').find('input[name=field_display_name]').val($(data.elem).find("option:selected").text());

            // 当选的添加组件类型是下拉项时展示可选的父级选项
            if (currentWidgetType == 2) {
                var getTpl = parentWidgetTemplate.innerHTML, view = $('form[name='+ currentForm +']').find('.show-select-widget');
                $.post('{{ route('goods.template.widget.show-select-all') }}', {id:'{{ Route::input('templateId')}}'}, function (result) {
                    layTpl(getTpl).render(result, function(html){
                        view.html(html);
                        layui.form.render()
                    });
                }, 'json');
            } else {
                $('.show-select-widget').html('');
            }
        });

        // 添加表单组件
        form.on('submit(add-widget)', function(data){

            $.post('{{ route('goods.template.widget.store') }}', {data:data.field},function (result) {
                // 添加成功后重新加载模版
                if (result.code == 1) {
                    $('#add-widget-form')[0].reset();
                    reloadTemplate();
                }
                layer.alert(result.message, {
                    title: '添加结果'
                });

            }, 'json');
            return false;
        });
        // 编缉组件按钮
        form.on('submit(show-widget)', function(data){
            element.tabChange('widgetTab', 'edit');
            var getTpl = editWidgetTemplate.innerHTML, view = $('form[name="edit-form"]');
            $.post('{{ route('goods.template.widget.show') }}', {id:data.elem.getAttribute("data-id"),template_id:"{{ Route::input('templateId') }}"}, function (result) {
                layTpl(getTpl).render(result, function(html){
                    view.html(html) ;
                    layui.form.render()
                });
            }, 'json');
            return false;
        });
        // 编辑组件选项
        form.on('submit(edit-widget-option)', function (data) {
            element.tabChange('widgetTab', 'edit-option');

            var getTpl = editWidgetOptionTemplate.innerHTML, view = $('form[name="edit-option-form"]');

            $.post('{{ route('goods.template.widget.edit-option') }}', {id:data.elem.getAttribute("data-id")}, function (result) {
                layTpl(getTpl).render(result.content, function(html){
                    view.html(html) ;
                    $('#nestable').nestable();
                    $('.dd').nestable('collapseAll');
                    layui.form.render()
                });
            }, 'json');


            return false;
        });

        // 删除组件按钮
        form.on('submit(destroy-widget)', function(data){
            layer.confirm('您确认要删除该组件吗？', function (index) {
                $.post('{{ route('goods.template.widget.destroy') }}', {id:data.elem.getAttribute("data-id")}, function (result) {
                    if (result.status == 1) {
                        reloadTemplate();
                    } else {
                        layer.msg(result.message);
                    }
                }, 'json');
                layer.close(index);
            });
            return false;
        });

        // 保存组件编缉
        form.on('submit(edit-save)', function(data){
            $.post('{{ route('goods.template.widget.edit') }}', {id:data.field.id, data:data.field}, function (result) {
                layer.msg(result.message);
                element.tabChange('widgetTab', 'add');
                reloadTemplate();
            }, 'json');
            return false;
        });

        // 当切换到添加时清空编辑表单
        element.on('tab(widgetTab)', function(data){
            if (data.index == 0) {
                $('form[name=edit-form]').empty();
            }
        });

        // 如选择父级组件时则加载出他应对应有项的值输入框
        form.on('select(parent-widget)', function(data) {
            // 获取当前form id
            var currentForm = data.elem.closest('form').name;
            // 找到ID所有的值，生成对应的输入框
            var getTpl = parentTemplate.innerHTML, view = $('form[name='+ currentForm +']').find('.widget-value');
            $.post('{{ route('goods.template.widget.show-select-value') }}', {id:data.value,edit:1},function (result) {
                layTpl(getTpl).render(result.content, function(html){
                    view.html(html);
                    layui.form.render()
                });
            }, 'json');
        });

        // 模版预览 下拉框值
        form.on('select(change-select)', function(data){
            var subordinate = "#select-parent-" + data.elem.getAttribute('data-id');
            if($(subordinate).length > 0){
                $.post('{{ route('goods.template.widget.show-select-child') }}', {parent_id:data.value}, function (result) {
                    $(subordinate).html(result);
                    $(result).each(function (index, value) {
                        $(subordinate).append('<option value="' + value.id + '">' + value.field_value + '</option>');
                    });
                    layui.form.render();
                }, 'json');
            }
            return false;
        });

        // 保存添加选项
        form.on('submit(add-option)', function (data) {
            $.post('{{ route('goods.template.widget.add-option') }}', {id:data.field.id, parent_id:data.field.parent_id, value:data.field.value}, function (result) {
                layer.msg(result.message);
                layer.closeAll();
            }, 'json');
            return false;
        });

        // 编缉选项
        form.on('submit(edit-option)', function (data) {

        });

        // 添加选项弹窗
        $('#nestable').on('click', '.add-option', function () {
            $('#add-option form').append('<input type="hidden"  name="id" value="'  + $(this).attr('data-id')  +  '">');
            $('#add-option form').append('<input type="hidden"   name="parent_id" value="'  + $(this).attr('data-parent')  +  '">');
            layer.open({
                type: 1,
                shade: 0.2,
                title: '添加选项',
                area: ['500px', '300px'],
                content: $('#add-option')
            });
            return false;
        });

        // 编辑选项
        $('#nestable').on('click', '.edit-option', function () {
            $('#edit-option form').append('<input type="hidden"  name="id" value="'  + $(this).attr('data-id')  +  '">');
            layer.open({
                type: 1,
                shade: 0.2,
                title: '编辑',
                area: ['500px', '300px'],
                content: $('#add-option')
            });
            return false;
        });

        // 删除选项
        $('#nestable').on('click', '.del-option', function () {
            var id = $(this).attr("data-id");
            layer.confirm('您确认要删除该选项吗？', function (index) {
                $.post('{{ route('goods.template.widget.del-option') }}', {id:id}, function (result) {
                    reloadTemplate();
                }, 'json');
                layer.close(index);
            });
            return false;
        });

        //重新加载模版
        function reloadTemplate() {
            var getTpl = goodsTemplate.innerHTML, view = document.getElementById('goods-template');
            $.get('{{ route('goods.template.widget.show.all', ['goodsTemplateId' => Route::input('templateId')]) }}',function (result) {
                layTpl(getTpl).render(result, function(html){
                    view.innerHTML = html;
                    layui.form.render()
                });
            }, 'json');
        }
    });

    $('#nestable-menu').on('click', function (e) {
        var target = $(e.target),
                action = target.data('action');
        if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
        }
        if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
        }
    });

</script>
@endsection