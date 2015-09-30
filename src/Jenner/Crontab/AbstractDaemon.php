<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/6
 * Time: 18:55
 */

namespace Jenner\Crontab;

use Psr\Log\LoggerInterface;


class AbstractDaemon
{
    /**
     * cron minssion config
     * @var array
     */
    protected $missions;

    /**
     * psr log instance
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    /**
     * set logger
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * start crontab and loop
     */
    public function start()
    {
        $this->logger->info("crontab start");
        $crontab = new Crontab($this->missions, $this->logger);
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