<?php
define('CONFIG_PATH', __DIR__);

return $config = [
    'worker' => [
        'queue' => [
            'host' => '127.0.0.1',
            'user' => 'user1',
            'pass' => 'changeme',
            'port' => 5672,
            'name' => 'task_queue'
        ],
        'php_path' => '/usr/bin/php',
        'proc_name' => 'woolworks worker',
        'log_dir' => CONFIG_PATH . '/log',
        'log_file' => CONFIG_PATH . '/log/worker.log',
        'pid_file' => CONFIG_PATH . '/log/woolworks-worker.pid',
    ],
    'websockd' => [
        'proc_name' => 'woolworks websockd',
        'log_dir' => CONFIG_PATH . '/log',
        'log_file' => CONFIG_PATH . '/log/websockd.log',
        'pid_file' => CONFIG_PATH . '/log/woolworks-websockd.pid',
    ]
];
