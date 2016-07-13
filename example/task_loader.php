<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2016/7/13
 * Time: 12:00
 */

date_default_timezone_set('PRC');
define('DS', DIRECTORY_SEPARATOR);
require dirname(dirname(__FILE__)) . DS . 'vendor' . DS . 'autoload.php';

function task_loader() {
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
            'name' => 'ls',
            'cmd' => "ls -al",
            'out' => '/tmp/php_crontab.log',
            'time' => '* * * * *',
            'user' => 'www',
            'group' => 'www'
        ],
    ];

    return $missions;
}


$daemon = new \Jenner\Crontab\Daemon();
$daemon->registerTaskLoader("task_loader");
$daemon->start();