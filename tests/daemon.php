<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/5
 * Time: 21:23
 */

date_default_timezone_set('PRC');
define('DS', DIRECTORY_SEPARATOR);
require dirname(__FILE__) . DS . 'vendor' . DS . 'autoload.php';

error_reporting(E_ALL);

$crontab_config = [
    'test_1' => [
        'name' => '·şÎñ¼à¿Ø1',
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