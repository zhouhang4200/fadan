@extends('backend.layouts.main')

@section('title', ' | 黑名单')

@section('css')
    <style>
        .layui-table th, td{
            text-align: center;
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
                            <li class="layui-this" lay-id="add">黑名单</li>
                        </ul>
                        <div class="layui-tab-content">
                        <form class="layui-form" method="" action="">
                            {!! csrf_field() !!}
                            <div class="layui-form-item">
                                <label class="layui-form-label">*打手昵称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="hatchet_man_name" lay-verify="required" value="{{ $hatchetManBlacklist->hatchet_man_name }}" autocomplete="off" placeholder="" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">*打手电话</label>
                                <div class="layui-input-block">
                                    <input type="text" name="hatchet_man_phone" value="{{ $hatchetManBlacklist->hatchet_man_phone }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">*打手QQ</label>
                                <div class="layui-input-block">
                                    <input type="text" name="hatchet_man_qq" value="{{ $hatchetManBlacklist->hatchet_man_qq }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">备注</label>
                                <div class="layui-input-block">
                                    <textarea placeholder="" name="content" class="layui-textarea">{{ $hatchetManBlacklist->content }}</textarea>
                                </div>
                            </div>
                            <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="update" lay-id="{{ $hatchetManBlacklist->id }}">确认</button>
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
         layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            // 取消按钮
            $('.cancel').click(function () {
                window.location.href="{{ route('admin.leveling-blacklist.index') }}";
            });
            // 新增
            form.on('submit(update)', function (data) {
                var id = this.getAttribute('lay-id');
                $.post("{{ route('admin.leveling-blacklist.update') }}", {
                    data:data.field,
                    id:id
                }, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        window.location.href="{{ route('admin.leveling-blacklist.index') }}";
                    }
                });
                return false;
            });
        });  
    </script>
@endsection