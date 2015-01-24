<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-9
 * Time: 下午9:32
 */


date_default_timezone_set('PRC');
define('DS', DIRECTORY_SEPARATOR);
require dirname(__FILE__) . DS . 'vendor' . DS . 'autoload.php';


error_reporting(E_ALL);

$crontab_config = [
    'test_1' => [
        'name' => '服务监控1',
        'cmd' => 'php -r "echo "11111" . PHP_EOL;sleep(60);"',
        'output' => '/www/test.log',
        'time' => '* * * * *',
        'user_name' => 'mysql',
        'group_name' => 'mysql'
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

$crontab_server = new \Jenner\Zebra\Crontab\Crontab($crontab_config);
$crontab_server->start();
