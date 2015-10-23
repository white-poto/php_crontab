<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 11:34
 */

namespace Jenner\Crontab;

use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

class HttpDaemon extends Daemon
{

    /**
     * @var int http server port
     */
    protected $port;

    /**
     * @param array $tasks
     * @param LoggerInterface $logger
     * @param int $port
     */
    public function __construct($tasks, LoggerInterface $logger, $port = 6364)
    {
        parent::__construct($tasks, $logger);
        $this->port = $port;
    }

    /**
     * start crontab and loop
     */
    public function start()
    {
        $this->logger->info("crontab start");

        $loop = Factory::create();

        // add periodic timer
        $crontab_timer = $loop->addPeriodicTimer(60, array($this, 'crontabCallback'));

        // recover the sub processes
        $loop->addPeriodicTimer(60, array($this, 'processRecoverCallback'));

        $server = new \Jenner\Crontab\HTTP\Server($loop, $this, $crontab_timer);
        $server->start($this->port);

        $loop->run();
    }

    /**
     * start crontab every minute
     */
    public function crontabCallback(Crontab $crontab, LoopInterface $loop)
    {
        $loop->addTimer(60 - time() % 60, function() use($crontab){
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
    }

    /**
     * recover the sub processes
     */
    public function processRecoverCallback()
    {
        while (($pid = pcntl_waitpid(0, $status, WNOHANG)) > 0) {
            $message = "process exit. pid:" . $pid . ". exit code:" . $status;
            $this->logger->info($message);
        }
    }

    /**
     * add task
     * @param $task
     */
    public function add($task)
    {
        $this->tasks[$task['name']] = $task;
    }

    /**
     * get task by name
     * @param $name
     * @return bool
     */
    public function getByName($name)
    {
        if (array_key_exists($name, $this->tasks)) {
            return $this->tasks[$name];
        }

        return false;
    }

    /**
     * remove task by name
     * @param $name
     */
    public function removeByName($name)
    {
        unset($this->tasks[$name]);
    }

    /**
     * clear all tasks
     */
    public function clear()
    {
        unset($this->tasks);
        $this->tasks = array();
    }

    /**
     * get all tasks
     * @return array
     */
    public function get()
    {
        return $this->tasks;
    }

}