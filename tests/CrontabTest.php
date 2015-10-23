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
     * @var string
     */
    protected $log_file = "file:///tmp/crontab_test.log";

    protected $err_file = "file:///tmp/crontab_err.log";


    public function testStart()
    {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
        $logger = \Jenner\Crontab\Logger\MissionLoggerFactory::create($this->log_file);
        $mission = new \Jenner\Crontab\Mission("mission_test", "ls /", "* * * * *", $logger);
        $crontab = new \Jenner\Crontab\Crontab(null, array($mission));

        $crontab->start(time());
        $out = file_get_contents($this->log_file);
        $except = shell_exec("ls /");
        $this->assertEquals($out, $except);
    }

    public function testError(){
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
        if(file_exists($this->err_file)){
            unlink($this->err_file);
        }

        $out = \Jenner\Crontab\Logger\MissionLoggerFactory::create($this->log_file);
        $err = \Jenner\Crontab\Logger\MissionLoggerFactory::create($this->err_file);
        $mission = new \Jenner\Crontab\Mission("mission_test", "ls / && ddddeee", "* * * * *", $out, $err);
        $crontab = new \Jenner\Crontab\Crontab(null, array($mission));

        $crontab->start(time());
        $stdout = file_get_contents($this->log_file);
        $except = shell_exec("ls /");
        $this->assertEquals($stdout, $except);
        $stderr = file_get_contents($this->err_file);
        $except = shell_exec('ddddeeeee 2>&1');
        $this->assertEquals($stderr, $except);
    }

    public function testNotStart()
    {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }

        $logger = \Jenner\Crontab\Logger\MissionLoggerFactory::create($this->log_file);
        $mission = new \Jenner\Crontab\Mission("mission_test", "ls /", "3 * * * *", $logger);
        $crontab = new \Jenner\Crontab\Crontab(null, array($mission));

        $crontab->start(time());
        $this->assertFalse(file_exists($this->log_file));
    }

}