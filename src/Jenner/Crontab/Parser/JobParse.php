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
     * @var
     */
    protected $time;

    /**
     * @var
     */
    protected $command;

    /**
     * @var
     */
    protected $output;

    /**
     * @var
     */
    protected $overwrite;

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
            return $time;
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
     * @param null $output
     * @return mixed
     */
    public function output($output = null)
    {
        if (!is_null($output)) {
            $this->output = $output;
        } else {
            return $this->output;
        }
    }

    /**
     * @param null $overwrite
     * @return mixed
     */
    public function overwrite($overwrite = null)
    {
        if (!is_null($overwrite)) {
            $this->overwrite = $overwrite;
        } else {
            return $this->overwrite;
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

        for ($i = count($info); $i >= 0; $i--) {
            $value = $info[$i];
            if ($value != '>>' || $value != '>') continue;
            if ($value == '>>') $this->overwrite = false;
            else $this->overwrite = true;

            $this->output = $info[$i - 1];
            break;
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        $raw = $this->time . ' ' . $this->command;
        if (empty($this->output)) return $raw;

        if ($this->overwrite === true) {
            $raw .= ' > ' . $this->output;
        } else {
            $raw .= ' >> ' . $this->output;
        }

        return $raw;
    }

    /**
     * @return array
     */
    public function getTask()
    {
        return array(
            'time' => $this->time,
            'cmd' => $this->command,
            'out' => $this->output,
        );
    }
}