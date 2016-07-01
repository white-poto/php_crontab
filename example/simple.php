<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-9
 * Time: 下午9:32
 */


date_default_timezone_set('PRC');
define('DS', DIRECTORY_SEPARATOR);
require dirname(dirname(__FILE__)) . DS . 'vendor' . DS . 'autoload.php';

error_reporting(E_ALL);

$missions = [
    [
        'name' => 'ls',
        'cmd' => "ls -al",
        'out' => '/tmp/ls-al.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => '/tmp/hostname.log',
        'time' =>  '* * * * *',
    ],
];



$tasks = array();
foreach($missions as $mission){
    $logger = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($mission['out']));
    $tasks[] = new \Jenner\Crontab\Mission(
            $mission['name'],
            $mission['cmd'],
            $mission['time'],
            $logger
        );
}

$logger = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
$logger->pushHandler(new \Monolog\Handler\StreamHandler("/var/log/php_crontab.log"));

$crontab_server = new \Jenner\Crontab\Crontab($logger, $tasks);
$crontab_server->start(time());
