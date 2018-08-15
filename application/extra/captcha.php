<?php
/***
 * 验证码配置文件
 */

return [
    // 验证码字符集合
    'codeSet'  => '23456789',
    // 验证码字体大小(px)
    'fontSize' => 25,
    // 是否画混淆曲线
    'useCurve' => false,
    // 是否添加杂点
    'useNoise' => true,
    // 验证码位数
    'length'   => 5,
    // 验证成功后是否重置
    'reset'    => true,
];
