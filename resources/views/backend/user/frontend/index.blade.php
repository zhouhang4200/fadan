@extends('backend.layouts.main')

@section('title', ' | 商户账号列表')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商户账号列表</span></li>
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
                                <input type="text" class="form-control" name="name"  placeholder="输入商户名称" value="{{ $name }}">
                            </div>
                            <button type="submit" class="btn btn-success">搜索</button>
                        </form>
                    </div>

                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-tab-item layui-show">
                            <table class="layui-table layui-form" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>账号ID</th>
                                    <th>账号名称</th>
                                    <th>可用余额</th>
                                    <th>冻结余额</th>
                                    <th>注册时间</th>
                                    <th>最后登录时间</th>
                                    <th>实名认证</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->asset->balance ?? 0 }}</td>
                                        <td>{{ $user->asset->frozen ?? 0 }}</td>
                                        <td>{{ $user->created_at }}</td>
                                        <td>{{ $user->updated_at }}</td>
                                        <td>
                                        @if ($user->realNameIdent && $user->realNameIdent->status === 0)
                                            待审核
                                        @elseif ($user->realNameIdent && $user->realNameIdent->status === 1)
                                            审核通过
                                        @elseif ($user->realNameIdent && $user->realNameIdent->status === 2)
                                            审核不通过
                                        @else 
                                            --
                                        @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if (! $user->roles->count() > 0)
                                                <a href="{{ route('groups.create', ['id' => $user->id]) }}" class="layui-btn layui-btn layui-btn-normal layui-btn-mini">添加角色</a>
                                            @else
                                                <a href="{{ route('groups.show', ['id' => $user->id])  }}" class="layui-btn layui-btn layui-btn-normal layui-btn-mini">查看角色</a>
                                            @endif
                                                <button  class="layui-btn layui-btn layui-btn-normal layui-btn-mini" lay-submit lay-filter="recharge-windows" data-id="{{ $user->id }}" data-name="{{ $user->name }}">手动加款</button>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {!! $users->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="recharge" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">

            <div class="layui-form-item">
                <label class="layui-form-label">ID</label>
                <div class="layui-input-block">
                    <input type="text" name="id" autocomplete="off" class="layui-input layui-disabled" readonly value="">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">商户名</label>
                <div class="layui-input-block">
                    <input type="text" name="name" autocomplete="off" class="layui-input layui-disabled" readonly value="">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">金额</label>
                <div class="layui-input-block">
                    <input type="text" name="amount" autocomplete="off" placeholder="请输入加款金额" class="layui-input" lay-verify="required|number">
                </div>
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="recharge">确定</button>
            </div>
        </form>
    </div>
@endsection

@section('js')

    <script>
        //Demo
        layui.use(['form', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer;

            // 手动加款按钮
            form.on('submit(recharge-windows)', function(data){
                $('input[name=id]').val(data.elem.getAttribute('data-id'));
                $('input[name=name]').val(data.elem.getAttribute('data-name'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '手动加款',
                    content: $('#recharge')
                });
                return false;
            });
            form.on('submit(recharge)', function(data){
                layer.confirm('您确认给用户ID为: ' + data.field.id  +' 的商户加款' + data.field.amount + ' 元吗？', {icon: 3, title:'提示'}, function(index){
                    $.post('{{ route('frontend.user.recharge') }}', {id:data.field.id, amount:data.field.amount}, function(result){
                        layer.msg(result.message)
                    }, 'json');
                    layer.closeAll();
                });
                return false;
            });
        });
    </script>
@endsection