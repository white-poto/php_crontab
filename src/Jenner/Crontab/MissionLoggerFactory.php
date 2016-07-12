<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2016/7/12
 * Time: 12:29
 */

namespace Jenner\Crontab;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MissionLoggerFactory
{
    public static function create($file) {
        $logger = new Logger(Crontab::NAME);
        $logger->pushHandler(new StreamHandler($file));
        return $logger;
    }
}