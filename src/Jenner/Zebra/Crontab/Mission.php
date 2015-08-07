<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-7
 * Time: 下午5:23
 */

namespace Jenner\Zebra\Crontab;


class Mission
{

    /**
     * @var string 需要执行的命令
     */
    protected $cmd;

    /**
     * @var string 日志文件
     */
    protected $out;

    /**
     * @param $cmd
     * @param null $out
     */
    public function __construct($cmd, $out = null)
    {
        $this->cmd = $cmd;
        $this->out = $out;
    }

    /**
     * 执行命令
     */
    public function start()
    {
        $output_file = is_null($this->out) ? '/dev/null' : $this->out;
        $cmd = $this->cmd . ' >> ' . $output_file;
        exec($cmd, $output, $status);
        exit($status);
    }
} 