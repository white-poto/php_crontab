<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-9
 * Time: 下午9:32
 */

define('DS', DIRECTORY_SEPARATOR);
require dirname(__FILE__) . DS . 'vendor' . DS . 'autoload.php';
date_default_timezone_set('PRC');

$crontab_config = [
    'test_1' => [
        'name' => '服务监控1',
        'cmd' => 'echo "111111111111\r\n"',
        'output_file' => '/tmp/test.log',
        'time_rule' => '* * * * *'
    ],
    'test_2' => [
        'name' => '服务监控2',
        'cmd' => 'echo "2222222222\r\n"',
        'output_file' => '/tmp/test.log',
        'time_rule' => '* * * * *'
    ],
    'test_3' => [
        'name' => '服务监控3',
        'cmd' => 'echo "333333333\r\n"',
        'output_file' => '/tmp/test.log',
        'time_rule' => '* * * * *'
    ],
];

$crontab_server = new \Jenner\Zebra\Crontab\Crontab($crontab_config);
$crontab_server->start();
