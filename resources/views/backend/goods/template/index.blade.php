@extends('backend.layouts.main')

@section('title', ' | 商品模版')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商品模版</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <div class="filter-block pull-left">
                        <form class="form-inline" role="form">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name"  placeholder="输入模版名称" value="{{ $name }}">
                            </div>
                            <button type="submit" class="btn btn-success">搜索</button>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <button  class="btn btn-primary pull-right" id="add-goods-template">
                            <i class="glyphicon glyphicon-plus"></i> 添加模版
                        </button>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>服务</th>
                                <th>游戏</th>
                                <th>添加人</th>
                                <th>更新人</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th>状态</th>
                                <th width="14%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($goodsTemplates as $item)
                                <tr class="{{ $item->status == 0 ? 'layui-bg-red' : '' }}">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->service->name ?? '' }}</td>
                                    <td>{{ $item->game->name ?? '' }}</td>
                                    <td>{{ $item->createdAdmin->name ?? '无' }}</td>
                                    <td>{{ $item->updatedAdmin->name ?? '无' }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->updated_at }}</td>
                                    <td>{{ $item->status == 1 ? '已启用' : '未启用' }}</td>
                                    <td>
                                        @if($item->status == 0)
                                            <button class="layui-btn layui-btn-mini layui-btn-normal" lay-submit="" lay-filter="change-status" data-id="{{ $item->id }}" data-status="1">启用</button>
                                        @else
                                            <button class="layui-btn layui-btn-mini layui-btn-danger" lay-submit="" lay-filter="change-status" data-id="{{ $item->id }}"  data-status="0">禁用</button>
                                        @endif
                                        <button data-route="{{ route('goods.template.show', ['id' => $item->id])  }}" class="layui-btn layui-btn-normal layui-btn-mini" lay-submit="" lay-filter="edit">编缉</button>
                                        <a href="{{ route('goods.template.config', ['id' => $item->id])  }}" class="layui-btn layui-btn-normal layui-btn-mini">配置</a>
                                        <button data-id="{{ $item->id }}" class="layui-btn layui-btn-normal layui-btn-mini" lay-submit="" lay-filter="copy">复制</button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        {{ $goodsTemplates->appends(['name' => $name])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="add-template" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">

            <div class="layui-form-item">
                <label class="layui-form-label">服务</label>
                <div class="layui-input-inline">
                    <select name="service_id" lay-verify="required">
                        @forelse($services as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">游戏</label>
                <div class="layui-input-inline">
                    <select name="game_id" lay-verify="required">
                        @forelse($games as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="save-template">确定添加</button>
            </div>
        </form>
    </div>

    <div id="show-template" style="display: none;padding: 20px">

    </div>

@endsection

@section('js')
<script id="showTemplate" type="text/html">
    <form class="layui-form layui-form-pane" action="">
        <input type="hidden" name="id" value="@{{ d.id }}">
        <div class="layui-form-item">
            <label class="layui-form-label">服务</label>
            <div class="layui-input-inline">
                <select name="service_id" lay-verify="required">
                    @{{#  layui.each(d.services, function(i, v){ }}
                        @{{#  if(d.service_id == i){ }}
                            <option value="@{{ i  }}" selected>@{{ v  }}</option>
                        @{{#  } else { }}
                            <option value="@{{ i  }}">@{{ v  }}</option>
                        @{{#  } }}
                    @{{#  }); }}
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">游戏</label>
            <div class="layui-input-inline">
                <select name="game_id" lay-verify="required">

                    @{{#  layui.each(d.games, function(i, v){ }}
                        @{{#  if(d.game_id == i){ }}
                        <option value="@{{ i  }}" selected>@{{ v  }}</option>
                        @{{#  } else { }}
                        <option value="@{{ i  }}">@{{ v  }}</option>
                        @{{# } }}
                    @{{#  }); }}
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="save-edit">确定修改</button>
        </div>
    </form>
</script>
<script>
    layui.use(['form', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
            //监听提交
            form.on('submit(save-template)', function(data){
                $.post('{{ route('goods.template.store') }}', {data:data.field}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            //修改状态
            form.on('submit(change-status)', function(data){
                $.post('{{ route('goods.template.status') }}', {id:data.elem.getAttribute('data-id'), status:data.elem.getAttribute('data-status')}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            //修改模版
            form.on('submit(edit)', function(data){
                var getTpl = showTemplate.innerHTML, view = document.getElementById('show-template');
                $.get(data.elem.getAttribute('data-route'),function (result) {
                    layTpl(getTpl).render(result, function(html){
                        view.innerHTML = html;
                        layui.form.render()
                    });
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '添加商品模版',
                        content: $('#show-template')
                    });
                }, 'json');
                return false;
            });
            // 提交修改
            form.on('submit(save-edit)', function(data){
                $.post('{{ route('goods.template.edit') }}', {id:data.field.id,data:data.field}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            // 复制模模
            form.on('submit(copy)', function (data) {
                layer.confirm('你确定要复制这个模版吗？', function (index) {
                    $.post('{{ route('goods.template.copy-template') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                    }, 'json');
                });
                layer.close(index);
                return false;
            });

            // 添加商品模版
            $('#add-goods-template').on('click', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加商品模版',
                    content: $('.add-template')
                });
            });
        });
</script>
@endsection