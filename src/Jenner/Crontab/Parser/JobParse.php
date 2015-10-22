<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/21
 * Time: 19:13
 */

namespace Jenner\Crontab\Parser;


class JobParse
{
    /**
     * @var string crontab time config
     */
    protected $time;

    /**
     * @var string cli command
     */
    protected $command;

    /**
     * @param null $raw
     */
    public function __construct($raw = null)
    {
        if (!is_null($raw)) {
            $this->parse($raw);
        }
    }

    /**
     * @param null $time
     * @return null
     */
    public function time($time = null)
    {
        if (!is_null($time)) {
            $this->time = $time;
        } else {
            return $this->time;
        }
    }

    /**
     * @param null $command
     * @return mixed
     */
    public function command($command = null)
    {
        if (!is_null($command)) {
            $this->command = $command;
        } else {
            return $this->command;
        }
    }

    /**
     * @param $raw
     */
    public function parse($raw)
    {
        $info = preg_split('/\s+/', $raw);
        $time_info = array_slice($info, 0, 5);
        $this->time = implode(' ', $time_info);

        $this->command = implode(' ', array_slice($info, 5));
    }

    /**
     * @return string
     */
    public function render()
    {
        $raw = $this->time . ' ' . $this->command;

        return $raw;
    }

    /**
     * @return array
     */
    public function getTask()
    {
        $name = uniqid('cron-', true) . mt_rand(0, 1000000);
        return array(
            'name' => $name,
            'time' => $this->time,
            'cmd' => $this->command,
        );
    }
}