<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/12
 * Time: 15:34
 */
class CrontabTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Jenner\Crontab\Crontab
     */
    protected $crontab;

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
        $this->mission = new \Jenner\Crontab\Mission("mission_test", "ls / -al", "* * * * *", $this->log_file);
        $this->crontab = new \Jenner\Crontab\Crontab(null, array($this->mission));
    }

    public function testStart()
    {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }

        $this->crontab->start(time());
        sleep(1);
        if(!file_exists($this->log_file)){
            echo "file not exists" . PHP_EOL;
        }
        $out = file_get_contents($this->log_file);
        $except = shell_exec("ls /");
        $this->assertEquals($out, $except);
    }

}