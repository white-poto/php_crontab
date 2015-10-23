<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/23
 * Time: 9:17
 */
class MissionLoggerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function streamHandlerProvider(){
        return array(
            array('file:///tmp/php_crontab.log'),
            array('tcp://127.0.0.1:80'),
            array('udp://127.0.0.1:80'),
        );
    }

    public function testStreamHandler($stream){
        $handler = \Jenner\Crontab\Logger\MissionLoggerFactory::getHandler($stream);
        $this->assertInstanceOf("Monolog\\Handler\\StreamHandler", $handler);
    }
}