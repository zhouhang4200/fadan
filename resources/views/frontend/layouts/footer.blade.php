<div class="footer">
    <p>©&nbsp;2017-2018  福禄网络科技有限公司，并保留所有权利。<span class="vol"></span></p>
</div>
<script>
// 退款通知
socket.on('notification:orderRefund', function (data) {
    if (data.user_id == {{ Auth::user()->id }}) {
        layer.alert('您有一笔金额' + data.amount + '的退款待处理。<br /><a href="{{ route('home-punishes.index') }}">查看</a>');
    }
});
</script>
