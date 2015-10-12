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

$hello_command = "echo \"hello \";";
$world_command = "sleep(1); echo \"world\";";

$missions = [
    [
        'name' => 'hello',
        'cmd' => "php -r '{$hello_command}'",
        'out' => '/tmp/www_php_crontab.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    'mission_ls' => [
        'name' => 'world',
        'cmd' => "php -r '{$world_command}'",
        'out' => '/tmp/php_crontab.log',
        'time' =>  '* * * * *',
    ],
];

$daemon = new \Jenner\Crontab\Daemon($missions);
$daemon->start();