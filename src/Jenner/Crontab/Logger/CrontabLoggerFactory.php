<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/22
 * Time: 14:48
 */

namespace Jenner\Crontab\Logger;


use Monolog\Handler\NullHandler;
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
    protected static $instances;

    /**
     * @param null $file
     * @return Logger|LoggerInterface
     */
    public static function getInstance($file = null)
    {
        if (is_null($file)) {
            $logger = new Logger(self::NAME);
            $logger->pushHandler(new NullHandler());
            return $logger;
        }

        if (empty(self::$instances)) self::$instances = array();

        if (!array_key_exists($file, self::$instances) || !is_object(self::$instances[$file])) {
            self::$instances[$file] = new Logger("php_crontab");
        }

        return self::$instances[$file];
    }
}