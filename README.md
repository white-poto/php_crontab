Zebra-Crontab
=============
基于PHP的定时任务管理器
------------

使用方式如下：
+ 编写一个任务管理器，可参考test/simple.php
+ 将上述脚本添加到crontab中，一分钟执行一次


**示例：**
```php
<?php
$crontab_config = [
    'test_1' => [
        'name' => '服务监控1',
        'cmd' => 'php -v',
        'output' => '/tmp/test.log',
        'time' => '* * * * *'
    ],
    'single_test' => [
        'name' => 'php -i',
        'cmd' => $single_script,
        'output' => '/tmp/single_script.log',
        'time' => '* * * * *'
    ],
];

$crontab_server = new \Jenner\Zebra\Crontab\Crontab($crontab_config);
$crontab_server->start();
```

[博客地址:www.huyanping.cn](http://www.huyanping.cn/ "程序猿始终不够")



