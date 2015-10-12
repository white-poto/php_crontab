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

    public function testNeed()
    {
        $this->mission = new \Jenner\Crontab\Mission("mission_test", "ls / -al", "* * * * *", "/tmp/mission_test.log");
        $this->assertTrue($this->mission->needRun(time()));
        $this->assertTrue($this->mission->needRun(time() + 60));
        $this->assertTrue($this->mission->needRun(time() + 120));
    }


    public function testRun()
    {
        if(file_exists("/tmp/mission_test.log")){
            unlink("/tmp/mission_test.log");
        }
        $this->mission = new \Jenner\Crontab\Mission("mission_test", "ls / -al", "* * * * *", "/tmp/mission_test.log");
        echo "prepare" . PHP_EOL;
        $this->mission->start();
        echo "run" . PHP_EOL;
        $this->mission->wait();
        echo "wait" . PHP_EOL;
        $this->assertEquals($this->mission->exitCode(), 0);
        $out = file("/tmp/mission_test.log");
        exec("ls / -al", $except);
        $this->assertEquals($out, $except);
    }
}