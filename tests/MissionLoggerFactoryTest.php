<?php

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'CustomHandler.php';

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/23
 * Time: 9:17
 */
class MissionLoggerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function streamHandlerProvider()
    {
        return array(
            array('file:///tmp/php_crontab.log'),
            array('tcp://127.0.0.1:80'),
            array('udp://127.0.0.1:80'),
            //array('unix:///tmp/test.socket'),
        );
    }

    /**
     * @dataProvider streamHandlerProvider
     * @param $stream
     */
    public function testStreamHandler($stream)
    {
        $handler = \Jenner\Crontab\Logger\MissionLoggerFactory::getHandler($stream);
        $this->assertInstanceOf("Monolog\\Handler\\StreamHandler", $handler);
    }

    public function testHttpHandler()
    {
        $handler = \Jenner\Crontab\Logger\MissionLoggerFactory::getHandler("http://www.huyanping.cn");
        $this->assertInstanceOf("Jenner\\Crontab\\Logger\\HttpHandler", $handler);
    }

    public function testRedisHandler()
    {
        $handler = \Jenner\Crontab\Logger\MissionLoggerFactory::getHandler("redis://127.0.0.1:6379/key");
        $this->assertInstanceOf("Monolog\\Handler\\RedisHandler", $handler);
    }

    public function testCustomHandler()
    {
        $handler = \Jenner\Crontab\Logger\MissionLoggerFactory::getHandler("custom://CustomHandler?param_1=param_1&param_2=param_2");
        $this->assertInstanceOf("Monolog\\Handler\\HandlerInterface", $handler);
        $reflect = new ReflectionObject($handler);
        $this->assertEquals($reflect->getProperty("param_1")->getValue($handler), "param_1");
        $this->assertEquals($reflect->getProperty("param_2")->getValue($handler), "param_2");
    }
}