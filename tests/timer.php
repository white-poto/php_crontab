<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2016/7/13
 * Time: 11:38
 */


define('DS', DIRECTORY_SEPARATOR);
require dirname(dirname(__FILE__)) . DS . 'vendor' . DS . 'autoload.php';

$loop = \React\EventLoop\Factory::create();
$loop->addPeriodicTimer(60, function() {
   while(true) {
       echo "function A", PHP_EOL;
       sleep(1);
   }
});

$loop->addPeriodicTimer(60, function() {
    while(true) {
        echo "function B", PHP_EOL;
        sleep(1);
    }
});