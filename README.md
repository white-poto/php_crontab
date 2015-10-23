php_crontab 
=============
[![Total Downloads](https://img.shields.io/packagist/dt/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)
[![Latest Stable Version](http://img.shields.io/packagist/v/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)
[![License](https://img.shields.io/packagist/l/jenner/crontab.svg?style=flat)](https://packagist.org/packages/jenner/crontab)

php crontab base on pcntl and react/event-loop

[中文说明](https://github.com/huyanping/php_crontab/blob/master/README.zh.md "中文说明")

Why use php_crontab?
------------
When we have a handful of crontab tasks, crontab service is enough for us to manage them. 
If we have many crontab tasks, there will be some problems like:
+ The crontab tasks are managed in a text file. If there are no comment, it will be 
hard for fresh man to understand what they are.
+ If the crontab tasks are distributed in different servers, it will be hard to manage them.
+ If you want to collect the crontab tasks' logs, it will be not easy. 
+ Tasks of different users must written in different files.
Based on the above reasons, we need a crontab manager which can manage crontab tasks together and configure the tasks.

How to use php_crontab?
---------------
First `composer require jenner/crontab`.    
There are two ways to use php_crontab to manage your crontab tasks. 
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
+ STDOUT can be redirected
+ Based on react/event-loop, it can run as a daemon.
+ A HTTP server which you can manage the crontab tasks through it.

Output Config
-----------
You can redirect the output(stdout and stderr) to anywhere you what, like:
+ `file:///path/to/file` 
+ `unix:///path/to/sock`
+ `tcp://host:port`
+ `udp://host:port`
+ `redis://host:port/queue_key`
+ `http://host:port/path`
+ `custom://namespace\\class_name?params`  
  
Note that the custom class must be an instance of `\Monolog\Handler\HandlerInterface`, 
and you can pass params to your custom class's `__construct` by query string.

HTTP interfaces
-------------
HTTP METHOD: `GET`  
+ `add` add new task to crontab server
+ `get_by_name` get task by name
+ `remove_by_name` remove task by name
+ `clear` clear all task
+ `get` get all tasks
+ `start` start crontab loop
+ `stop` stop crontab loop

Examples:
```shell
http://host:port/add?name=name&cmd=cmd&time=time&out=out&user=user&group=group&comment=comment
http://host:port/get_by_name?name=name
http://host:port/remove_by_name?name=name
http://host:port/clear
http://host:port/get
http://host:port/start
http://host:port/stop
```

TODO
------------------
+ add log handler interface
+ add http log handler, socket log handler, file handler and so on.
+ separate stdout and stderr. use different handlers


**run based on crontab service**
```shell
* * * * * php demo.php
```
```php
<?php
$missions = [
    [
        'name' => 'ls',
        'cmd' => "ls -al",
        'out' => 'file:///tmp/php_crontab.log',
        'err' => 'file:///tmp/php_crontab.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => 'unix:///tmp/php_crontab.sock',
        'err' => 'unix:///tmp/php_crontab.sock',
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
**run as a daemon**

it will check the task configs every minute.
```php
$missions = [
    [
        'name' => 'ls',
        'cmd' => "ls -al",
        'out' => 'file:///tmp/php_crontab.log',
        'err' => 'file:///tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => 'unix:///tmp/php_crontab.sock',
        'err' => 'unix:///tmp/php_crontab.sock',
        'time' => '* * * * *',
    ],
];

$daemon = new \Jenner\Crontab\Daemon($missions);
$daemon->start();
```

**run as a daemon and start the http server**
```php
$missions = [
    [
        'name' => 'ls',
        'cmd' => "ls -al",
        'out' => 'file:///tmp/php_crontab.log',
        'err' => 'file:///tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => 'unix:///tmp/php_crontab.sock',
        'err' => 'unix:///tmp/php_crontab.sock',
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



