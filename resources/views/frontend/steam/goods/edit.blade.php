@extends('frontend.layouts.app')

@section('title', '商品 - 修改商品')

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
    </style>
@endsection

@section('submenu')
    @include('frontend.steam.submenu')
@endsection

@section('main')
    <form class="layui-form" action="">
    <input type="hidden" name="id" value="{{ $goods->id }}">
        <div class="layui-form-item">
            <label class="layui-form-label">游戏</label>
            <div class="layui-input-block">
                <select name="game_id" lay-filter="aihao" lay-verify="required">
                    <option value=""></option>
                    @forelse($games as $key => $val)
                        <option value="{{ $key }}" {{ $goods->game && $goods->game->id == $key ? 'selected' : '' }}>{{ $val }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">商品名</label>
            <div class="layui-input-block">
                <input type="text" name="name" autocomplete="off" value="{{ old('name') ?: $goods->name }}" placeholder="请输入标题" class="layui-input" lay-verify="required">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">面值</label>
            <div class="layui-input-block">
                <input type="text" name="price" autocomplete="off" value="{{ old('price') ?: $goods->price }}" placeholder="请输入单价" class="layui-input" lay-verify="required|number">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">游戏URL</label>
            <div class="layui-input-block">
                <input type="text" name="game_url" autocomplete="off" value="{{ old('game_url') ?: $goods->game_url }}" placeholder="请输入游戏URL" class="layui-input" lay-verify="required">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">示例图</label>
            <div class="layui-upload">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-small upload-images">
                    上传图片
                </button><input class="layui-upload-file" type="file" value="" name="file">

                <div class="layui-upload-list">
                    <img class="layui-upload-img" id="demo2" src="{{$goods->images}}">
                    <input type="hidden" name="images" value="{{$goods->images}}" lay-verify="required">
                    <p id="demoText"></p>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">描述</label>
            <div class="layui-input-block">
                <textarea type="text" style="width: 500px;height: 100px" name="description" lay-verify="required">{{ old('description') ?: $goods->description }}</textarea>
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label">显示排序</label>
            <div class="layui-input-block">
                <input type="text" name="sortord" value="{{ old('sortord') ?: $goods->sortord }}" autocomplete="off" placeholder="请输入显示排序" class="layui-input"  lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn layui-bg-blue" lay-submit="" lay-filter="update">确认修改</button>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate','upload'], function(){
            var form = layui.form, $ =layui.jquery , upload = layui.upload , layer = layui.layer;

            //监听提交
            form.on('submit(update)', function(data){
                $.post("{{ route('frontend.goods.update') }}", {data:data.field}, function (result) {
                    layer.alert(result.message);
                    redirect('/goods');
                }, 'json');
                return false;
            });

            upload.render({
                elem: '.upload-images',
                url: "{{ route('ident.upload-images') }}",
                size: 3000,
                accept: 'file',
                exts: 'jpg|jpeg|png|gif',
                before: function (obj) {
                    var dom = this;
                    obj.preview(function (index, file, result) {
                        dom.item.nextAll('div').find('img').remove();
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