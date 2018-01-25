@extends('backend.layouts.main')

@section('title', ' | 添加违规')

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

        .layui-select-title {
            margin-bottom: 20px;
        }
        .layui-input-item .layui-input{
            width:492px;
        }
        .layui-input-block .layui-input{
            width:492px;
        }
        .layui-textarea {
            width:492px;
            height:300px;
        }
        dd .layui-select-tips {
            width:492px;
        }
        .layui-anim .layui-anim-upbit{
            width:492px;
        }
        .layui-form-select dl {
            min-width: 492px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
    @foreach ($gameArr as $k => $game)
        {{ $game }}
    @endforeach
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">添加违规</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="{{ route('punishes.store') }}">
                            {!! csrf_field() !!}
                                <div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">缴费金额</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="money" lay-verify="required" value="{{ old('money') }}" autocomplete="off" placeholder="请输入金额" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">截止时间</label>
                                        <div class="layui-input-block">
                                            <input type="text" class="layui-input" lay-verify="required" value="" name="deadline" id="test1" placeholder="点击选择日期">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">备注说明</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入内容" name="remark" lay-verify="required" class="layui-textarea">{{ old('remark') }}</textarea>
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

            // 
            form.on('select(userId)', function (data) {
                var userId = data.value;
                $.post('{{ route('punishes.user') }}', {id:userId}, function(result){

                        var length = result.orders.length;
                        var str = '';                          
                        result.orders.forEach(function(item, index){
                            str += '<option value="'+item+'">'+ item+'</option>';
                        })    
                        console.log(str); 
                        
                    }, 'json');
            });
          form.render();
    });  
    </script>
@endsection