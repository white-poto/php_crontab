<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 11:34
 */

namespace Jenner\Crontab;

use React\EventLoop\Factory;

class HttpDaemon extends Daemon
{
    const LOG_FILE = '/var/log/php_crontab.log';

    /**
     * start crontab and loop
     */
    public function start()
    {
        $this->logger->info("crontab start");

        $loop = Factory::create();

        // add periodic timer
        $loop->addPeriodicTimer(1, function () {
            $pid = pcntl_fork();
            if ($pid > 0) {
                return;
            } elseif ($pid == 0) {
                $crontab = $this->createCrontab();
                $crontab->start(time());
                exit();
            } else {
                $this->logger->error("could not fork");
                exit();
            }
        });

        // recover the sub processes
        $loop->addPeriodicTimer(1, function () {
            while (($pid = pcntl_waitpid(0, $status, WNOHANG)) > 0) {
                $message = "process exit. pid:" . $pid . ". exit code:" . $status;
                $this->logger->info($message);
            }
        });

        $server = new \Jenner\Crontab\HTTP\Server($loop, $this);
        $server->start();

        $loop->run();
    }

    /**
     * @param $task
     */
    public function add($task)
    {
        $this->tasks[$task['name']] = $task;
    }

    /**
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
     * @param $name
     */
    public function removeByName($name)
    {
        unset($this->tasks[$name]);
    }

    /**
     *
     */
    public function clear()
    {
        unset($this->tasks);
        $this->tasks = array();
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->tasks;
    }

}