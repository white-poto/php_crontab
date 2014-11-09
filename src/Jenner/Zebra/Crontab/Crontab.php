<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-11-7
 * Time: 下午5:09
 */

namespace Jenner\Zebra\Crontab;

use \Jenner\Zebra\MultiProcess\Process;
use \Jenner\Zebra\MultiProcess\ProcessManager;


class Crontab
{

    /**
     * @var 定时任务配置
     * 格式：[['name'=>'服务监控', 'cmd'=>'要执行的命令', 'output_file'=>'输出重定向', 'time_rule'=>'时间规则(crontab规则)']]
     */
    protected $config;

    /**
     * @param $crontab_config
     */
    public function __construct($crontab_config)
    {
        $this->config = $crontab_config;
    }

    /**
     * 创建子进程执行定时任务
     */
    public function start()
    {
        $manager = new ProcessManager();
        $missions = $this->getMission();
        foreach ($missions as $mission) {
            $mission_executor = new Mission($mission['cmd'], $mission['output_file']);
            $manager->fork(new Process([$mission_executor, 'start'], $mission['name']));
        }
        do {
            foreach ($manager->getChildren() as $process) {
                $iid = $process->getInternalId();
                if ($process->isAlive()) {
//                    echo sprintf('Process %s is running', $iid) . PHP_EOL;
                } else if ($process->isFinished()) {
//                    echo sprintf('Process %s is finished', $iid) . PHP_EOL;
                }
            }
            sleep(1);
        } while ($manager->countAliveChildren());

    }

    /**
     * 判断定时任务是否到时
     * @return array
     */
    protected function getMission()
    {
        $cur_time = time();
        $mission = [];
        foreach ($this->config as $config) {
            if ($cur_time - CrontabParse::parse($config['time_rule']) == 0) {
                $mission[] = $config;
            }
        }

        return $mission;
    }


} 