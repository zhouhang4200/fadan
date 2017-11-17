@extends('frontend.layouts.app')

@section('title', '首页 - 违规记录')

@section('css')
    <style>
        .layui-table tr th, td {
            text-align: center;
        }
        .layui-form-label {
            width:60px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-inline" style="float:left">

            <div class="layui-form-item">
                <div class="layui-input-inline">

                    <div class="layui-input-inline">
                        <select name="type" lay-verify="" lay-search="">
                            <option value="">请选择支付状态</option>
                            <option value="0" {{ is_numeric($type) && $type == 0 ? 'selected' : '' }}>未支付</option>
                            <option value="1" {{ is_numeric($type) && $type == 1 ? 'selected' : '' }}>已支付</option>
                        </select>
                    </div>
                </div>
                
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="开始时间">
                </div>

                <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="结束时间">
                </div>
            </div>
        </div>
        <div class="layui-inline" >
            <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1">查找</button>
            <a href="{{ route('home-punishes.index') }}" class="layui-btn layui-btn-normal layui-btn-small">返回</a>
        </div>
    </form>

    <div class="layui-tab-item layui-show">
        <table class="layui-table" lay-size="sm">
            <thead>
            <tr style="text-aliag:center">
                <th style="width:7%">序号</th>
                <th>订单号</th>
                <th>交款金额</th>
                <th>支付状态</th>
                <th>交款最后期限</th>
                <th>发生时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($punishes as $punish)
                <tr>
                    <td>{{ $punish->id }}</td>
                    <td>{{ $punish->order_id }}</td>
                    <td>{{ $punish->money }}</td>
                    <td>{{ $punish->type == 1 ? '已交款' : '未交款' }}</td>
                    <td>{{ $punish->deadline }}</td>
                    <td>{{ $punish->created_at }}</td>
                    <td>
                        @if ($punish->type == 0)
                            <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px" id="payment" onclick="payment({{ $punish->id }})">交款</button>
                        @else
                            <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px" id="show" onclick="display({{ $punish->id }})">查看</button>
                        @endif
                    </td>
                </tr>
                @if ($punish->type == 0)
                    <div class="payment{{ $punish->id }}" style="display: none; padding: 10px">
                    
                        <input type="hidden" name="id">
                        <div class="layui-form-item">
                            <label class="layui-form-label">订单号</label>
                            <div class="layui-input-inline">
                                <input type="text" name="order_id" value="{{ $punish->order_id }}" lay-verify="required" placeholder="请输入服务名称" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户名</label>
                            <div class="layui-input-inline">
                                <input type="text" name="user_id" value="{{ $punish->user ? $punish->user->name : Auth::user()->name }}" lay-verify="required" placeholder="请输入显示排序" autocomplete="off" class="layui-input" value="999">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">金额</label>
                            <div class="layui-input-inline">
                                <input type="text" name="money" value="{{ $punish->money }}" lay-verify="required" placeholder="请输入服务名称" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">最后期限</label>
                            <div class="layui-input-inline">
                                <input type="text" name="deadline" value="{{ $punish->deadline }}" lay-verify="required" placeholder="请输入服务名称" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">备注说明</label>
                            <div class="layui-input-inline">
                                <textarea placeholder="请输入内容" name="remark" lay-verify="required" class="layui-textarea" style="height:200px;width:190px">{{ $punish->remark }}</textarea>
                            </div>
                        </div>
                    
                </div>
                @else
                <div class="show{{ $punish->id }}" style="display: none; padding: 10px">
                    <input type="hidden" name="id">
                        <div class="layui-form-item">
                            <label class="layui-form-label">订单号</label>
                            <div class="layui-input-inline">
                                <input type="text" name="order_id" value="{{ $punish->order_id }}" lay-verify="required" placeholder="请输入服务名称" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户名</label>
                            <div class="layui-input-inline">
                                <input type="text" name="user_id" value="{{ $punish->user ? $punish->user->name : Auth::user()->name }}" lay-verify="required" placeholder="请输入显示排序" autocomplete="off" class="layui-input" value="999">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">金额</label>
                            <div class="layui-input-inline">
                                <input type="text" name="money" value="{{ $punish->money }}" lay-verify="required" placeholder="请输入服务名称" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">最后期限</label>
                            <div class="layui-input-inline">
                                <input type="text" name="deadline" value="{{ $punish->deadline }}" lay-verify="required" placeholder="请输入服务名称" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">备注说明</label>
                            <div class="layui-input-inline">
                                <textarea placeholder="请输入内容" name="remark" lay-verify="required" class="layui-textarea" style="height:200px;width:190px">{{ $punish->remark }}</textarea>
                            </div>
                        </div>
                </div>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
    
    {!! $punishes->appends([
        'type' => $type,
        'startDate' => $startDate,
        'endDate' => $endDate,
    ])->render() !!}

@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            var form = layui.form, layer = layui.layer;
            //常规用法
            laydate.render({
            elem: '#test1'
            });

            //常规用法
            laydate.render({
            elem: '#test2'
            });
        });

        function display(id) {
            layui.use('form', function() {
                var form = layui.form, layer = layui.layer;
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '违规详情',
                    content: $('.show'+id),
                    btn: ['关闭']
                });
            });
        }

        function payment(id) {
            layui.use('form', function() {
                var form = layui.form, layer = layui.layer;
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '违规交款',
                    content: $('.payment'+id),
                    btn: ['确认交款', '关闭'] //只是为了演示
                    ,yes: function(){
                        layer.closeAll();
                        $.post('{{ route('home-punishes.payment') }}', {id:id}, function (result) {
                            layer.msg(result.message);
                            window.location.href = "{{ route('home-punishes.index') }}";
                        });
                    }
                });
            });
        }

    </script>
@endsection