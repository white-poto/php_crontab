<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/6
 * Time: 18:55
 */

namespace Jenner\Crontab;

use Psr\Log\LoggerInterface;


abstract class AbstractDaemon
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

    public function correctTime(){

    }

    /**
     * start crontab and loop
     */
    abstract public function start();
}