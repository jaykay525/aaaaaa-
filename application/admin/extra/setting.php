<?php
/*------------------------------------------------------------------------
 * setting.php 
 *
 * 配置文件
 * 	
 * Created on 2016-12-29
 *
 * Author: ukl.io <ukl.io@139.com>
 * 
 * Copyright (c) 2016 All rights reserved.
 *
 * 配置项说明
 * ----label		标签
 * ----title		标题
 * ----name			键名
 * ----val			默认值
 * ----description	描述
 * ----val_type		值类型 text、radio、textarea、file
 * ----val_content	选项内容,对radio有效
 * 
 * ------------------------------------------------------------------------
 */
return [
	'setting' => [
		// 系统配置
		'system' => [
			['label' => '基础配置', 'title' => '网站标题6666', 'name' => 'web_title', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '网站关键字', 'name' => 'web_keywords', 'val'=> '', 'description' => '', 'val_type' => 'textarea', 'val_content' => ''],
			['label' => '', 'title' => '网站描述', 'name' => 'web_description', 'val'=> '', 'description' => '', 'val_type' => 'textarea', 'val_content' => ''],
			['label' => '', 'title' => '咨询电话', 'name' => 'service_phone', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '客服QQ', 'name' => 'service_qq', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '客服URL', 'name' => 'service_url', 'val'=> '', 'description' => '', 'val_type' => 'url', 'val_content' => ''],
//			['label' => '', 'title' => '备案号', 'name' => 'web_filing_number', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
//			['label' => '', 'title' => '开放注册', 'name' => 'is_open_register', 'val'=> '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=开放||0=关闭'],
//			['label' => '', 'title' => 'WAP地址', 'name' => 'wap_url', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			
		],

        // 邀请配置
        'invite' => [
            ['label' => '邀请配置', 'title' => '邀请标题', 'name' => 'invite_share_title', 'val' => '', 'description' => '微信邀请标题', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '朋友圈标题', 'name' => 'invite_share_friends_title', 'val' => '', 'description' => '微信邀请朋友圈标题', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '邀请描述', 'name' => 'invite_share_desc', 'val' => '', 'description' => '微信邀请描述', 'val_type' => 'textarea', 'val_content' => ''],
            ['label' => '', 'title' => '邀请图标', 'name' => 'invite_share_icon', 'val' => '', 'description' => '微信邀请图标', 'val_type' => 'file', 'val_content' => ''],
            ['label' => '', 'title' => '复制内容', 'name' => 'invite_share_copytext', 'val' => '', 'description' => '复制内容, #link# 为邀请的链接地址', 'val_type' => 'textarea', 'val_content' => ''],

            ['label' => '糖果配置', 'title' => '标题前缀', 'name' => 'before_title', 'val' => '', 'description' => '分享标题前缀', 'val_type' => 'text', 'val_content' => ''],
        ],

        // 积分配置
        'credit' => [
            ['label' => '积分配置', 'title' => '邀请奖励积分', 'name' => 'invite_award_credit', 'val' => '', 'description' => '邀请成功后，邀请者及被邀请者都可获得多少积分', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '邀请奖励上限', 'name' => 'invite_award_limit', 'val' => '', 'description' => '通过邀请好友获积分的数量上限不超过多少积分', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '积分起兑金额', 'name' => 'credit_exchange_begin', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '积分兑换金额', 'name' => 'credit_exchange_eth', 'val' => '', 'description' => '每100积可兑换的ETH个数,示例值：0.01', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '一级分销奖励', 'name' => 'promote_level_one', 'val' => '', 'description' => '奖励对象：一级，交易每个ETH奖励的积分，示例值：50', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '二级分销奖励', 'name' => 'promote_level_two', 'val' => '', 'description' => '奖励对象：二级，交易每个ETH奖励的积分，示例值：30', 'val_type' => 'text', 'val_content' => ''],
        ],

        //客服配置
        'wxcustom' => [
            ['label' => '客服配置', 'title' => '项目微信客服', 'name' => 'wechat_custom', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '微信客服头像', 'name' => 'wechat_custom_icon', 'val' => '', 'description' => '微信客服头像', 'val_type' => 'file', 'val_content' => ''],

        ],

        //消息推送配置
        'push' => [
            ['label' => '推送配置', 'title' => '是否开启推送', 'name' => 'is_open_push', 'val'=> '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=开启||0=关闭'],
            ['label' => '', 'title' => '邀请成功给直接上级推送的内容', 'name' => 'invite_success_content', 'val' => '', 'description' => '', 'val_type' => 'textarea', 'val_content' => ''],

        ],

        //后端配置
        'admin' => [
            ['label' => '后端配置', 'title' => '浏览量最小值', 'name' => 'min_pv', 'val' => '', 'description' => '系统每日为最近添加的文章自动增加的虚拟访问量最小值，示例值：300', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '浏览量最大值', 'name' => 'max_pv', 'val' => '', 'description' => '系统每日为最近添加的文章自动增加的虚拟访问量最大值，示例值：2000', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '最近更新天数', 'name' => 'update_day', 'val' => '', 'description' => '定义最近更新天数，示例值：5，代表最近五天', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '是否开启自动增加访问量服务', 'name' => 'is_open_autoread', 'val' => '', 'description' => '开启后，系统将在每日4点为最近五天的文章增加随机访问量，访问量规则如上', 'val_type' => 'radio', 'val_content' => '1=开启||0=关闭'],

        ],

        // 电报、微信配置
        'telegram' => [
            ['label' => '电报配置', 'title' => '电报群地址', 'name' => 'telegram_url', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '电报群二维码', 'name' => 'telegram_code', 'val' => '', 'description' => '', 'val_type' => 'file', 'val_content' => ''],

            ['label' => '微信配置', 'title' => '微信昵称', 'name' => 'telegram_wx_name', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '微信号', 'name' => 'telegram_wx_wechat', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '微信头像', 'name' => 'telegram_wx_img', 'val' => '', 'description' => '', 'val_type' => 'file', 'val_content' => ''],
            ['label' => '', 'title' => '微信二维码', 'name' => 'telegram_wx_code', 'val' => '', 'description' => '', 'val_type' => 'file', 'val_content' => ''],
        ],

		// 短信配置
		'sms' => [
            //microsoco(国内)
            ['label' => 'microsoco(国内通道)', 'title' => '账号', 'name' => 'ms_account', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'http://www.microsoco.com/']],
            ['label' => '', 'title' => '企业ID', 'name' => 'ms_userid', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
            ['label' => '', 'title' => '密码', 'name' => 'ms_password', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],

            //isms(国外)
            ['label' => 'isms（国外通道）', 'title' => '账号', 'name' => 'isms_account', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'http://www.isms.com.my/']],
            ['label' => '', 'title' => '密码', 'name' => 'isms_password', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],


            //创蓝短信
			['label' => '创蓝短信', 'title' => '账号', 'name' => 'cl_account', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'http://www.cl2009.com/']],
			['label' => '', 'title' => '密码', 'name' => 'cl_password', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			
			//中国网建
			['label' => '中国网建', 'title' => '账号', 'name' => 'wc_uid', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'http://sms.webchinese.com.cn/']],
			['label' => '', 'title' => 'KEY', 'name' => 'wc_key', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			
			//亿美
			['label' => '亿美', 'title' => 'cdkey', 'name' => 'b2m_cdkey', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '密码', 'name' => 'b2m_password', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			
			//云片网
			['label' => '云片网', 'title' => 'APIKEY', 'name' => 'yunpian_apikey', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'http://www.yunpian.com/']],
			
			//集时通
			['label' => '集时通', 'title' => '账号', 'name' => 'vcomcn_account', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '密码', 'name' => 'vcomcn_password', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			
			//其它配置
			['label' => '其它配置', 'title' => '使用短信类型', 'name' => 'type', 'val'=> '', 'description' => '', 'val_type' => 'radio', 'val_content' => 'cl=创蓝短信||b2m=亿美短信||webchinese=中国网建||yunpian=云片网短信||yunpian_voice=云片网语音||vcomcn=集时通'],
			['label' => '', 'title' => '签名', 'name' => 'signature', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '验证码模板', 'name' => 'smscode_tpl', 'val' => '', 'description' => '#signature#为签名<br>#code#为验证码', 'val_type' => 'textarea', 'val_content' => ''],
            ['label' => '', 'title' => '开启发送日志', 'name' => 'is_record_send_logs', 'val' => '', 'description' => '', 'val_type' => 'radio', 'val_content' => '0=关闭||1=开启'],
            ['label' => '', 'title' => '开启调试模式', 'name' => 'verify_smscode_debug', 'val' => '', 'description' => '开启调试模式后，系统将不下发短信验证码，用户可随意输入任意字符通过验证', 'val_type' => 'radio', 'val_content' => '0=关闭||1=开启'],
            ['label' => '', 'title' => '短信白名单', 'name' => 'smscode_whitelist', 'val' => '', 'description' => '一行一个号码，白名单内的手机号码，系统将不下发短信验证码，用户可随意输入任意字符通过验证（本功能供APPSTORE审核使用）', 'val_type' => 'textarea', 'val_content' => ''],

            //短信提醒时间段
			// ['label' => '短信提醒时间段', 'title' => '开始时间', 'name' => 'start_remind_time', 'val'=> '', 'description' => '开始提醒时间', 'val_type' => 'select', 'val_content' => '08=8点||09=9点||10=10点||11=11点||12=12点||13=13点||14=14点||15=15点||16=16点||17=17点||18=18点||19=19点||20=20点||21=21点||22=22点'],
			// ['label' => '', 'title' => '结束时间', 'name' => 'end_remind_time', 'val' => '', 'description' => '结束提醒时间', 'val_type' => 'select', 'val_content' => '08=8点||09=9点||10=10点||11=11点||12=12点||13=13点||14=14点||15=15点||16=16点||17=17点||18=18点||19=19点||20=20点||21=21点||22=22点'],
		],
		
		//短信过滤
		'filter_sms' => [
			//创蓝短信
			['label' => '过滤手机号', 'title' => '手机号', 'name' => 'filter_mobile', 'val' => '', 'description' => '一行一个手机号', 'val_type' => 'textarea', 'val_content' => '', 'style'=>'height:400px;'],
		],

		//微信配置
		'weixin' => [
			['label' => '公众号配置', 'title' => 'Token', 'name' => 'token', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'https://mp.weixin.qq.com/']],
			['label' => '', 'title' => 'AppID', 'name' => 'appid', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => 'AppSecret', 'name' => 'appsecret', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '关注欢迎词', 'name' => 'welcome', 'val'=> '', 'description' => '', 'val_type' => 'textarea', 'val_content' => ''],
			['label' => 'WEB配置', 'title' => 'AppID', 'name' => 'web_appid', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'https://open.weixin.qq.com/']],
			['label' => '', 'title' => 'AppSecret', 'name' => 'web_appsecret', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			// ['label' => 'WEB-账号中心配置', 'title' => 'AppID', 'name' => 'web_sso_appid', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'https://open.weixin.qq.com/'],
			// ['label' => '', 'title' => 'AppSecret', 'name' => 'web_sso_appsecret', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			// ['label' => 'Android配置', 'title' => 'AppID', 'name' => 'android_appid', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'https://open.weixin.qq.com/'],
			// ['label' => '', 'title' => 'AppSecret', 'name' => 'android_appsecret', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			// ['label' => 'iOS配置', 'title' => 'AppID', 'name' => 'ios_appid', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'https://open.weixin.qq.com/'],
			// ['label' => '', 'title' => 'AppSecret', 'name' => 'ios_appsecret', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			// ['label' => '消息模板', 'title' => '点名模板ID', 'name' => 'rollcall_template_id', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
		],
		//QQ登录配置
		'qq' => [
			['label' => 'QQ登录配置', 'title' => 'APP ID', 'name' => 'appid', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'http://connect.qq.com/']],
			['label' => '', 'title' => 'APP KEY', 'name' => 'appkey', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '回调地址', 'name' => 'callback', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
		],
		//微博登录配置
		'weibo' => [
			['label' => '微博配置', 'title' => 'App Key', 'name' => 'appkey', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => '', 'opt' => ['name' => '申请', 'url' => 'http://open.weibo.com/']],
			['label' => '', 'title' => 'App Secret', 'name' => 'appsecret', 'val'=> '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
		],

		// OSS配置
		'aliyun_oss' => [
			['label' => 'Access Key', 'title' => 'Key ID', 'name' => 'key_id', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => 'Key Secret', 'name' => 'key_secret', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => 'OSS配置', 'title' => 'Bucket', 'name' => 'bucket_name', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => 'Endpoint', 'name' => 'endpoint', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '绑定域名', 'name' => 'file_domain', 'val' => '', 'description' => '', 'val_type' => 'url', 'val_content' => ''],
			['label' => '', 'title' => '图片域名', 'name' => 'img_domain', 'val' => '', 'description' => '', 'val_type' => 'url', 'val_content' => ''],
			['label' => '', 'title' => '删除本地文件', 'name' => 'is_delete_local', 'val' => '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=删除||0=保留'],
			['label' => '', 'title' => '异步上传服务', 'name' => 'is_open', 'val' => '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=启用||0=禁用'],
		],

		//极光配置
		'jpush' => [
			//家长版配置
			['label' => '家长版配置', 'title' => 'AppKey', 'name' => 'parent_app_keys', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => 'Master Secret', 'name' => 'parent_master_secret', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => 'APNS环境', 'name' => 'parent_apns_production', 'val' => '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=生产环境||0=开发环境'],
			//老师版配置
			['label' => '老师版配置', 'title' => 'AppKey', 'name' => 'teacher_app_keys', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => 'Master Secret', 'name' => 'teacher_master_secret', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => 'APNS环境', 'name' => 'teacher_apns_production', 'val' => '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=生产环境||0=开发环境'],
		],

		// 接口配置
		'api' => [
			['label' => '接口配置', 'title' => '接口日志开关', 'name' => 'api_logs_open', 'val' => '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=打开||0=关闭'],
			['label' => '', 'title' => '授权开关', 'name' => 'api_auth_open', 'val' => '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=打开||0=关闭'],
			['label' => 'SaaS配置', 'title' => '启用在线账套', 'name' => 'saas_is_open', 'val' => '', 'description' => '', 'val_type' => 'radio', 'val_content' => '1=启用||0=禁用'],
			['label' => '', 'title' => 'SasS AppID', 'name' => 'saas_appid', 'val' => '', 'description' => '', 'val_type' => 'text', 'val_content' => ''],
			['label' => '', 'title' => '在线账套URL', 'name' => 'online_bill_url', 'val' => '', 'description' => '', 'val_type' => 'url', 'val_content' => ''],
			['label' => '', 'title' => '创建账套URL', 'name' => 'create_books_url', 'val' => '', 'description' => '', 'val_type' => 'url', 'val_content' => ''],
			['label' => '', 'title' => 'SaaS登录URL', 'name' => 'saas_login_url', 'val' => '', 'description' => '', 'val_type' => 'url', 'val_content' => ''],
		],

	],
];