<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/12
 * Time: 14:52
 */
class MissionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Jenner\Crontab\Mission
     */
    protected $mission;

    /**
     * @var string
     */
    protected $log_file = "/tmp/mission_test.log";

    public function setUp()
    {
        if(file_exists($this->log_file)) {
            unlink($this->log_file);
        }
        $logger = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
        $stream = new \Monolog\Handler\StreamHandler($this->log_file);
        $stream->setFormatter(new \Monolog\Formatter\LineFormatter("%message%", "", true));
        $logger->pushHandler($stream);

        $this->mission = new \Jenner\Crontab\Mission(
            "mission_test",
            "ls /",
            "* * * * *",
            $logger
        );
    }

    public function testNeed()
    {
        $this->assertTrue($this->mission->needRun(time()));
        $this->assertTrue($this->mission->needRun(time() + 60));
        $this->assertTrue($this->mission->needRun(time() + 120));
    }


    public function testRun()
    {
        $this->mission->start();
        $this->mission->wait();
        $this->assertEquals($this->mission->errno(), 0);
        $out = file_get_contents($this->log_file);
        $except = shell_exec("ls /");
        $this->assertEquals($out, $except);
    }
}