<?php
	echo Route::currentRouteName();
?>
<ul class="seller_center_left_menu">
	<li class="{{ in_array(Route::currentRouteName(), ['frontend.steam.goods.index','frontend.steam.goods.create']) ? 'current' : '' }}">

		<a href="{{ route('frontend.steam.goods.index') }}">审核查询</a>
		<div class="arrow"></div>
	</li>

	<li class="{{ in_array(Route::currentRouteName(), ['frontend.steam.examine.examine-goods']) ? 'current' : '' }}">
		<a href="{{ route('frontend.steam.examine.examine-goods') }}">商品列表</a>
		<div class="arrow"></div>
	</li>

	<li class="{{ in_array(Route::currentRouteName(), ['frontend.steam.cdkey.index','frontend.steam.cdkeylibrary.index']) ? 'current' : '' }}">
		<a href="{{ route('frontend.steam.cdkey.index') }}">CDK列表</a>
		<div class="arrow"></div>
	</li>

	<li class="{{ in_array(Route::currentRouteName(), ['frontend.steam.card.recharge']) ? 'current' : '' }}">
		<a href="{{ route('frontend.steam.card.recharge') }}">机器人账号</a>
		<div class="arrow"></div>
	</li>

	<li class="{{ in_array(Route::currentRouteName(), ['frontend.steam.order.index']) ? 'current' : '' }}">
		<a href="{{ route('frontend.steam.order.index') }}">订单列表</a>
		<div class="arrow"></div>
	</li>

</ul>
