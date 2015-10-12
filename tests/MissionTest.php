<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/12
 * Time: 9:28
 */
class MissionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Jenner\Crontab\Mission
     */
    protected $mission;

    /**
     *
     */
    public function setUp()
    {
        $this->mission = new \Jenner\Crontab\Mission("mission_test", "ls / -al", "* * * * *", "/tmp/mission_test.log");
    }

    public function testNeed()
    {
        $this->assertTrue($this->mission->needRun(time()));
        $this->assertTrue($this->mission->needRun(time() + 60));
        $this->assertTrue($this->mission->needRun(time() + 120));
    }

    public function testRun()
    {
        $this->mission->run();
        $this->mission->wait();
        $this->assertEquals($this->mission->exitCode(), 0);
        $out = file_get_contents("/tmp/mission_test.log");
        exec("ls / -al", $except);
        $this->assertEquals($out, $except);
    }
}