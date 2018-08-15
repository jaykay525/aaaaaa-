<?php
//配置文件
return [
    'log'                    => [
        // 日志记录方式，支持 file socket
        'type' => 'File',
        // 日志记录级别，使用数组表示
        'level' => ['info'],
        // 日志保存目录
        'path' => LOG_PATH,
        // 日志记录级别，使用数组表示
        'single' => true,
    ],
];