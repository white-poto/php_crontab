<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-7
 * Time: 下午5:09
 */

namespace Jenner\Crontab;

use Jenner\SimpleFork\Pool;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Crontab
{
    /**
     *
     */
    const DEFAULT_FILE = '/var/log/php_crontab.log';

    /**
     * @var array cron config
     * format：[
     *  [
     *      'name'=>'mission name',
     *      'cmd'=>'shell command',
     *      'out'=>'output filename',
     *      'time'=>'time rule like crontab'
     *  ]
     * ]
     */
    protected $missions;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var integer start time
     */
    protected $start_time;

    /**
     * @param $missions
     * @param LoggerInterface $logger
     */
    public function __construct($missions, LoggerInterface $logger = null)
    {
        set_time_limit(0);
        $this->missions = $missions;
        if (is_null($logger)) {
            $this->logger = new Logger("php_crontab");
            $this->logger->pushHandler(new StreamHandler(self::DEFAULT_FILE));
        } else {
            $this->logger = $logger;
        }
    }

    /**
     * @param $time integer start time
     */
    public function start($time = null)
    {
        try {
            if(is_null($time)) $time = time();
            $this->start_time = $time;
            $this->logger->info(
                "start. date:" .date("Y-m-d H:i:s", $time) . ". pid:" . getmypid());
            $pool = new Pool();

            $missions = $this->currentMissions();
            foreach ($missions as $mission) {
                $process = new Mission(
                    $mission['cmd'],
                    $mission['out'],
                    $mission['user'],
                    $mission['group']
                );

                try {
                    $process->start();
                } catch (\Exception $e) {
                    $this->logException($e);
                }

                $pool->submit($process);
            }
        } catch (\Exception $e) {
            $this->logException($e);
        }
    }

    /**
     * get current missions
     *
     * @return array
     */
    protected function currentMissions()
    {
        $missions = $this->formatMission();
        $current_missions = [];
        foreach ($missions as $mission_value) {
            if ($this->start_time - CrontabParse::parse($mission_value['time'], $this->start_time) == 0) {
                $current_missions[] = $mission_value;
            }
        }

        return $current_missions;
    }

    /**
     * format mission
     *
     * @return array
     */
    protected function formatMission()
    {
        $missions = [];
        foreach ($this->missions as $mission) {
            if (is_array($mission['time']) && !empty($mission['time'])) {
                foreach ($mission['time'] as $time) {
                    $tmp = $mission;
                    $tmp['time'] = $time;
                    array_key_exists('user', $tmp) ? null : $tmp['user'] = null;
                    array_key_exists('group', $tmp) ? null : $tmp['group'] = null;
                    $missions[] = $tmp;
                }
            } else {
                array_key_exists('user', $mission) ? null : $mission['user'] = null;
                array_key_exists('group', $mission) ? null : $mission['group'] = null;
                $missions[] = $mission;
            }
        }

        return $missions;
    }

    /**
     * add a new mission
     *
     * @param array $mission
     * @return mixed
     */
    public function addMission(array $mission)
    {
        array_push($this->missions, $mission);
    }

    protected function logException(\Exception $e){
        $message = "Exception. message:" . $e->getMessage() .
            ". code:" . $e->getCode() .
            ". trace:" . $e->getTraceAsString();

        $this->logger->error($message);
    }
} 