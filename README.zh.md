php_crontab 
=============
[![Total Downloads](https://img.shields.io/packagist/dt/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)
[![Latest Stable Version](http://img.shields.io/packagist/v/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)
[![License](https://img.shields.io/packagist/l/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)

基于pcntl和react/event-loop的定时任务管理器

[英文说明](https://github.com/huyanping/php_crontab/blob/master/README.md "英文说明")

为什么使用php_crontab？
------------
当我们有少量的定时任务需要管理时，unix的crontab服务时足够的。如果我们有非常多的定时任务
需要管理时，机会有一些问题，例如：
+ crontab服务通过一个文本文件管理定时任务，如果没有注释，对新人来说去理解他们是比较难的。
+ 如果定时任务分散在许多机器上，管理他们也是比较难的。
+ 如果你想收集他们的日志，同样不会简单。
+ 不同用户的定时任务分散在不同的文件中。
基于以上几点原因，我们需要一个可以统一管理配置的定时任务管理器。

如何使用php_crontab？
---------------
有两种方式使用php_crontab管理你的定时任务。
你可以写一个脚本，然后把它加入到crontab服务器中，每分钟执行一次。例如`tests/simple`。
或者你可以写一个守护进程脚本，它会像一个服务一样一只运行，直到你杀死它。
它将每分钟检查一次定时任务。例如`tests/daemon.php`

特性
-----------
+ 定时任务管理可以被存储在任何地方。例如：mysql、redis等。
+ 定时任务的日志可以根据你的需要进行配置
+ 多个用户的定时任务可以统一管理
+ 多进程，每个任务一个进程
+ 你可以为每个任务设置用户和用户组
+ 标准输出可以进行重定向
+ 基于react/event-loop，它可以作为一个守护进程运行
+ 一个HTTP服务器，你可以通过它管理定时任务

HTTP 接口
-------------
HTTP 方法: `GET`  
+ `add` 增加任务
+ `get_by_name` 根据任务名称获取任务
+ `remove_by_name` 根据任务名称删除任务
+ `clear` 删除所有任务
+ `get` 获取所有任务
+ `start` 开始检测定时任务
+ `stop` 停止检测定时任务

示例:
```shell
http://host:port/add?name=name&cmd=cmd&time=time&out=out&user=user&group=group&comment=comment
http://host:port/get_by_name?name=name
http://host:port/remove_by_name?name=name
http://host:port/clear
http://host:port/get
http://host:port/start
http://host:port/stop
```


**基于crontab服务运行**
```shell
* * * * * php demo.php
```
```php
<?php
$missions = [
    [
        'name' => 'ls',
        'cmd' => "ls -al",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
];

$tasks = array();
foreach($missions as $mission){
    $tasks[] = new \Jenner\Crontab\Mission($mission['name'], $mission['cmd'], $mission['time'], $mission['out']);
}

$crontab_server = new \Jenner\Crontab\Crontab(null, $tasks);
$crontab_server->start(time());
```
**作为一个守护进程运行**

it will check the task configs every minute.
```php
$missions = [
    [
        'name' => 'ls',
        'cmd' => "ls -al",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => '/tmp/php_crontab.log',
        'time' =>  '* * * * *',
    ],
];

$daemon = new \Jenner\Crontab\Daemon($missions);
$daemon->start();
```

**作为守护进程运行同时启动一个http server**
```php
$missions = [
    [
        'name' => 'ls',
        'cmd' => "ls -al",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
];

$http_daemon = new \Jenner\Crontab\HttpDaemon($missions, "php_crontab.log");
$http_daemon->start($port = 6364);
```
Then you can manage the crontab task by curl like:
```shell
curl http://127.0.0.1:6364/get_by_name?name=ls
curl http://127.0.0.1:6364/remove_by_name?name=hostname
curl http://127.0.0.1:6364/get
```

**启动脚本**
```shell
[root@jenner php_crontab]# ./bin/php_crontab 
php_crontab help:
-c  --config    crontab tasks config file
-p  --port      http server port
-f  --pid-file  daemon pid file
-l  --log       crontab log file
[root@jenner php_crontab]#nohup ./bin/php_crontab -c xxoo.php -p 8080 -f /var/php_crontab.pid -l /var/logs/php_crontab.log >/dev/null & 
```

[blog:www.huyanping.cn](http://www.huyanping.cn/ "程序猿始终不够")



