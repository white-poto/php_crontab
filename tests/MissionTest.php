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
        $log_file = "/tmp/mission_test.log";
        if(file_exists($log_file)){
            unlink($log_file);
        }
        $this->mission = new \Jenner\Crontab\Mission("mission_test", "ls /", "* * * * *", $log_file);
        $this->mission->start();
        $this->mission->wait();
        $this->assertEquals($this->mission->exitCode(), 0);
        $out = file_get_contents($log_file);
        $except = shell_exec("ls /");
        $this->assertEquals($out, $except);
    }
}