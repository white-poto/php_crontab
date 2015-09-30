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
    'mission_1' => [
        'name' => 'ls_tmp',
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

$crontab_server = new \Jenner\Crontab\Crontab($crontab_config);
$crontab_server->start(time());
