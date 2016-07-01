<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/12
 * Time: 15:34
 */
class CrontabTest extends PHPUnit_Framework_TestCase
{
    public function testStart()
    {
        $log_file = "/tmp/test_start.log";
        $logger = new \Monolog\Logger(new \Monolog\Handler\StreamHandler($log_file));
        $mission = new \Jenner\Crontab\Mission("mission_test", "ls /", "* * * * *", $logger);
        $crontab = new \Jenner\Crontab\Crontab(null, array($mission));

        $crontab->start(time());
        $out = file_get_contents($log_file);
        $except = shell_exec("ls /");
        $this->assertEquals($out, $except);
    }

    public function testError()
    {
        $log_file = "/tmp/test_error.log";
        $err_file = "/tmp/test_error_err.log";
        $out = new \Monolog\Logger(new \Monolog\Handler\StreamHandler($log_file));
        $err = new \Monolog\Logger(new \Monolog\Handler\StreamHandler($err_file));
        $mission = new \Jenner\Crontab\Mission("mission_test", "ls / && command_not_exists", "* * * * *", $out, $err);
        $crontab = new \Jenner\Crontab\Crontab(null, array($mission));

        $crontab->start(time());
        $stdout = file_get_contents($log_file);
        $except = shell_exec("ls /");
        $this->assertEquals($stdout, $except);
        $stderr = file_get_contents($err_file);
        $except = shell_exec('command_not_exists 2>&1');
        $this->assertEquals($stderr, $except);
    }

    public function testNotStart()
    {
        $log_file = "/tmp/test_not_start.log";
        $logger = new \Monolog\Logger(new \Monolog\Handler\StreamHandler($log_file));
        $mission = new \Jenner\Crontab\Mission("mission_test", "ls /", "3 * * * *", $logger);
        $crontab = new \Jenner\Crontab\Crontab(null, array($mission));

        $crontab->start(time());
        $this->assertFalse(file_exists($log_file));
    }

}