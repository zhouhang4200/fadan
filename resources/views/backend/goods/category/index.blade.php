@extends('backend.layouts.main')

@section('title', ' | 商品类目')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商品类目</span></li>
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
                        @if(Route::input('id'))
                            <a href="{{ route('goods.category-index') }}" class="layui-btn layui-btn-samll layui-btn-normal">返回上级</a>
                        @endif
                        <button class="layui-btn layui-btn-samll layui-btn-normal" id="add-goods-category">添加类目</button>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>类目ID</th>
                                <th>上级类目</th>
                                <th>类目名称</th>
                                <th>状态</th>
                                <th>添加人员</th>
                                <th>最后修改人员</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th width="15%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($categories as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->parent->name ?? '无' }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->status == 0 ? '没启用' : '已启用' }}</td>
                                    <td>{{ $item->createdAdmin->name }}</td>
                                    <td>{{ $item->updatedAdmin->name ?? '无' }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        @if($item->status == 0)
                                            <button class="layui-btn layui-btn-mini layui-btn-normal" lay-submit="" lay-filter="change-status" data-id="{{ $item->id }}" data-status="1">启用</button>
                                        @else
                                            <button class="layui-btn layui-btn-mini layui-btn-danger" lay-submit="" lay-filter="change-status" data-id="{{ $item->id }}"  data-status="0">禁用</button>
                                        @endif
                                        <a href="{{ route('goods.category-index', ['id' => $item->id]) }}" class="layui-btn layui-btn-mini layui-btn-normal">子类</a>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        {{ $categories->appends(['name' => $name])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="add-category" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <input type="hidden" name="parent_id" value="{{ Route::input('id') ?? 0 }}">
            <div class="layui-form-item">
                <label class="layui-form-label">关联模版</label>
                <div class="layui-input-inline">
                    <select name="goods_template_id" lay-verify="" lay-search>
                        <option value="0">无</option>
                        @forelse($goodsTemplates as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">类目名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="required" placeholder="请输入类目名称" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">显示排序</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="required" placeholder="请输入显示排序" autocomplete="off" class="layui-input" value="999">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="save-category">确定添加</button>
            </div>
        </form>
    </div>

    <div class="edit-category-box">

    </div>

@endsection

@section('js')
<script id="editGoodsCategory" type="text/html">
    <div class="layui-form-item">
        <label class="layui-form-label">父级组件</label>
        <div class="layui-input-block">
            <select name="parent_id" lay-verify="required">
                <option value="0">无</option>
                @{{#  if(d.length > 0){ }}
                @{{#  layui.each(d, function(i, v){ }}
                <option value="@{{ v.id }}">@{{ v.field_display_name }}</option>
                @{{#  }); }}
                @{{#  } }}
            </select>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">关联模版</label>
            <div class="layui-input-inline">
                <select name="goods_template_id" lay-verify="" lay-search>
                    <option value="0">无</option>
                    @forelse($goodsTemplates as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">类目名称</label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verify="required" placeholder="请输入类目名称" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">显示排序</label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verify="required" placeholder="请输入显示排序" autocomplete="off" class="layui-input" value="999">
            </div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="save-category">确定添加</button>
        </div>
    </div>
</script>
<script>
        layui.use('form', function(){
            var form = layui.form;
            //监听提交
            form.on('submit(save-category)', function(data){
                $.post('{{ route('goods.category-store') }}', {data:data.field}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            //监听修改状态
            form.on('submit(change-status)', function(data){
                $.post('{{ route('goods.category-status') }}', {id:data.elem.getAttribute('data-id'), status:data.elem.getAttribute('data-status')}, function (result) {
                    layer.msg(result.message);

                }, 'json');
                reload();
                return false;
            });

            // 添加商品模版
            $('#add-goods-category').on('click', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加商品类目',
                    content: $('.add-category')
                });
            });
        });
    </script>
@endsection