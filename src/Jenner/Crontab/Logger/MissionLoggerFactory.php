<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/22
 * Time: 14:50
 */

namespace Jenner\Crontab\Logger;


use Jenner\Crontab\Logger\Formatter\MessageFormatter;
use Jenner\Crontab\Logger\Handler\HttpHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RedisHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MissionLoggerFactory
{
    const DEFAULT_FILE = '/dev/null';
    const NAME = 'php_crontab';

    /**
     * @param $stream
     * @return Logger
     */
    public static function create($stream)
    {
        $logger = new Logger(self::NAME);
        $handler = self::getHandler($stream);
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @param $stream
     * @return HandlerInterface
     */
    public static function getHandler($stream){
        $handler = null;
        $protocol = self::getProtocol($stream);
        switch ($protocol) {
            case 'tcp' :
            case 'udp' :
            case 'unix':
                $socket = stream_socket_client($stream, $errno, $error);
                if ($socket === false) {
                    $message = "connect to socket server failed. errno:" . $error . '. error:' . $error;
                    throw new \RuntimeException($message);
                }
                $handler = new StreamHandler($socket);
                break;
            case 'file' :
                $stream_info = parse_url($stream);
                $handler = new StreamHandler($stream_info['path']);
                break;
            case 'http':
                $handler = new HttpHandler($stream);
                break;
            case 'redis':
                $handler = self::getRedisHandler($stream);
                break;
            case 'custom':
                $handler =  self::getCustomHandler($stream);
                break;
            default:
                throw new \InvalidArgumentException("stream format is error");
        }
        $handler->setFormatter(new MessageFormatter());

        return $handler;
    }

    /**
     * get custom handler
     *
     * @param string $stream custom://classname?construct_params
     * @return HandlerInterface
     */
    protected static function getCustomHandler($stream)
    {
        $stream_info = parse_url($stream);
        $class_name = $stream_info['host'];
        if (!class_exists($class_name)) {
            throw new \RuntimeException("custom handler class is not exists");
        }
        $params = array();
        if (array_key_exists('query', $stream_info)) {
            parse_str($stream_info['query'], $params);
        }
        $reflect = new \ReflectionClass($class_name);
        $handler_interface = "Monolog\\Handler\\HandlerInterface";
        if (!$reflect->isSubclassOf($handler_interface)) {
            $message = "custom logger is not sub class of " . $handler_interface;
            throw new \RuntimeException($message);
        }

        $handler = $reflect->newInstanceArgs($params);
        $handler->setFormatter(new MessageFormatter());

        return $handler;
    }

    /**
     * get redis handler
     *
     * @param $stream
     * @return RedisHandler
     */
    protected static function getRedisHandler($stream)
    {
        $stream_info = parse_url($stream);
        $redis = new \Redis();
        $connect_result = $redis->connect($stream_info['host'], $stream_info['port']);
        if ($connect_result === false) {
            throw new \RuntimeException("connect to redis failed");
        }

        $handler = new RedisHandler($redis, substr($stream_info['path'], 1));
        $handler->setFormatter(new MessageFormatter());

        return $handler;
    }

    /**
     *
     * @param $stream
     * @return mixed
     */
    protected static function getProtocol($stream)
    {
        if(strpos($stream, 'unix') === 0){
            return 'unix';
        }
        $stream_info = parse_url($stream);
        if (!array_key_exists('scheme', $stream_info)) {
            throw new \InvalidArgumentException("stream format error");
        }

        return $stream_info['scheme'];
    }
}