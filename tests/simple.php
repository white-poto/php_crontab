<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-9
 * Time: 下午9:32
 */

define('DS', DIRECTORY_SEPARATOR);
require dirname(__FILE__) . DS . 'vendor' . DS . 'autoload.php';

$single_script = 'php ' . dirname(__FILE__) . DS . 'single.php';
date_default_timezone_set('PRC');

error_reporting(E_ALL);

$crontab_config = [
    'test_1' => [
        'name' => '服务监控1',
        'cmd' => 'echo "111111111111\r\n"',
        'output_file' => '/tmp/test.log',
        'time_rule' => '* * * * *'
    ],
    'single_test' => [
        'name' => '服务监控2',
        'cmd' => $single_script,
        'output_file' => '/tmp/single_script.log',
        'time_rule' => '* * * * *'
    ],
];

$crontab_server = new \Jenner\Zebra\Crontab\Crontab($crontab_config);
$crontab_server->start();
