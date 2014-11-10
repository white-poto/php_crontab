<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-11-7
 * Time: 下午5:23
 */

namespace Jenner\Zebra\Crontab;


class Mission {

    protected $cmd;

    protected $output_file;

    public function __construct($cmd, $output_file=null){
        $this->cmd = $cmd;
        $this->output_file = $output_file;
    }

    public function start(){
        $output_file = is_null($this->output_file) ? '/dev/null' : $this->output_file;
        $cmd = $this->cmd . ' >> ' .  $output_file;
        exec($cmd, $output, $status);
        exit($status);
    }
} 