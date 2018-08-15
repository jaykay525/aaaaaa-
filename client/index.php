<?php
/**
 *
 * +------------------------------------------------------------+
 * ukl.io 后端服务
 * +------------------------------------------------------------+
 *
 * @copyright http://www.ukl.io
 * @version 1.0
 *
 * Create at : 2018/5/29 上午11:43
 *
 */

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 绑定当前访问到index模块
define('BIND_MODULE','client');

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
