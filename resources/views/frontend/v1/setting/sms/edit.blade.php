<div id="template-edit-popup" style="padding: 20px">
    <form class="layui-form" action="" id="template-add-form">
        <input type="hidden" name="id" value="{{ $template->id }}">
        <input type="hidden" name="type" value="{{ $template->type }}">
        <div class="layui-form-item">
            <input type="text" name="name" required lay-verify="required" placeholder="模板名称"
                   autocomplete="off" class="layui-input" value="{{ $template->name }}">
        </div>
        <div class="layui-form-item layui-form-text">
            <textarea name="contents" placeholder="短信内容" class="layui-textarea">{{ $template->contents }}</textarea>
        </div>
        <div class="layui-form-item">
            <button class="qs-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="template-edit-save">保存修改
            </button>
            <button type="button" class="qs-btn layui-btn-danger cancel">取消编辑</button>
        </div>
    </form>
</div>