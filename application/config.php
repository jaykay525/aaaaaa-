<?php

return [
	// 'exception_tmpl' => 'public/404.html',
	 'app_debug' => true,
	// 视图输出字符串内容替换
	'view_replace_str'  =>  [
		'__PUBLIC__' => \think\Request::instance()->domain() . '/public/',
		'../public/' => \think\Request::instance()->domain() . '/public/' . BIND_MODULE .'/',
		'__ROOT__'   => \think\Request::instance()->domain() . '/',
	],
	// 'url_domain_deploy' =>  true,
    'url_route_on'  => true,
    'PAGE_SIZE' => 20,
    'MIN_SINGLE_HB_MONEY' => 0.1,//平均单个红包最低金额
    'COMMENT_INTERVAL_TIME' => 3,
    'HB_COMMENT_PRE' => 'HB_COMMENT_USER_',
    //红包剩余领取数量前缀
    'HB_REDIS_PRE' => 'HB_CURRENT_COUNT_',
    //token前缀
    'HB_TOKEN_PRE'=>'HB_TOKEN_',
//    'AES_KEY'=>"D973CD0DDB2F4276A34BA8DBDA1ADD0F365168B16F6BFBEF",//AES加密密钥
    'AES_KEY'=>"D973CD0DD973CD0D",//AES加密密钥
//    'AES_IV'=>"4BC80CF60299700AC80FE95D51D351E5",//AES加密向量
    'AES_IV'=>"4BC80CF64BC80CF6",//AES加密向量
    'WEB_URL'=>'http://ukl.suncco.com',

    //缓存配置
    'cache' =>  [
        // 使用复合缓存类型
        'type'  =>  'complex',
        // 默认使用的缓存
        'default'   =>  [
            // 驱动方式
            'type'   => 'File',
            // 缓存保存目录
            'path'   => CACHE_PATH,
        ],
        // 文件缓存
        'file'   =>  [
            // 驱动方式
            'type'   => 'file',
            // 设置不同的缓存保存目录
            'path'   => RUNTIME_PATH . 'file/',
        ],
        // redis缓存（TODO 后期考虑加密码鉴权）
        'redis'   =>  [
            // 驱动方式
            'type'   => 'redis',
            // 服务器地址
            'host'       => '127.0.0.1',
        ],
    ],
];
