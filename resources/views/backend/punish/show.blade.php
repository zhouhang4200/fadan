@extends('backend.layouts.main')

@section('title', ' | 违规详情')

@section('css')
    <style>
        .layui-tab-content input {
            width:800px;
        }
        .table {
            width:800px;
        }
        .layui-form-item .layui-input-inline {
            float: left;
            width: 135px;
            margin-right: 10px;
        }
        .layui-form-checkbox span {
            padding: 0 5px;
        }
        .layui-form-checked[lay-skin="primary"] i {
            color: #fff;
            background-color: #1E9FFF;
            border-color: #1E9FFF;
        }
        .layui-form-label {
            width: 100px;
        }
    </style>
@endsection

@section('content')



    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">违规详情</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="">
                                {!! csrf_field() !!}
                                <input type="hidden" name="id" value="{{ $punish->id }}">
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
                                            <input type="text" name="status" lay-verify="required" value="{{ config('punish.status')[$punish->status] }}" autocomplete="off" placeholder="请输入订单号" class="layui-input">
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
                                            <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->start_time ?? '--' }}" name="start_time"  placeholder="">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">权重截止时间</label>
                                        <div class="layui-input-block">
                                            <input type="text" class="layui-input" lay-verify="required" value="{{ $punish->end_time ?? '--' }}" name="end_time"  placeholder="">
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
                                            <textarea placeholder="请输入内容" name="remark" lay-verify="required" class="layui-textarea" style="width:801px">{{ $punish->remark }}</textarea>
                                        </div>
                                    </div>
                                    @if ($punish->voucher)
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">凭证图片</label>
                                        <div class="layui-input-block">
                                            <ul>
                                                @forelse($punish->voucher as $k => $voucherItem)
                                                    <li id="voucher{{ $k+1 }}" style="float:left;width:400px;height:400px;background-image: url('{{ $voucherItem }}');background-size: cover !important;background-position: center !important;margin-bottom:3px;">
                                                    </li>
                                                @empty
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="layui-inline">
                                        <label class="layui-form-label"></label>
                                    @if($punish->status == 9)
                                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="pass" style="margin-left: 10px">同意申诉</button>
                                        <button  class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="refuse">驳回申诉</button>
                                    @else
                                        <a class="layui-btn layui-btn-normal layui-btn-small" href="{{ route('punishes.index') }}"  style="margin-left: 10px">返回</a>
                                    @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate, form = layui.form;
            //常规用法
            laydate.render({
                elem: '#test1'
            });

            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            var error = "{{ $errors->count() > 0 ? '请按要求填写!' : '' }}";
            var createFail = "{{ session('createFail') ?: '' }}";

            if (error) {
                layer.msg(error, {icon: 5, time:1500});
            } else if(createFail) {
                layer.msg(createFail, {icon: 5, time:1500});
            }

            form.on('submit(pass)', function (data) {
                $.post('{{ route('execute.pass') }}', {data: data.field}, function (result) {
                    if (result.code == 1) {
                        layer.msg(result.message, {
                            icon:6,
                            time:1500
                        })
                    } else {
                        layer.msg(result.message, {
                            icon:5,
                            time:1500
                        })
                    }
                }, 'json');
                    window.location.href = "{{ route('punishes.index') }}";
                return false;
            });

            form.on('submit(refuse)', function (data) {
                $.post('{{ route('execute.refuse') }}', {data: data.field}, function (result) {
                    if (result.code == 1) {
                        layer.msg(result.message, {
                            icon:6,
                            time:1500
                        })
                    } else {
                        layer.msg(result.message, {
                            icon:5,
                            time:1500
                        })
                    }
                }, 'json');
                    window.location.href = "{{ route('punishes.index') }}";
                return false;
            });
        });


    </script>
@endsection
