    您在淘宝发单平台接到的订单现遭到商户投诉，如有异议，请在24小时内回复此邮件并提供证据。如无异议则不需回复，24小时后我们将人工处理此订单，进行赔偿。赔偿方法为：扣除您在淘宝发单平台上的余额，转移到商户账号余额，望知悉。

<p>订单号：{{ $order }}</p>

<p>要求赔偿金额：{{ $amount }}</p>

<p>投诉原因：{{ $remark }}</p>

截图：
@if($image1)
    <img src="{{ $image1 }}">
@endif
<br/>
@if($image2)
    <img src="{{ $image2 }}">
@endif
<br/>
@if($image3)
    <img src="{{ $image3 }}">
@endif