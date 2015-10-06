php_crontab 
=============
[![Total Downloads](https://img.shields.io/packagist/dt/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)
[![Latest Stable Version](http://img.shields.io/packagist/v/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)
[![License](https://img.shields.io/packagist/l/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)

php crontab base on pcntl and libev

[中文说明](https://github.com/huyanping/php_crontab/blob/master/README.zh.md "中文说明")

Why use php_crontab
------------
When we have a handful of crontab task, crontab service is enough for us to manager them. 
If we have many crontab task, there will be some problerm like:
+ The crontab tasks is managed in a text file. If there is no comment, it will be 
hard for fresh man to understand what is it.
+ If the crontab task is distributed in different servers, it is hard to manage them.
+ If you want to collect logs of crontab tasks, it will not be easy. 
+ Different task of users must written in different files.
Based on the above reasons, we need a crontab manager which can manage crontab tasks together and configure the tasks.


Properties
-----------
+ The crontab tasks can be stored in any way you what. For example, mysql, reids. 
What's more? You can develop a web application to manage them.
+ Logs of the crontab tasks can be configured as you want.
+ The tasks of different users can be managed together.


How to use it?
---------------
There is two ways to use php_crontab to manage you crontab tasks. 
You can just write a php script and add it to the crontab config file 
with commnad `crontab -e`. The php script should run every minute. For example `tests/simple.php`
Or you can write a php daemon which will run as a service and will not exit until someone kill it.
It will check the tasks every minute. For example `tests/daemon.php`

Properties
-----------
+ Multi-Process, every task is a process.
+ You can set the user and group of a crontab task
+ You can set more than one time configs to one crontab task.
+ STDOUT can be redirected
+ Based on libev, it can run as a daemon.

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
```



[blog:www.huyanping.cn](http://www.huyanping.cn/ "程序猿始终不够")



