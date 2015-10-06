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

class HttpDaemon extends AbstractDaemon
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

    }
}