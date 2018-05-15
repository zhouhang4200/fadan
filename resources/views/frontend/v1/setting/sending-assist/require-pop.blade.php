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
    .layui-form-select dl {
        max-height: 200px;
    }
</style>
<div class="pop" style="padding: 30px">
    <div class="">
        <div class="" style="width: 300px;height:280px;padding:10px;float: left;border: 1px solid #ccc;overflow-y: auto">
            @forelse($template as $item)
                <div style="height: 25px;line-height: 25px;cursor:default">
                    <span  class="edit" data-id="{{ $item->id }}" data-status="{{ $item->status }}" data-game="{{ $item->game_id }}" data-name="{{ $item->name }}" data-content="{{ $item->content }}">{{ $item->name }} </span>
                    <span style="float: right" class="delete" data-id="{{ $item->id }}">x</span>
                </div>
            @empty
            @endforelse
        </div>
        <div class="" style="width: 300px;float: left">
            <form class="layui-form" action="">
                <input type="hidden" name="id" value="0">
                <div class="layui-form-item">
                    <label class="layui-form-label">游戏</label>
                    <div class="layui-input-block">
                        <select name="game_id" lay-verify="" id="game">
                            <option value="0"></option>
                            @foreach($game as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">模板名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" required  lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">模板内容</label>
                    <div class="layui-input-block">
                        <textarea name="content" placeholder="请输入内容" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否默认</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="status" value="1" lay-skin="switch">
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="qs-btn" lay-submit lay-filter="formDemo" style="height: 28px;line-height: 28px">确定</button>
                        <button type="reset" class="qs-btn reset">重置</button>
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
//            layer.msg(JSON.stringify(data.field));
            $.post('{{ route('frontend.setting.sending-assist.require.pop.store') }}', {
                id:data.field.id,
                game_id:data.field.game_id,
                name:data.field.name,
                status:data.field.status,
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
            selectedGame($(this).data('game'));
        });

        $('.pop').on('click', '.delete', function () {
            var current = $(this).parent();
            var id = $(this).data('id');
            layer.confirm('您确定要删除吗？', {icon: 3, title:'提示'}, function(index){
                $.post('{{ route('frontend.setting.sending-assist.require.destroy') }}', {id:id}, function (result) {
                    if (result.status) {
                        current.remove();
                    }
                }, 'json');
                layer.close(index);
                $('.layui-form')[0].reset()
            });
        });

        $('.pop').on('click', '.reset', function () {
            $('input[name=id]').val(0);
        });

        function selectedGame(value) {
            $("#game").val(value);
            form.render();
        }
    });
</script>