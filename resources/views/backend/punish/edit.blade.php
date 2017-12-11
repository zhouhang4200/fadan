@extends('backend.layouts.main')

@section('title', ' | 编辑违规')

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
        .layui-input-block .layui-select-title input .layui-input .layui-unselect{
            width:370px;
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
                            <li class="layui-this" lay-id="add">编辑违规</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="{{ route('punishes.update', ['id' => $punish->id]) }}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PUT">
                                <div style="width: 40%">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">用户id</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="user_id" lay-verify="required" value="{{ old('user_id') ?: $punish->user_id }}" autocomplete="off" placeholder="请输入用户id" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">订单号</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="order_no" lay-verify="required" value="{{ old('order_no') ?: $punish->order_no }}" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">缴费金额</label>
                                        <div class="layui-input-block">
                                            <select name="money">
                                                <option value="">请选择金额</option>
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="50">50</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">备注说明</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入内容" name="remark" lay-verify="required" class="layui-textarea" style="width:801px">{{ old('remark') ?: $punish->remark }}</textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">凭证图片</label>
                                        <div class="layui-input-block">
                                            <ul style="width:700px">
                                            @forelse($punish->voucher as $k => $voucherItem)
                                                <li id="voucher{{ $k+1 }}" style="float:left;width:200px;height:200px;background-image: url('{{ $voucherItem }}');background-size: cover !important;background-position: center !important;margin-right:3px;">
                                                </li>
                                            @empty
                                            @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">立即提交</button>
                                        </div>
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
            var laydate = layui.laydate;
            //常规用法
            laydate.render({
            elem: '#test1'
            });
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            var error = "{{ $errors->count() > 0 ? '请按要求填写!' : '' }}";
            var createFail = "{{ session('createFail') ?: '' }}";

            if (error) {
                layer.msg(error, {icon: 5, time:1500});            } else if(createFail) {
                layer.msg(createFail, {icon: 5, time:1500});            }
          form.render();
    });  


    </script>
@endsection