<?php
/*------------------------------------------------------------------------
 * Admin.php 
 *
 * 管理员验证器
 * 	
 * Created on 2016-12-28
 *
 * Author: ukl.io <ukl.io@139.com>
 * 
 * Copyright (c) 2016 All rights reserved.
 * ------------------------------------------------------------------------
 */

namespace app\admin\validate;

use think\Validate;
use app\common\validate\BaseValidate;

class Admin extends BaseValidate{
	// 验证规则
	protected $rule = [
		'username'   => 'require|min:3|unique:admin',
		'password'   => 'require',
		'repassword' => 'require|confirm:password',
		'realname'   => 'require|min:2',
		'mobile'     => 'require|mobile',
	];
	// 使用场景
	protected $scene = [
		'add'  => ['username', 'email', 'mobile', 'realname'],
		'edit' => ['email', 'mobile', 'realname'],
	];
	// 消息设置
	protected $message = [
		'username.min'    => '用户名必须要3位以上字符',
		'username.unique' => '用户名已存在',
		'realname.min'    => '姓名必须要2位以上',
	];
}