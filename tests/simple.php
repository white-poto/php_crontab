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

$hello_command = "sleep(1); echo 'world';";
$world_command = "sleep(1); echo 'world';";

$missions = [
    [
        'name' => 'hello',
        'cmd' => "php -r '{$hello_command}'",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    'mission_ls' => [
        'name' => 'world',
        'cmd' => "php -r '{$world_command}'",
        'out' => '/tmp/php_crontab.log',
        'time' => [
            '* * * * *',
        ],
    ],
];
$logger = new \Monolog\Logger("php_crontab");
$logger->pushHandler(new \Monolog\Handler\StreamHandler("/tmp/php_crontab.log"));

$tasks = array();
foreach($missions as $mission){
    $tasks[] = new \Jenner\Crontab\Task(
            $mission['name'],
            $mission['cmd'],
            $mission['time'],
            $mission['out'],
            $mission['user'],
            $mission['group']
        );
}

$crontab_server = new \Jenner\Crontab\Crontab($logger, $tasks);
$crontab_server->start(time());
