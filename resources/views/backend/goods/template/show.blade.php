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
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <form class="layui-form layui-form-pane" action="">
                                    <input type="hidden" name="goods_template_id" value="{{ Route::input('templateId')}}">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">组件类型</label>
                                        <div class="layui-input-block">
                                            <select name="filed_name" lay-verify="required">
                                                <option value=""></option>
                                                @foreach($filedName as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">展示方式</label>
                                        <div class="layui-input-block"  >
                                            @foreach($filedType as $key => $value)
                                                <input type="radio" name="filed_type" value="{{ $key }}" title="{{ $value }}" lay-filter="fieldType">
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">排序</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="filed_sort" autocomplete="off" placeholder="请输序号" class="layui-input"  lay-verify="required">
                                        </div>
                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">显示名称</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="filed_display_name" autocomplete="off" placeholder="请输入名称" class="layui-input"  lay-verify="required">
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">组件默认值</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="filed_default_value" autocomplete="off" placeholder="当类型为【下拉选】时，此项无效" class="layui-input">
                                        </div>
                                    </div>

                                    <div class="layui-form-item layui-form-text">
                                        <label class="layui-form-label">组件可选值（多个值之前用 | 分隔，多组之前用 ，分隔）</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入内容" class="layui-textarea" name="filed_value" lay-verify="filed_value"></textarea>
                                        </div>
                                    </div>

                                    <div class="layui-form-item" pane="">
                                        <label class="layui-form-label">是否必填</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox"  name="filed_required" lay-skin="switch"  title="开关" value="1">
                                        </div>
                                    </div>
                                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="addWidget">确认添加</button>
                                </form>
                            </div>
                            <div class="layui-tab-item" >
                                <form class="layui-form layui-form-pane" action="" id="show-widget">

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script id="goodsTemplate" type="text/html">
        @{{#  layui.each(d, function(index, item){ }}

            <!--输入框-->
            @{{#  if(item.filed_type === 1){ }}
                <div class="layui-form-item">
                    <label class="layui-form-label">@{{ item.filed_display_name }}</label>
                    <div class="layui-input-inline">
                        @{{#  if(item.filed_name == 'password'){ }}
                            <input type="password" name="@{{ item.filed_name }}"  placeholder="请输入@{{ item.filed_display_name }}" autocomplete="off" class="layui-input" value="@{{ item.filed_default_value }}">
                        @{{#  } else { }}
                            <input type="text" name="@{{ item.filed_name }}"  placeholder="请输入@{{ item.filed_display_name }}" autocomplete="off" class="layui-input" value="@{{ item.filed_default_value }}">
                        @{{#  } }}
                    </div>
                    <button class="layui-btn layui-btn-normal layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="showWidget"><i class="layui-icon">&#xe642;</i> 编辑</button>
                    <button class="layui-btn layui-btn-normal layui-btn-danger layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="destroyWidget"><i class="layui-icon">&#xe640;</i> 删除</button>
                </div>
            @{{#  } }}

            <!--下拉项-->
            @{{#  if(item.filed_type === 2){ }}
                <div class="layui-form-item">
                    <label class="layui-form-label">@{{ item.filed_display_name }}</label>
                    <div class="layui-input-inline">
                        <select name="@{{ item.filed_name }}">
                            @{{# var option = (item.filed_value).split("|") }}
                            @{{#  layui.each(option, function(i, v){ }}
                                <option value="@{{ v }}">@{{ v }}</option>
                            @{{#  }); }}
                        </select>
                    </div>
                    <button class="layui-btn layui-btn-normal layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="showWidget"><i class="layui-icon">&#xe642;</i> 编辑</button>
                    <button class="layui-btn layui-btn-normal layui-btn-danger layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="destroyWidget"><i class="layui-icon">&#xe640;</i> 删除</button>
                </div>
            @{{#  } }}

            <!--单选项-->
            @{{#  if(item.filed_type === 3){ }}
                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">@{{ item.filed_display_name }}</label>
                    <div class="layui-input-block" >
                        @{{# var option = (item.filed_value).split("|") }}
                        @{{#  layui.each(option, function(i, v){ }}
                            @{{#  if(item.filed_default_value ==  v ){  }}
                                <input type="radio" name="filed_type" value="@{{ v }}" title="@{{ v }}" checked="">
                            @{{#  } else { }}
                                <input type="radio" name="filed_type" value="@{{ v }}" title="@{{ v }}">
                             @{{#  } }}

                        @{{#  }); }}
                        <button class="layui-btn layui-btn-normal layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="showWidget"><i class="layui-icon">&#xe642;</i> 编辑</button>
                        <button class="layui-btn layui-btn-normal layui-btn-danger layui-btn-small" data-id="@{{ item.id }}" lay-submit="" lay-filter="destroyWidget"><i class="layui-icon">&#xe640;</i> 删除</button>
                    </div>
                </div>
            @{{#  } }}

        @{{#  }); }}

        @{{#  if(d.length === 0){ }}
            没有组件
        @{{#  } }}
    </script>
    <script id="showWidgetTemplate" type="text/html">

        <div class="layui-form-item" pane="">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="text" name="filed_sort" autocomplete="off" placeholder="请输序号" class="layui-input"  lay-verify="required" value="@{{ d.filed_sort }}">
            </div>
        </div>

        <div class="layui-form-item" pane="">
            <label class="layui-form-label">显示名称</label>
            <div class="layui-input-block">
                <input type="text" name="filed_display_name" autocomplete="off" placeholder="请输入名称" class="layui-input"  lay-verify="required" value="@{{ d.filed_display_name }}">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">组件默认值</label>
            <div class="layui-input-block">
                <input type="text" name="filed_default_value" autocomplete="off" placeholder="当类型为【下拉选】时，此项无效" class="layui-input" value="@{{ d.filed_default_value }}">
            </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">组件可选值（多个值之前用 | 分隔，多组之前用 ，分隔）</label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" class="layui-textarea" name="filed_value">@{{ d.filed_value }}</textarea>
            </div>
        </div>

        <div class="layui-form-item" pane="">
            <label class="layui-form-label">是否必填</label>
            <div class="layui-input-block">
                @{{#  if(d.filed_required ==  1 ){  }}
                    <input type="checkbox"  name="filed_required" lay-skin="switch"  title="开关" value="1" checked="">
                @{{#  } else { }}
                    <input type="checkbox"  name="filed_required" lay-skin="switch"  title="开关" value="1">
                @{{#  } }}
            </div>
        </div>
        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="editWidget">保存修改</button>
    </script>
    <script>
        layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
            reloadTemplate();
            var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
            // 当前选择的组件展示方式
            var currentWidgetType = 1;
            //自定义验证规则
            form.verify({
                filed_value: function(value){
                    if(currentWidgetType > 1 && value.length == 0){
                        return '这种展示方式必须填入可选值呢！';
                    }
                }
            });
            // 监听组件展示方式选择
            form.on('radio(fieldType)', function(data){
                currentWidgetType = data.value;
                layer.msg('开关checked：', {
                    offset: '6px'
                });
            });
            // 添加表单组件
            form.on('submit(addWidget)', function(data){
                $.post('{{ route('goods.template.widget.store') }}', {data:data.field},function (result) {
                    // 添加成功后重新加载模版
                    if (result.code == 1) {
                        reloadTemplate();
                    }
                    layer.alert(result.message, {
                        title: '添加结果'
                    });
                }, 'json');
                return false;
            });
            // 删除表单组件
            form.on('submit(destroyWidget)', function(data){
                destroyWidget(data.elem.getAttribute("data-id"));
                return false;
            });
            // 编缉表单组件
            form.on('submit(showWidget)', function(data){
                showWidget(data.elem.getAttribute("data-id"));
                return false;
            });
            // 保存组件修改
            form.on('submit(editWidget)', function(data){
                $.post('{{ route('goods.template.widget.edit') }}', {id:data.field.id, data:data.field}, function (result) {

                }, 'json');
                return false;
            });
            // 当切换到添加时清空编辑表单
            element.on('tab(widgetTab)', function(data){
                if (data.index == 0) {
                    $('#show-widget').html('');
                }
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
            // 删除组件
            function destroyWidget(widgetId) {
                layer.confirm('您确认要删除该组件吗？', function (index) {
                    $.post('{{ route('goods.template.widget.destroy') }}', {id:widgetId}, function (result) {
                        reloadTemplate();
                    }, 'json');
                    layer.close(index);
                });
            }
            // 编辑组件
            function showWidget(widgetId) {
                element.tabChange('widgetTab', 'edit');
                var getTpl = showWidgetTemplate.innerHTML, view = document.getElementById('show-widget');
                $.post('{{ route('goods.template.widget.show') }}', {id:widgetId}, function (result) {
                    layTpl(getTpl).render(result, function(html){
                        view.innerHTML = html;
                        layui.form.render()
                    });
                }, 'json');
            }
        });
    </script>
@endsection