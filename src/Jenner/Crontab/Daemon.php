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
}