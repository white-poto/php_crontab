<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 14:42
 */

namespace Jenner\Crontab;


class Task
{
    protected $name;
    protected $cmd;
    protected $time;
    protected $out;
    protected $user;
    protected $group;

    public function __construct($name, $cmd, $time, $out = null, $user = null, $group = null)
    {
        $this->name = $name;
        $this->cmd = $cmd;
        $this->time = $time;
        $this->out = $out;
        $this->user = $user;
        $this->group = $group;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCmd()
    {
        return $this->cmd;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getOut()
    {
        return $this->out;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function needRun($time)
    {
        if ($time - CrontabParse::parse($this->getTime(), $time) == 0) {
            return true;
        }
        return false;
    }
}