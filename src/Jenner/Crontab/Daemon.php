<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/5
 * Time: 17:11
 */

namespace Jenner\Crontab;

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Daemon extends AbstractDaemon
{

    /**
     * @param $missions array
     * @param $logfile string
     */
    public function __construct($missions, $logfile)
    {
        $this->missions = $missions;
        $logger = new Logger("php_crontab");
        if (!empty($logfile)) {
            $logger->pushHandler(new StreamHandler($logfile));
        } else {
            $logger->pushHandler(new NullHandler());
        }
        $this->logger = $logger;

        parent::__construct($logger);
    }
}