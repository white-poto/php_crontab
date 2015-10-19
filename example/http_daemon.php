<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/7
 * Time: 9:18
 */

date_default_timezone_set('PRC');
define('DS', DIRECTORY_SEPARATOR);
require dirname(dirname(__FILE__)) . DS . 'vendor' . DS . 'autoload.php';

error_reporting(E_ALL);


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
$http_daemon->start();