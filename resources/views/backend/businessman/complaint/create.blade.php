@extends('backend.layouts.main')

@section('title', ' | 商户投诉')

@section('css')
    <style>
        .layui-form-label {
            width: 120px;
        }
        .layui-input-block {
            margin-left: 120px;
        }
    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class=""><span>首页</span></li>
            <li class="active"><span>商户投诉</span></li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="main-box">
            <header class="main-box-header clearfix">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(isset($message))
                        <div class="alert alert-success">
                            <ul>
                                    <li>{{ $message }}</li>
                            </ul>
                        </div>
                @endif
            </header>
            <div class="main-box-body clearfix">

                <div class="layui-tab-item layui-show col-lg-5">
                    <form class="layui-form" action="{{ route('businessman.complaint.store') }}" method="post">

                        <div class="layui-form-item">
                            <label class="layui-form-label">*投诉商户ID</label>
                            <div class="layui-input-block">
                                <input type="text" name="complaint_primary_user_id" required  lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{{ old('complaint_primary_user_id')}}">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">*被投诉商户ID</label>
                            <div class="layui-input-block">
                                <input type="text" name="be_complaint_primary_user_id" required  lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{{ old('be_complaint_primary_user_id') }}">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">*被投诉订单号</label>
                            <div class="layui-input-block">
                                <input type="text" name="order_no" required  lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{{ old('order_no') }}">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">*天猫订单号</label>
                            <div class="layui-input-block">
                                <input type="text" name="foreign_order_no" required  lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{{ old('foreign_order_no') }}">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">*被投诉赔偿金额</label>
                            <div class="layui-input-block">
                                <input type="text" name="amount" required  lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{{ old('amount')  }}">
                            </div>
                        </div>

                        <div class="layui-form-item layui-form-text">
                            <label class="layui-form-label">备注</label>
                            <div class="layui-input-block">
                                <textarea name="remark" placeholder="请输入内容" class="layui-textarea">{{ old('remark') }}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <input type="submit" class="layui-btn" value="确定">
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
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
        //Demo
        layui.use('form', function() {
            var form = layui.form;

            //监听提交
            form.on('submit(formDemo)', function (data) {
                layer.msg(JSON.stringify(data.field));
                return false;
            });

            $('input[name=order_no]').on('blur', function () {
                $.post('{{ route('businessman.complaint.query-order') }}', {no:$(this).val()}, function (result) {
                    $('input[name=complaint_primary_user_id]').val(result.content.creator_primary_user_id);
                    $('input[name=be_complaint_primary_user_id]').val(result.content.gainer_primary_user_id);
                    $('input[name=foreign_order_no]').val(result.content.foreign_order_no);
                    $('input[name=amount]').val(result.content.amount);
                    layui.form.render();
                }, 'json');
            });
        });
    </script>
@endsection

