<link rel="stylesheet" href="/vendor/layui/css/layui.css">
<link rel="stylesheet" href="/frontend/css/layui-rewrit.css">
<script src="/js/jquery-1.11.0.min.js"></script>
<script src="/vendor/layui/layui.js"></script>
<style>
    .layui-form-label {
        float: left;
        display: block;
        padding: 9px 15px;
        width: 58px;
    }
    .layui-input-block {
        margin-left: 88px;
        min-height: 36px;
    }
</style>
<div class="pop" style="padding: 30px">
    <div class="">
        <div class="" style="width: 300px;height:280px;padding:10px;float: left;border: 1px solid #ccc">
            @forelse($template as $item)
                <div style="height: 25px;line-height: 25px;cursor:default">
                    <span  class="edit" data-id="{{ $item->id }}"  data-name="{{ $item->name }}" data-content="{{ $item->content }}">{{ $item->name }} </span>
                    <span style="float: right" class="delete" data-id="{{ $item->id }}">x</span>
                </div>
            @empty
            @endforelse
        </div>
        <div class="" style="width: 300px;float: left">
            <form class="layui-form" action="">
                <input type="hidden" name="id" value="0">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" required  lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">{{ $type == 1 ? '联系电话' : '联系QQ' }}</label>
                    <div class="layui-input-block">
                        <textarea name="content" placeholder="请输入内容" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formDemo" style="height: 28px;line-height: 28px">确定</button>
                        <button type="reset" class="layui-btn">重置</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
<script>
    //Demo
    var token = '{{ csrf_token() }}';
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': token}});

    layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element', 'upload'], function(){
        var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;

        //监听提交
        form.on('submit(formDemo)', function(data){

            $.post('{{ route('frontend.setting.businessman-contact.store') }}', {
                id:data.field.id,
                name:data.field.name,
                type:data.field.type,
                content:data.field.content
            }, function (result) {
                layer.msg(result.message);
            },'json');
            setTimeout(function () {
                location.reload();//当前页面
            }, 500);
            return false;
        });

        $('.pop').on('click', '.edit', function () {
            var id = $(this).data('id');
            $('input[name=id]').val($(this).data('id'));
            $('input[name=name]').val($(this).data('name'));
            $('textarea[name=content]').val($(this).data('content'));
            if ($(this).data('status') == 1) {
                $('input:checkbox[name=status]').prop('checked', true);
            } else {
                $('input:checkbox[name=status]').prop('checked', false);
            }
            form.render();
        });

        $('.pop').on('click', '.delete', function () {
            var current = $(this).parent();
            var id = $(this).data('id');
            layer.confirm('您确定要删除吗？', {icon: 3, title:'提示'}, function(index){
                $.post('{{ route('frontend.setting.businessman-contact.destroy') }}', {id:id}, function (result) {
                    if (result.status) {
                        current.remove();
                    }
                }, 'json');
                layer.close(index);
                $('.layui-form')[0].reset()
            });
        });

        function selectedGame(value) {
            $("#game").val(value);
            form.render();
        }
    });
</script>