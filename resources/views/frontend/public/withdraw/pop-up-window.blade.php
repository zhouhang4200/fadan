<button id="withdraw" class="{{ $domClass }}" type="button" style="{{ $domStyle }}">{{ $bottomText }}</button>

<div id="withdraw-box" style="display: none;padding: 20px 60px 20px 0;">
    <div class="layui-form-item" style="margin-bottom: 15px;">
        <label class="layui-form-label">提现金额</label>
        <div class="layui-input-block">
            <input type="text" name="fee" class="layui-input" placeholder="可提现金额 {{ Auth::user()->userAsset->balance }}">
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom: 15px;">
        <label class="layui-form-label">备注说明</label>
        <div class="layui-input-block">
            <input type="text" name="remark" class="layui-input" placeholder="可留空">
        </div>
    </div>
    <div id="template"></div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button id="withdraw-submit" class="layui-btn layui-bg-blue" type="button">提交</button>
        </div>
    </div>
</div>

<script>

layui.use(['layer'], function () {
    var layer = layui.layer;
});

$('#withdraw').click(function () {
    layer.open({
        type: 1,
        title: '提现单',
        area: ['350px', '240px'],
        content: $('#withdraw-box')
    });
});

$('#withdraw-submit').click(function () {
    var loading = layer.load(2, {shade: [0.1, '#000']});

    $.ajax({
        url: "{{ route('frontend.finance.withdraw-order.store') }}",
        type: 'POST',
        dataType: 'json',
        data: {
            fee: $('[name="fee"]').val(),
            remark: $('[name="remark"]').val()
        },
        error: function (data) {
            layer.close(loading);
            var responseJSON = data.responseJSON.errors;
            for (var key in responseJSON) {
                layer.msg(responseJSON[key][0]);
                break;
            }
        },
        success: function (data) {
            layer.close(loading);
            if (data.status === 1) {
                layer.alert('操作成功', function () {
                    location.href = "{{ route('frontend.finance.withdraw-order') }}";
                });
            } else {
                layer.alert(data.message);
            }
        }
    });
});
</script>