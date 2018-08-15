<?php
/*------------------------------------------------------------------------
 * BaseValidate.php 
 *
 * 验证器基类
 * 	
 * Created on 2016-12-28
 *
 * Author: ukl.io <ukl.io@139.com>
 * 
 * Copyright (c) 2016 All rights reserved.
 * ------------------------------------------------------------------------
 */

namespace app\common\validate;

use think\Validate;

class BaseValidate extends Validate{
	
	// 自定义手机号验证规则
    protected function mobile($value, $rule, $data){
    	$is_ok = preg_match("/^((13[0-9])|(14[0-9])|(15[^4,\\D])|(17[0-9])|(18[0-9])|(19[0-9]))\\d{8}$/", $value);
        return $is_ok ? true : '手机号不正确';
    }
}