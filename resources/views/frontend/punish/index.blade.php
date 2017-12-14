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
        .layui-tab-content .layui-input{
            width:400px;
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
                            <option value="">请选择类型</option>
                            <option value="2" {{ is_numeric($type) && $type == 2 ? 'selected' : '' }}>罚款</option>
                            <option value="3" {{ is_numeric($type) && $type == 3 ? 'selected' : '' }}>加权重</option>
                            <option value="4" {{ is_numeric($type) && $type == 4 ? 'selected' : '' }}>减权重</option>
                            <option value="5" {{ is_numeric($type) && $type == 5 ? 'selected' : '' }}>禁止接单</option>
                        </select>
                    </div>
                </div>

                <div class="layui-input-inline">
                    <div class="layui-input-inline">
                        <select name="status" lay-verify="" lay-search="">
                            <option value="">请选择状态</option>
                            <option value="0" {{ is_numeric($status) && $status == 0 ? 'selected' : '' }}>禁止接单一天待处罚</option>
                            <option value="21" {{ is_numeric($status) && $status == 3 ? 'selected' : '' }}>未交罚款</option>
                            <option value="22" {{ is_numeric($status) && $status == 4 ? 'selected' : '' }}>已交罚款</option>
                            <option value="31" {{ is_numeric($status) && $status == 5 ? 'selected' : '' }}>未加权重</option>
                            <option value="32" {{ is_numeric($status) && $status == 6 ? 'selected' : '' }}>已加权重</option>
                            <option value="41" {{ is_numeric($status) && $status == 7 ? 'selected' : '' }}>未减权重</option>
                            <option value="42" {{ is_numeric($status) && $status == 8 ? 'selected' : '' }}>已减权重</option>
                            <option value="9" {{ is_numeric($status) && $status == 9 ? 'selected' : '' }}>申诉中</option>
                            <option value="10" {{ is_numeric($status) && $status == 10 ? 'selected' : '' }}>申诉驳回</option>
                            <option value="11" {{ is_numeric($status) && $status == 11 ? 'selected' : '' }}>撤销</option>
                            <option value="12" {{ is_numeric($status) && $status == 12 ? 'selected' : '' }}>禁止接单一天已处罚</option>
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
                <th>序号</th>
                <th>单号</th>
                <th>关联订单号</th>
                <th>账号id</th>
                <th>类型</th>
                <th>状态</th>
                <th>罚款金额</th>
                <th>最后期限</th>
                <th>初始权重值</th>
                <th>奖惩权重率</th>
                <th>变更后的权重</th>
                <th>权重生效时间</th>
                <th>权重截止时间</th>
                <th>奖励金额</th>
                <th>时间</th>
                <th style="width:10%">操作</th>
            </thead>
            <tbody>
            @forelse($punishes as $punish)
                <tr>
                    <td>{{ $punish->id }}</td>
                    <td>{{ $punish->no }}</td>
                    <td>{{ $punish->order_no }}</td>
                    <td>{{ $punish->user_id }}</td>
                    <td>{{ config('punish.type')[$punish->type] }}</td>
                    @if(in_array($punish->type, [2, 4, 5]) && in_array($punish->status, [2, 10, 11, 12]))
                    <td style="color:#1E9FFF">
                        @if(in_array($punish->type, [2, 3, 4]) && !in_array($punish->status, [0, -1, 9, 10, 11]))
                        {{ config('punish.status')[$punish->type . $punish->status] }}
                        @else
                        {{ config('punish.status')[$punish->status] }}
                        @endif
                    </td>
                    @elseif($punish->type == 3 && $punish->status == 2)
                    <td style="color:green">
                        @if(in_array($punish->type, [2, 3, 4]) && !in_array($punish->status, [0, -1, 9, 10, 11]))
                        {{ config('punish.status')[$punish->type . $punish->status] }}
                        @else
                        {{ config('punish.status')[$punish->status] }}
                        @endif
                    </td>
                    @else
                    <td style="color:red">
                        @if(in_array($punish->type, [2, 3, 4]) && !in_array($punish->status, [0, -1, 9, 10, 11]))
                        {{ config('punish.status')[$punish->type . $punish->status] }}
                        @else
                        {{ config('punish.status')[$punish->status] }}
                        @endif
                    </td>
                    @endif
                    <td>{{ $punish->sub_money ? number_format($punish->sub_money, 2) : '--' }}</td>
                    <td>{{ $punish->deadline ?? '--' }}</td>
                    <td>{{ $punish->before_weight_value ?? '--' }}</td>
                    <td>{{ $punish->ratio ?? '--' }}</td>
                    <td>{{ $punish->after_weight_value ?? '--' }}</td>
                    <td>{{ $punish->start_time ?? '--' }}</td>
                    <td>{{ $punish->end_time ?? '--' }}</td>
                    <td>{{ $punish->add_money ? number_format($punish->add_money, 2) : '--' }}</td>
                    <td>{{ $punish->created_at }}</td>
                    <td>
                    @if($punish->status == 9)
                        申诉中
                    @elseif($punish->confirm == 0 && in_array($punish->type, ['5']))
                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px" id="show" onclick="detail({{ $punish->id }})">详情</button>
                    @elseif($punish->confirm == 0 && in_array($punish->status, ['1', '9']))
                        <button class="layui-btn layui-btn-danger layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px" id="show" onclick="display({{ $punish->id }})">查看</button>
                    @elseif($punish->confirm == 0 && in_array($punish->type, ['1', '3']))
                        <button class="layui-btn layui-btn-danger layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px" id="show" onclick="show({{ $punish->id }})">查看</button>
                    @else
                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px" id="show" onclick="detail({{ $punish->id }})">详情</button>
                    @endif
                    </td>
                </tr>
                <div class="payment{{ $punish->id }}" style="display: none; padding: 10px">
                    <div class="layui-tab-content">
                        <form class="layui-form" method="POST" action="">
                            <input type="hidden" name="id" value="{{ $punish->id }}">
                            {!! csrf_field() !!}
                            <div style="width: 40%">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">用户id</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="user_id" lay-verify="required" value="{{ $punish->user_id }}" autocomplete="off" placeholder="请输入用户id" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">订单号</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="no" lay-verify="required" value="{{ $punish->no }}" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">关联订单号</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="order_no" lay-verify="required" value="{{ $punish->order_no }}" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">类型</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="type" lay-verify="required" value="{{ config('punish.type')[$punish->type] }}" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                    </div>
                                </div>

                                <div class="layui-form-item">
                                    <label class="layui-form-label">状态</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="status" lay-verify="required" value="@if(in_array($punish->type, [2, 3, 4]) && !in_array($punish->status, [0, -1, 9, 10, 11])) {{ config('punish.status')[$punish->type . $punish->status] }} @else {{ config('punish.status')[$punish->status] }} @endif" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                    </div>
                                </div>
                                 <div class="layui-form-item">
                                    <label class="layui-form-label">罚款金额</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="sub_money" lay-verify="required" value="{{ $punish->sub_money ? number_format($punish->sub_money, 2) : '--' }}" autocomplete="off" placeholder="" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">截止时间</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->deadline ?? '--' }}" name="deadline"  placeholder="点击选择日期">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">初始权重</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->before_weight_value ?? '--' }}" name="before_weight_value"  placeholder="">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">权重率</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->ratio ?? '--' }}" name="ratio"  placeholder="点击选择日期">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">变更后的权重</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->after_weight_value ?? '--' }}" name="after_weight_value"  placeholder="">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">权重生效时间</label>
                                    <div class="layui-input-block">
                                    <input type="text" class="layui-input" value="" name="start_time" id="start_time" placeholder="权重生效时间">
                                    </div>
                                </div>

                                <div class="layui-form-item">
                                    <label class="layui-form-label">权重截止时间</label>
                                    <div class="layui-input-block">
                                    <input type="text" class="layui-input" value=""  name="end_time" id="end_time" placeholder="权重截止时间">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">奖励金额</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->add_money ? number_format($punish->add_money, 2) : '--' }}" name="add_money"  placeholder="">
                                    </div>
                                </div>                           

                                <div class="layui-form-item">
                                    <label class="layui-form-label">备注说明</label>
                                    <div class="layui-input-block">
                                        <textarea placeholder="请输入内容" name="remark" lay-verify="required" class="layui-textarea" style="width:400px">{{ $punish->remark }}</textarea>
                                    </div>
                                </div>
                                @if($punish->voucher)
                                <div class="layui-form-item">
                                    <label class="layui-form-label">凭证图片</label>
                                    <div class="layui-input-block">
                                        <ul style="width:700px">
                                        @forelse($punish->voucher as $k => $voucherItem)
                                            <li id="voucher{{ $k+1 }}" style="float:left;width:400px;height:400px;background-image: url('{{ $voucherItem }}');background-size: cover !important;background-position: center !important;margin-bottom:3px;">
                                            </li>
                                        @empty
                                        @endforelse
                                        </ul>
                                    </div>
                                </div>
                                @endif

                            </div>                              
                        </form>
                    </div>
                </div>
                <div class="show{{ $punish->id }}" style="display: none; padding: 10px">
                    <div class="layui-tab-content">
                        <form class="layui-form" method="POST" action="">
                            <input type="hidden" name="id" value="{{ $punish->id }}">
                            {!! csrf_field() !!}
                            <div style="width: 40%">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">用户id</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="user_id" lay-verify="required" value="{{ $punish->user_id }}" autocomplete="off" placeholder="请输入用户id" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">订单号</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="no" lay-verify="required" value="{{ $punish->no }}" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">关联订单号</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="order_no" lay-verify="required" value="{{ $punish->order_no }}" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">类型</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="type" lay-verify="required" value="{{ config('punish.type')[$punish->type] }}" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                    </div>
                                </div>

                                <div class="layui-form-item">
                                    <label class="layui-form-label">状态</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="status" lay-verify="required" value="@if(in_array($punish->type, [2, 3, 4]) && !in_array($punish->status, [0, -1, 9, 10, 11])) {{ config('punish.status')[$punish->type . $punish->status] }} @else {{ config('punish.status')[$punish->status] }} @endif" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                    </div>
                                </div>
                                 <div class="layui-form-item">
                                    <label class="layui-form-label">罚款金额</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="sub_money" lay-verify="required" value="{{ $punish->sub_money ? number_format($punish->sub_money, 2) : '--' }}" autocomplete="off" placeholder="" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">截止时间</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->deadline ?? '--' }}" name="deadline"  placeholder="点击选择日期">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">初始权重</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->before_weight_value ?? '--' }}" name="before_weight_value"  placeholder="">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">权重率</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->ratio ?? '--' }}" name="ratio"  placeholder="点击选择日期">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">变更后的权重</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->after_weight_value ?? '--' }}" name="after_weight_value"  placeholder="">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">权重生效时间</label>
                                    <div class="layui-input-block">
                                    <input type="text" class="layui-input" value="" name="start_time" id="start_time" placeholder="权重生效时间">
                                    </div>
                                </div>

                                <div class="layui-form-item">
                                    <label class="layui-form-label">权重截止时间</label>
                                    <div class="layui-input-block">
                                    <input type="text" class="layui-input" value=""  name="end_time" id="end_time" placeholder="权重截止时间">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">奖励金额</label>
                                    <div class="layui-input-block">
                                        <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->add_money ? number_format($punish->add_money, 2) : '--' }}" name="add_money"  placeholder="">
                                    </div>
                                </div>                           

                                <div class="layui-form-item">
                                    <label class="layui-form-label">备注说明</label>
                                    <div class="layui-input-block">
                                        <textarea placeholder="请输入内容" name="remark" lay-verify="required" class="layui-textarea" style="width:400px">{{ $punish->remark }}</textarea>
                                    </div>
                                </div>
                                @if($punish->voucher)
                                <div class="layui-form-item">
                                    <label class="layui-form-label">凭证图片</label>
                                    <div class="layui-input-block">
                                        <ul style="width:700px">
                                        @forelse($punish->voucher as $k => $voucherItem)
                                            <li id="voucher{{ $k+1 }}" style="float:left;width:400px;height:400px;background-image: url('{{ $voucherItem }}');background-size: cover !important;background-position: center !important;margin-bottom:3px;">
                                            </li>
                                        @empty
                                        @endforelse
                                        </ul>
                                    </div>
                                </div>
                                @endif

                            </div>                              
                        </form>
                    </div>
                </div>

            @empty
            @endforelse
            </tbody>
        </table>
    {!! $punishes->appends([
        'type' => $type,
        'startDate' => $startDate,
        'endDate' => $endDate,
    ])->render() !!}
    </div> 

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
             //常规用法
            laydate.render({
            elem: '#start_time'
            });

            //常规用法
            laydate.render({
            elem: '#end_time'
            });
        }); 

        function display(id) {
            layui.use('form', function() {
                var form = layui.form, layer = layui.layer;
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '奖惩明细',
                    area: ['650px', '650px'],
                    content: $('.payment'+id),
                    btn: ['确认', '申诉'] //只是为了演示
                    ,yes: function(){
                        layer.closeAll();
                        $.post('{{ route('home-punishes.payment') }}', {id:id}, function (result) {
                            layer.msg(result.message, {
                                time:1500,
                            })
                            window.location.href = "{{ route('home-punishes.index') }}";
                        });
                    },
                    btn2: function(index, layero){
                        layer.closeAll();
                        $.post('{{ route('home-punishes.complain') }}', {id:id}, function (result) {
                            layer.msg(result.message, {
                                time:1500,
                            })
                            window.location.href = "{{ route('home-punishes.index') }}";
                        });
                    }
                });
            });
        } 

        function show(id) {
            layui.use('form', function() {
                var form = layui.form, layer = layui.layer;
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '奖惩明细',
                    area: ['650px', '650px'],
                    content: $('.payment'+id),
                    btn: ['确认'] //只是为了演示
                    ,yes: function(){
                        layer.closeAll();
                        $.post('{{ route('home-punishes.payment') }}', {id:id}, function (result) {
                            layer.msg(result.message, {
                                time:1500,
                            })
                            window.location.href = "{{ route('home-punishes.index') }}";
                        });
                    }
                });
            });
        }  

         function detail(id) {
            layui.use('form', function() {
                var form = layui.form, layer = layui.layer;
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '奖惩明细',
                    area: ['650px', '650px'],
                    content: $('.payment'+id),
                    btn: ['返回'] //只是为了演示
                    
                });
            });
        }            
      
    </script>
@endsection