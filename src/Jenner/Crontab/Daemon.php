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

class Daemon extends AbstractDaemon
{
    const DEFAULT_FILE = '/var/log/php_crontab.log';

    /**
     * @param $missions array
     * @param $logfile string
     */
    public function __construct($missions, $logfile = null)
    {
        $this->missions = $missions;
        $logger = new Logger("php_crontab");
        if (!empty($logfile)) {
            $logger->pushHandler(new StreamHandler($logfile));
        } else {
            $logger->pushHandler(new StreamHandler(self::DEFAULT_FILE));
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
        $crontab = new Crontab($this->logger, $this->missions);
        $timer = new \EvPeriodic(0., 60., null, function ($timer, $revents) use ($crontab) {
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

        $child = new \EvChild(0, false, function ($child, $revents) {
            pcntl_waitpid($child->rpid, $status);
            $message = "process exit. pid:" . $child->rpid . ". exit code:" . $child->rstatus;
            $this->logger->info($message);
        });

        \Ev::run();
        $this->logger->info("crontab exit");
    }
}