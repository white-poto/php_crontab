<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-7
 * Time: 下午5:09
 */

namespace Jenner\Zebra\Crontab;

use \Jenner\Zebra\MultiProcess\Process;
use \Jenner\Zebra\MultiProcess\ProcessManager;


/**
 * Class Crontab
 * @package Jenner\Zebra\Crontab
 */
class Crontab
{
    /**
     * @var 定时任务配置
     * 格式：[['name'=>'服务监控', 'cmd'=>'要执行的命令', 'output_file'=>'输出重定向', 'time_rule'=>'时间规则(crontab规则)']]
     */
    protected $mission;

    /**
     * @var null
     * 日志文件[推荐使用绝对路径，请确保有可写权限，否则日志将不会被记录]
     */
    protected $log_file;

    /**
     * @var
     * start()函数开始执行时间，避免程序执行超过一分钟，获取到的时间不准确
     */
    protected $start_time;

    /**
     * @param $crontab_config
     * @param $log_file
     */
    public function __construct($crontab_config, $log_file = null)
    {
        $this->mission = $crontab_config;
        if (is_null($log_file)) {
            $this->log_file = '/var/log/php_crontab.log';
        } else {
            $this->log_file = $log_file;
        }
    }

    /**
     * 创建子进程执行定时任务
     */
    public function start()
    {
        $this->start_time = time();
        $this->log('start. pid' . getmypid());
        $manager = new ProcessManager();
        $missions = $this->getMission();
        foreach ($missions as $mission) {
            $mission_executor = new Mission($mission['cmd'], $mission['output']);
            $this->log($mission['cmd']);
            $manager->fork(new Process([$mission_executor, 'start'], $mission['name']));
        }
        //等待子进程退出
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
        $this->log('end. pid:' . getmypid());
    }

    /**
     * 判断定时任务是否到时
     * @return array
     */
    protected function getMission()
    {
        $mission_config = $this->formatMission();
        $mission = [];
        foreach ($mission_config as $mission_value) {
            if ($this->start_time - CrontabParse::parse($mission_value['time'], $this->start_time) == 0) {
                $mission[] = $mission_value;
            }
        }

        return $mission;
    }

    /**
     * 格式化定时任务配置数组
     * @return array
     */
    protected function formatMission(){
        $mission_array = [];
        foreach($this->mission as $mission_value){
            if(is_array($mission_value['time']) && !empty($mission_value['time'])){
                foreach($mission_value['time'] as $time){
                    $tmp = $mission_value;
                    $tmp['time'] = $time;
                    $mission_array[] = $tmp;
                }
            }else{
                $mission_array[] = $mission_value;
            }
        }
        return $mission_array;
    }

    /**
     * 添加定时任务
     * @param $mission
     * @return mixed
     */
    public function addMission($mission){
        return array_pop($this->mission, $mission);
    }

    /**
     * 设置日志文件
     * @param $filename
     */
    public function setLogFile($filename)
    {
        $this->log_file = $filename;
    }

    /**
     * 日志记录
     * @param $cmd
     */
    protected function log($cmd)
    {
        $content = '[' . date('Y-m-d H:i:s') . ']-' . 'content:' . $cmd . PHP_EOL;
        if (touch($this->log_file) && is_file($this->log_file) && is_writable($this->log_file)) {
            file_put_contents($this->log_file, $content, FILE_APPEND);
        }else{
            echo 'crontab log_file is not writable' . PHP_EOL;
        }
    }
} 