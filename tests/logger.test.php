<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2016/7/9
 * Time: 9:29
 */

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$logger = new \Monolog\Logger(new \Monolog\Handler\StreamHandler("/tmp/monolog.log"));
$logger->info("test");