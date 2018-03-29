<div class="footer">
    <p>©&nbsp;2017-2018  福禄网络科技有限公司，并保留所有权利。<span class="vol"></span></p>
</div>
<script>
$('#leveling-message').click(function () {
    layer.open({
        title:'代练留言',
        type: 2,
        move: false,
        resize:false,
        scrollbar: false,
        area: ['800px', '500px'],
        content: '{{ route('frontend.message-list') }}'
    });
    return false;
});
// 退款通知
socket.on('notification:orderRefund', function (data) {
    if (data.user_id == {{ Auth::user()->id }}) {
        layer.alert('您有一笔金额' + data.amount + '的退款待处理。<br /><a href="{{ route('home-punishes.index') }}">查看</a>');
    }
});

// 留言数量
socket.on('notification:LevelingMessageQuantity', function (data) {
    if (data.user_id == {{ Auth::user()->id }}) {
        if (data.quantity == 0) {
            $('.leveling-message-quantity').addClass('layui-hide');
        } else {
            $('.leveling-message-quantity').removeClass('layui-hide').html(data.quantity);
        }
    }
});
// 订单数量角标
socket.on('notification:OrderCount', function (data) {
    if (data.user_id == {{ Auth::user()->id }}) {
        if (data.quantity == 0) {
            $('.quantity-' + data.status).addClass('layui-hide');
        } else {
            $('.quantity-' + data.status).removeClass('layui-hide').html(data.quantity);
        }
    }
});
</script>
