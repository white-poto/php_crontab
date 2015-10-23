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
        'out' => 'file:///tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => 'unix:///tmp/php_crontab.sock',
        'time' =>  '* * * * *',
    ],
];
$logger = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
$logger->pushHandler(new \Monolog\Handler\StreamHandler("/var/log/php_crontab.log"));

$http_daemon = new \Jenner\Crontab\HttpDaemon($missions, $logger);
$http_daemon->start();