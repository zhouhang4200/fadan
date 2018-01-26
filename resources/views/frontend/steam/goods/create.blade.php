@extends('frontend.layouts.app')

@section('title', '商品 - 添加商品')

@section('css')
    <style>
        .layui-form-item > .layui-form-label {
            width: 110px;
        }

        .layui-form-item > .layui-input-block {
            margin-left: 170px;
        }

        .layui-form-item > .layui-upload > .layui-btn {
            margin-left: 30px;
        }

        .layui-upload-list {
            width: 500px;
            height: 300px;
            margin-left: 170px;
        }

        .layui-upload-list > img {
            width: 100%;
            height: 100%;
        }

        /*分割线*/
        .layui-form-label {
            width: 110px;
        }

        .layui-input-block {
            margin-left: 170px;
        }

        .layui-btn {
            margin-left: 30px;
        }

        .layui-upload-list {
            width: 500px;
            height: 300px;
            margin-left: 170px;
            background-size: cover !important;
            background-position: center !important;
        }

        .layui-upload-list > img {
            width: 100%;
            height: 100%;
            visibility:
        }

        .layui-input-block {
            margin-left: 170px;
        }

        .none {
            display: none;
        }

        .layui-input-block {
            margin-left: 170px;
        }

        .layui-anim {
            color: #1E9FFF !important;
        }
        .layui-input{

        }
    </style>
@endsection

@section('submenu')
    @include('frontend.goods.submenu')
@endsection

@section('main')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">游戏名</label>
            <div class="layui-input-block">
                <input type="text" name="game_name" autocomplete="off" placeholder="请输入标题" class="layui-input" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">版本名</label>
            <div class="layui-input-block">
                <input type="text" name="name" autocomplete="off" placeholder="请输入标题" class="layui-input" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">面值</label>
            <div class="layui-input-block">
                <input type="text" name="price" autocomplete="off" placeholder="请输入单价" class="layui-input" lay-verify="required|number">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">游戏URL</label>
            <div class="layui-input-block">
                <input type="text" name="game_url" autocomplete="off" placeholder="请输入游戏URL" class="layui-input" lay-verify="required">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">示例图</label>
            <div class="layui-upload">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-small upload-images">
                    上传图片
                </button><input class="layui-upload-file" type="file" name="file">

                <div class="layui-upload-list">
                    <img class="layui-upload-img" id="demo2">
                    <input type="hidden" name="images" value="" lay-verify="required">
                    <p id="demoText"></p>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">描述</label>
            <div class="layui-input-block">
                <textarea type="text" style="width: 500px;height: 100px" class="layui-textarea" name="description"></textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <button class="layui-btn layui-bg-blue" lay-submit="" lay-filter="add">确认添加</button>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate','upload'], function(){
            var form = layui.form, $ =layui.jquery , upload = layui.upload , layer = layui.layer;
            //监听提交
            form.on('submit(add)', function(data){
                $.post("{{ route('frontend.goods.store') }}", {data:data.field}, function (result) {
                    if(result.status == 1){
                        layer.alert(result.message);
                        redirect('/goods');
                    }else {
                        layer.alert(result.message);
                    }
                }, 'json');
                return false;
            });

            upload.render({
                elem: '.upload-images',
                url: "{{ route('frontend.game.upload-images') }}",
                size: 3000,
                accept: 'file',
                exts: 'jpg|jpeg|png|gif',
                before: function (obj) {
                    var dom = this;
                    obj.preview(function (index, file, result) {
                        dom.item.nextAll('div').css('background', 'url(' + result + ')');
                    });
                },
                done: function (res) {
                    var dom = this;
                    //如果上传失败
                    if (res.code == 2) {
                        return layer.msg('上传失败');
                    }
                    dom.item.nextAll('div').find('input').val(res.path);
                }
            });

        });
    </script>
@endsection