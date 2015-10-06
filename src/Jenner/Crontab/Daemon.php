<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/5
 * Time: 17:11
 */

namespace Jenner\Crontab;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use React\EventLoop\Factory;

class Daemon extends AbstractDaemon
{
    const LOG_FILE = '/var/log/php_crontab.log';

    /**
     * @var array cron config
     * format£º[
     *  mission_name => [
     *      'name'=>'mission name',
     *      'cmd'=>'shell command',
     *      'out'=>'output filename',
     *      'time'=>'time rule like crontab',
     *      'user'=>'process user',
     *      'group'=>'process group'
     *  ]
     * ]
     */
    protected $missions = array();

    /**
     * @param $missions array
     * @param $logfile string
     */
    public function __construct($missions, $logfile = null)
    {
        foreach($missions as $mission){
            $this->missions[$mission['name']] = $mission;
        }

        $logger = new Logger("php_crontab");
        if (!empty($logfile)) {
            $logger->pushHandler(new StreamHandler($logfile));
        } else {
            $logger->pushHandler(new StreamHandler(self::LOG_FILE));
        }
        $this->logger = $logger;

        parent::__construct($logger);
    }

    /**
     * start crontab and loop
     */
    public function start()
    {
        $this->logger->info("crontab start");
        $crontab = $this->createCrontab();
        $loop = Factory::create();

        // add periodic timer
        $loop->addPeriodicTimer(60, function () use ($crontab) {
            $pid = pcntl_fork();
            if ($pid > 0) {
                return;
            } elseif ($pid == 0) {
                $crontab->start(time());
                exit();
            } else {
                $this->logger->error("could not fork");
                exit();
            }
        });

        // recover the sub processes
        $loop->addPeriodicTimer(60, function () {
            while (($pid = pcntl_waitpid(0, $status, WNOHANG)) > 0) {
                $message = "process exit. pid:" . $pid . ". exit code:" . $status;
                $this->logger->info($message);
            }
        });

        $loop->run();
    }

    /**
     * create crontab object
     *
     * @return Crontab
     */
    protected function createCrontab()
    {
        $missions = $this->formatMission();
        $tasks = array();
        foreach ($missions as $mission) {
            $task = new Task(
                $mission['name'],
                $mission['cmd'],
                $mission['time'],
                $mission['out'],
                $mission['user'],
                $mission['group']
            );
            $tasks[] = $task;
        }

        return new Crontab($this->logger, $tasks);
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
}