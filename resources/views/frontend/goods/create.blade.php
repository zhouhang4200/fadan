@extends('frontend.layouts.app')

@section('title', '商品 - 添加商品')

@section('submenu')
    @include('frontend.goods.submenu')
@endsection

@section('main')
    <form class="layui-form layui-form-pane" action="">

        <div class="layui-form-item">
            <label class="layui-form-label">类型</label>
            <div class="layui-input-block">
                <select name="interest" lay-filter="aihao">
                    <option value=""></option>
                    @forelse($services as $key => $val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">游戏</label>
            <div class="layui-input-block">
                <select name="interest" lay-filter="aihao">
                    <option value=""></option>
                    @forelse($games as $key => $val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">商品名</label>
            <div class="layui-input-block">
                <input type="text" name="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">单价</label>
            <div class="layui-input-block">
                <input type="text" name="title" autocomplete="off" placeholder="请输入单价" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">外部ID</label>
            <div class="layui-input-block">
                <input type="text" name="title" autocomplete="off" placeholder="请输入外部ID" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" pane="">
            <label class="layui-form-label">是否显示</label>
            <div class="layui-input-block">
                <input type="checkbox" checked="" name="open" lay-skin="switch" lay-filter="switchTest" title="开关">
            </div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn layui-bg-blue" lay-submit="" lay-filter="add">确认添加</button>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
                    ,layer = layui.layer
                    ,layedit = layui.layedit
                    ,laydate = layui.laydate;

            //监听指定开关
            form.on('switch(switchTest)', function(data){
                layer.msg('开关checked：'+ (this.checked ? 'true' : 'false'), {
                    offset: '6px'
                });
            });

            //监听提交
            form.on('submit(add)', function(data){
                layer.alert(JSON.stringify(data.field), {
                    title: '最终的提交信息'
                });
                return false;
            });


        });
    </script>
@endsection