<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 11:34
 */

namespace Jenner\Crontab;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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
        $loop->addPeriodicTimer(60, function () {
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
        $loop->addPeriodicTimer(60, function () {
            while (($pid = pcntl_waitpid(0, $status, WNOHANG)) > 0) {
                $message = "process exit. pid:" . $pid . ". exit code:" . $status;
                $this->logger->info($message);
            }
        });

        $server = new \Jenner\Crontab\HTTP\Server($loop, $this->missions);
        $server->start();

        $loop->run();
    }
}