<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-9
 * Time: 下午10:23
 */

define('DS', DIRECTORY_SEPARATOR);
require dirname(__FILE__) . DS . 'vendor' . DS . 'autoload.php';
error_reporting(E_ALL);

try{
    $single = new \Jenner\Zebra\Daemon\DaemonSingle(__FILE__);
    $single->single();
}catch (\Exception $e){
    echo $e->getMessage();
}


echo 'I am a single process' . PHP_EOL;