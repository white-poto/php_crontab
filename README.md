php_crontab 
=============
[![Total Downloads](https://img.shields.io/packagist/dt/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)
[![Latest Stable Version](http://img.shields.io/packagist/v/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)
[![License](https://img.shields.io/packagist/l/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)

php crontab base on pcntl and react/event-loop

[中文说明](https://github.com/huyanping/php_crontab/blob/master/README.zh.md "中文说明")

Why use php_crontab
------------
When we have a handful of crontab tasks, crontab service is enough for us to manage them. 
If we have many crontab tasks, there will be some problems like:
+ The crontab tasks are managed in a text file. If there are no comment, it will be 
hard for fresh man to understand what they are.
+ If the crontab tasks are distributed in different servers, it will be hard to manage them.
+ If you want to collect the crontab tasks' logs, it will not be easy. 
+ Tasks of different users must written in different files.
Based on the above reasons, we need a crontab manager which can manage crontab tasks together and configure the tasks.

How to use it?
---------------
There are two ways to use php_crontab to manage you crontab tasks. 
You can just write a php script and add it to the crontab config file 
with the command `crontab -e`. The php script should run every minute. For example `tests/simple.php`
Or you can write a php daemon script which will run as a service and will not exit until someone kill it.
It will check the tasks every minute. For example `tests/daemon.php`

Properties
-----------
+ The crontab tasks can be stored in any way you what. For example, mysql, reids. 
What's more? You can develop a web application to manage them.
+ Logs of the crontab tasks can be configured as you want.
+ The tasks of different users can be managed together.
+ Multi-Process, every task is a process.
+ You can set the user and group of a crontab task
+ You can set more than one time configs to one crontab task.
+ STDOUT can be redirected
+ Based on react/event-loop, it can run as a daemon.
+ A HTTP server which you can manage the crontab tasks through it.

TODO
-------------
+ Add http interface to manage the tasks configs.


**run based on crontab service：**
```shell
* * * * * php demo.php
```
```php
<?php
$crontab_config = [
    'test_1' => [
        'name' => 'test_1', //task name
        'cmd' => 'php -v', // cli command
        'output' => '/tmp/test.log', // output file
        'time' => '* * * * *', //time config
        'user_name' => 'www', //user
        'group_name' => 'group_name', // group
    ],
    'single_test' => [
        'name' => 'php -i',
        'cmd' => 'php -i',
        'output' => '/tmp/single_script.log',
        'time' => [
            '* * * * *',
            '* * * * *',
        ],
    ],
];

$time = time();
$crontab_server = new \Jenner\Zebra\Crontab\Crontab($crontab_config);
$crontab_server->start($time);
```
**run as a daemon**

it will check the task configs every minute.
```php
$crontab_config = [
    'test_1' => [
        'name' => 'test_1',
        'cmd' => 'php -r "echo "11111" . PHP_EOL;sleep(60);"',
        'output' => '/www/test.log',
        'time' => '* * * * *',
        'user_name' => 'www',
        'group_name' => 'www'
    ],
    'single_test' => [
        'name' => 'php -i',
        'cmd' => 'php -i',
        'output' => '/tmp/single_script.log',
        'time' => [
            '* * * * *',
            '* * * * *',
        ],
    ],
];

$daemon = new \Jenner\Zebra\Crontab\Daemon($crontab_config, "logfile.log");
$daemon->start();

**run as aa daemon and start the http server**
```php
error_reporting(E_ALL);

$hello_command = "echo \"hello \";";
$world_command = "sleep(1); echo \"world\";";

$missions = [
    [
        'name' => 'hello',
        'cmd' => "php -r '{$hello_command}'",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
    [
        'name' => 'world',
        'cmd' => "php -r '{$world_command}'",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
];

$http_daemon = new \Jenner\Crontab\HttpDaemon($missions, "php_crontab.log");
$http_daemon->start($port = 6364);
```
Then you can manage the crontab task by curl like:
```shell
curl http://127.0.0.1:6364/get_by_name?name=hello
curl http://127.0.0.1:6364/remove_by_name?name=hello
curl http://127.0.0.1:6364/missions
```

**run the script**
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



