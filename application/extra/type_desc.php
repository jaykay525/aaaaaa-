<?php
return [
	// 通用状态说明
	'status_desc' => [
		0 => '禁用',
		1 => '启用',
	],
	// 终端类型
	'pay_type_desc' => [
		'kuaiqian'    => '快钱支付',
		'alipay'      => '支付宝',
		'upmp'        => '银联支付',
		'weixin'      => '微信支付',
		'balance_pay' => '余额支付',
		'cash'        => '现金支付',
		'pos'         => 'POS支付',
		'other'       => '其它支付',
		'offline_pay' => '线下支付',
		'system'      => '系统',
	],
	// 终端类型
	'app_type_desc' => [
		'system'   => '系统',
		'ios'      => 'iOS',
		'android'  => 'Android',
		'web'      => 'Web',
		'wap'      => 'Wap',
		'mac'      => 'Mac',
		'weixin'   => 'Weixin',
		'wxopen'   => '微信小程序',
		'windows'  => 'Windows',
		'linux'    => 'Linux',
		'unknown'  => '未知',
		'saas_web' => 'SaaS Web',
		'saas_wap' => 'SaaS Wap',
	],
	// 订单类型
	'order_type_desc' => [
		'product_order'  => '产品订单',
		'recharge_order' => '充值订单',
		
	],
	// 支付订单类型
	'pay_order_type_desc' => [
		'product_order'  => '支付单',
		'recharge_order' => '充值单',
		
	],
	// 订单状态
	'order_status_desc' => [
		'wait_confirm' => '待确认',
		'wait_pay'     => '等待付款',
		'offline_pay'  => '等待核对',
		'payed'        => '进行中',// 已付款
		'dispatch'     => '进行中',// 已派单
		'doing'        => '进行中',// 进行中
		'finish'       => '已完成',
		'cancel'       => '已取消',
		'refund'       => '已退款',
	],
	// 订单状态(后台使用)
	'order_status_admin_desc' => [
		'wait_confirm' => '待确认',
		'wait_pay'     => '等待付款',
		'offline_pay'  => '等待核对',
		'payed'        => '已付款',
		'dispatch'     => '已派单',
		'doing'        => '进行中',
		'finish'       => '已完成',
		'cancel'       => '已取消',
		'refund'       => '已退款',
	],
	// 订单状态颜色
	'order_status_color' => [
		'wait_confirm' => '#d32541',
		'wait_pay'     => '#ff7100',
		'offline_pay'  => '#ff7100',
		'payed'        => '#00beb7',// 已付款
		'dispatch'     => '#00beb7',// 已派单
		'doing'        => '#00beb7',// 进行中
		'finish'       => '#c8c8c8',
		'cancel'       => '#c8c8c8',
		'refund'       => '#c8c8c8',
	],
	// 账户类型
	'account_type_desc' => [
		'usable_account'  => '可用账户',
		'used_account'    => '已用账户',
		'blocked_account' => '托管账户',
	],
	// 账单类型
	'bill_type_desc' => [
		'pay_product_order' => '支付订单',
		'recharge'          => '充值',
		'take_out'          => '提现',
		'take_out_refund'   => '提现退款',
		'cancel_order'      => '取消订单',
		'order_refund'      => '订单退款',
	],
	'take_out_status_desc' => [
		1 => '等待审核',
		4 => '受理中',
		2 => '提现成功',
		3 => '拒绝提现',
	],
	'audit_status_desc' => [
		'0'      => '等待审核',
		'1'      => '审核通过',
		'2'      => '审核不通过',

		'wait'   => '等待审核',
		'pass'   => '审核通过',
		'reject' => '审核不通过',
	],
	'audit_status_color' => [
		'0'      => '#ff7100',
		'1'      => '#00beb7',
		'2'      => '#FF3C00',

		'wait'   => '#ff7100',
		'pass'   => '#00beb7',
		'reject' => '#FF3C00',
	],
	// 性别
	'sex_desc' => [
		0 => '未设置', 
		1 => '男', 
		2 => '女',
		3 => '不限',
	],
	// 短信类型说明
	'sms_type_desc' => [
	    'ms'            => 'microsoco（国内）',
        'isms'          => 'isms(国外)',
		'cl'            => '创蓝短信',
		'webchinese'    => '中国网建',
		'b2m'           => '亿美',
		'yunpian'       => '云片网短信',
		'yunpian_voice' => '云片网语音',
		'vcomcn'        => '集时通',
	]
];