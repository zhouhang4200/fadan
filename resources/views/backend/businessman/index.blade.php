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
                    <div class="filter-block">
                        <form class="layui-form">
                            <div class="row">
                                {{--<div class=" col-xs-1">--}}
                                    {{--<select class="layui-input" name="source_id" lay-search="">--}}
                                        {{--<option value="0">请选择来源</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                <div class=" col-xs-2">
                                    <input type="text" class="layui-input" name="id"  placeholder="账号ID" value="{{ $id }}">
                                </div>
                                <div class=" col-xs-2">
                                    <input type="text" class="layui-input" name="nickname"  placeholder="别名" value="{{ $nickname }}">
                                </div>
                                <div class=" col-xs-2">
                                    <input type="text" class="layui-input" name="name"  placeholder="账号名称" value="{{ $name }}">
                                </div>
                                <div class=" col-xs-2">
                                    <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $users->total() }}　本页显示：{{$users->count()}}
                        </div>
                        <div class="col-xs-9">
                        </div>
                    </div>
                    <table class="layui-table layui-form" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>账号ID</th>
                                    <th>别名</th>
                                    <th>账号名称</th>
                                    <th>可用余额</th>
                                    <th>冻结余额</th>
                                    <th>注册时间</th>
                                    <th>最后登录时间</th>
                                    <th>实名认证</th>
                                    <th style="text-align: center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->nickname }}</td>
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
                                            <a href="{{ route('groups.create', ['id' => $user->id]) }}" class="layui-btn layui-btn layui-btn-normal layui-btn-mini">添加角色</a>
                                            <a href="{{ route('groups.show', ['id' => $user->id])  }}" class="layui-btn layui-btn layui-btn-normal layui-btn-mini">查看角色</a>
                                            <a href="{{ route('frontend.user.show', ['id' => $user->id])  }}" class="layui-btn layui-btn layui-btn-normal layui-btn-mini">详情</a>

                                            <button  class="layui-btn layui-btn layui-btn-normal layui-btn-mini" lay-submit lay-filter="recharge-button" data-id="{{ $user->id }}" data-name="{{ $user->name }}">手动加款</button>
                                            <button  class="layui-btn layui-btn layui-btn-normal layui-btn-mini" lay-submit lay-filter="subtract-money-button" data-id="{{ $user->id }}" data-name="{{ $user->name }}">手动减款</button>

                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $users->total() }}　本页显示：{{$users->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $users->appends([
                                    'id' => $id,
                                    'nickname' => $nickname,
                                    'name' => $name,
                                ])->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="recharge-pop" style="display: none;padding: 20px">
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
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <input type="text" name="remark" autocomplete="off" class="layui-input" lay-verify="required">
                </div>
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="recharge">确定</button>
            </div>
        </form>
    </div>

    <!-- 减款弹窗 -->
    <div id="subtract-money-popup" style="display: none;padding: 20px">
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
                    <input type="text" name="amount" autocomplete="off" placeholder="请输入减款金额" class="layui-input" lay-verify="required|number">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <input type="text" name="remark" autocomplete="off" class="layui-input" lay-verify="required">
                </div>
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="subtract-money-popup">确定</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
   <script>
        layui.use(['form', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer;

            // 手动加款按钮
            form.on('submit(recharge-button)', function(data){
                $('input[name=id]').val(data.elem.getAttribute('data-id'));
                $('input[name=name]').val(data.elem.getAttribute('data-name'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '手动加款',
                    content: $('#recharge-pop')
                });
                return false;
            });
            form.on('submit(recharge)', function(data){
                layer.confirm('您确认给用户ID为: ' + data.field.id  +' 的商户加款' + data.field.amount + ' 元吗？', {icon: 3, title:'提示'}, function(index){
                    $.post('{{ route('frontend.user.recharge') }}', {
                        user_id:data.field.id,
                        amount:data.field.amount,
                        remark:data.field.remark
                    }, function (result) {
                        layer.msg(result.message)
                    }, 'json');
                    layer.closeAll();
                });
                return false;
            });

            // 手动减款
            form.on('submit(subtract-money-button)', function(data) {
                $('input[name=id]').val(data.elem.getAttribute('data-id'));
                $('input[name=name]').val(data.elem.getAttribute('data-name'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '手动减款',
                    content: $('#subtract-money-popup')
                });
                return false;
            });
            form.on('submit(subtract-money-popup)', function(data){
                layer.confirm('您确认要扣用户ID为: ' + data.field.id  +' 商户 <br/><span style="color:red;">' + $(data.form).find("option:selected").text()  + data.field.amount + ' </span>元吗？', {icon: 3, title:'提示'}, function(index){
                    $.post("{{ route('frontend.user.subtract-money') }}", {
                        user_id:data.field.id,
                        amount:data.field.amount,
                        remark:data.field.remark
                    }, function(result){
                        layer.msg(result.message)
                    }, 'json');
                    layer.closeAll();
                });
                return false;
            });
        });
    </script>
@endsection
