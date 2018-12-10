@extends('backend.layouts.main')

@section('title', ' | 游戏修改')

@section('css')
    <style>
        .layui-table th, td{
            text-align: center;
        }
        .layui-form-checked span, .layui-form-checked:hover span {
            background-color: #1E9FFF !important;
        }
        .layui-form-checked i, .layui-form-checked:hover i {
            color: #1E9FFF !important;
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
                            <li class="layui-this" lay-id="add">游戏修改</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="" action="">
                                {!! csrf_field() !!}
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*游戏名</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="name" lay-verify="required" value="{{ $game->name }}" autocomplete="off" placeholder="" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*游戏类型</label>
                                    <div class="layui-input-block">
                                        @forelse(config('gameleveling.game_type') as $id => $name)
                                            <input type="checkbox" name="type" value="{{ $id  }}" {{ in_array($id, $game->gameTypes ? $game->gameTypes->pluck('type')->toArray() : []) ? 'checked' : '' }} title="{{ $name }}">
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">*图标</label>
                                    <div class="layui-input-block">
                                        <div class="layui-upload">
                                            <button type="button" class="layui-btn layui-btn-normal" class="layui-btn" id="test1">上传图片</button>
                                            <div class="layui-upload-list">
                                                <img class="layui-upload-img" id="demo1" src="{{ $game->icon }}" style="width: 200px;height: 200px">
                                                <p id="demoText"></p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="icon" id="icon" value="{{ $game->icon }}" placeholder="" autocomplete="off" class="layui-input">
                                        <input type="hidden" name="id" value="{{ $game->id }}" placeholder="" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="update">确认</button>
                                        <button type="button" class="layui-btn layui-btn-normal cancel" >取消</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'upload'], function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;
            var $ = layui.jquery;
            var upload = layui.upload;

            // 取消按钮
            $('.cancel').click(function () {
                window.location.href="{{ route('admin.game.index') }}";
            });
            //普通图片上传
            var uploadInst = upload.render({
                elem: '#test1'
                ,url: "{{ route('admin.game.upload') }}"
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $('#demo1').attr('src', result); //图片链接（base64）
                    });
                }
                ,done: function(res){
                    if (res.status == 1) {
                        $("#icon").val(res.message);
                    } else {
                        return layer.msg('上传失败!');
                    }
                }
                ,error: function(){
                    return layer.msg('上传失败!');
                }
            });
            // 修改
            form.on('submit(update)', function (data) {
                var type=[];
                $("input:checkbox[name='type']:checked").each(function() { // 遍历name=test的多选框
                    $(this).val();  // 每一个被选中项的值
                    type.push($(this).val());
                });

                $.post("{{ route('admin.game.update') }}", {
                    name:data.field.name,
                    icon:data.field.icon,
                    id:data.field.id,
                    type:type
                }, function (result) {
                    layer.msg(result.message);
                });
                return false;
            });
        });
    </script>
@endsection