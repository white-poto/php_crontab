<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/12
 * Time: 14:52
 */
class MissionTest extends TestSuite
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



}