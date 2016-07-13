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
$loop->addPeriodicTimer(10, function() {
   for($i=0; $i<3; $i++) {
       echo "function A", PHP_EOL;
       sleep(1);
   }
});

$loop->addPeriodicTimer(10, function() {
    for($i=0; $i<3; $i++) {
        echo "function B", PHP_EOL;
        sleep(1);
    }
});

$loop->run();