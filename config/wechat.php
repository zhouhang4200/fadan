<?php

return [
	'base_config' => [
		'app_id'      => "wxd741646d2b519c60",
		'key'         => 'c27656886d284e71a3d87aa1fce03ed1',
		'mch_id'      => '1502886431',
//		'notify_url'  => 'http://js.qsios.com/mobile/leveling/wechat/notify',
		 'notify_url'  => 'http://www.tm.com/channel/wechat-notify',
		'cert_client' => public_path('resources/wechat/apiclient_cert.pem'),
		'cert_key'    => public_path('resources/wechat/apiclient_key.pem'), 
	],
//	'return_url'  => 'http://js.qsios.com/mobile/leveling/wechat/return',
	 'return_url'  => 'http://ww.tm.com/channel/wechat-return',

	'find_config' => [
		'app_id'      => "wxd741646d2b519c60",
		'mch_id'      => '1502886431',
		'key'         => 'c27656886d284e71a3d87aa1fce03ed1',
	],
];