<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/22
 * Time: 14:50
 */

namespace Jenner\Crontab\Logger;


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

    public static function getHandler($stream){
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
                return new StreamHandler($socket);
            case 'file':
                $stream_info = parse_url($stream);
                return new StreamHandler($stream_info['path']);
            case 'http':
                return new HttpHandler($stream);
            case 'redis':
                return self::getRedisHandler($stream);
            case 'custom':
                return self::getCustomHandler($stream);
            default:
                throw new \InvalidArgumentException("stream format is error");
        }
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
        if (class_exists($class_name)) {
            throw new \RuntimeException("custom handler class is not exists");
        }
        $params = array();
        if (array_key_exists('query', $stream_info)) {
            parse_str($stream_info['query'], $params);
        }
        $reflect = new \ReflectionClass($class_name);
        $handler_interface = "Monolog\\Handler\\HandlerInterface";
        if ($reflect->isSubclassOf($handler_interface)) {
            $message = "custom logger is not sub class of " . $handler_interface;
            throw new \RuntimeException($message);
        }

        return $reflect->newInstanceArgs($params);
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

        return new RedisHandler($redis, substr($stream_info['path'], 1));
    }

    /**
     *
     * @param $stream
     * @return mixed
     */
    protected static function getProtocol($stream)
    {
        $stream_info = parse_url($stream);
        if (!array_key_exists('scheme', $stream_info)) {
            throw new \InvalidArgumentException("stream format error");
        }

        return $stream_info['scheme'];
    }
}