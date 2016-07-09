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
        if(file_exists($log_file)) {
            unlink($log_file);
        }
        $logger = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
        $stream = new \Monolog\Handler\StreamHandler($log_file);
        $stream->setFormatter(new \Monolog\Formatter\LineFormatter("%message%", ""));
        $logger->pushHandler($stream);
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
        if(file_exists($log_file)) {
            unlink($log_file);
        }
        if(file_exists($err_file)) {
            unlink($err_file);
        }
        $out = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
        $stream = new \Monolog\Handler\StreamHandler($log_file);
        $stream->setFormatter(new \Monolog\Formatter\LineFormatter("%message%", ""));
        $out->pushHandler($stream);;
        $err = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
        $stream = new \Monolog\Handler\StreamHandler($err_file);
        $stream->setFormatter(new \Monolog\Formatter\LineFormatter("%message%", ""));
        $err->pushHandler($stream);
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
        if(file_exists($log_file)) {
            unlink($log_file);
        }
        $logger = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
        $stream = new \Monolog\Handler\StreamHandler($log_file);
        $stream->setFormatter(new \Monolog\Formatter\LineFormatter("%message%", ""));
        $logger->pushHandler($stream);
        $mission = new \Jenner\Crontab\Mission("mission_test", "ls /", "3 * * * *", $logger);
        $crontab = new \Jenner\Crontab\Crontab(null, array($mission));

        $crontab->start(time());
        $this->assertFalse(file_exists($log_file));
    }

}