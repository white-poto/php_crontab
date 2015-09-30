<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/5
 * Time: 21:23
 */

date_default_timezone_set('PRC');
define('DS', DIRECTORY_SEPARATOR);
require dirname(dirname(__FILE__)) . DS . 'vendor' . DS . 'autoload.php';

error_reporting(E_ALL);

$missions = [
    'mission_1' => [
        'name' => 'hello',
        'cmd' => 'ls /tmp',
        'out' => '/tmp/ls_tmp.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    'mission_2' => [
        'name' => 'ls',
        'cmd' => 'ls -al',
        'out' => '/tmp/ls_al.log',
        'time' => [
            '* * * * *',
            '1 * * * *',
        ],
    ],
];

$daemon = new \Jenner\Crontab\Daemon($crontab_config, "logfile.log");
$daemon->start();