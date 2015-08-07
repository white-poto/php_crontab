<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/5
 * Time: 17:11
 */

namespace Jenner\Zebra\Crontab;

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Daemon extends AbstractDaemon
{

    /**
     * @param $crontab_config
     * @param $logfile
     */
    public function __construct($crontab_config, $logfile)
    {
        $this->crontab_config = $crontab_config;
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