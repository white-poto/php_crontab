<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/22
 * Time: 14:48
 */

namespace Jenner\Crontab\Logger;


use Monolog\Logger;
use Psr\Log\LoggerInterface;

class CrontabLoggerFactory
{

    /**
     * logger name
     */
    const NAME = 'php_crontab';
    /**
     * @var LoggerInterface[]
     */
    protected static $instance;

    /**
     * @param null $file
     * @return Logger|LoggerInterface
     */
    public static function getInstance($file = null)
    {
        if (!is_object(self::$instance) || !(self::$instance instanceof LoggerInterface)) {
            self::$instance = new Logger("php_crontab");
        }

        return self::$instance;
    }
}