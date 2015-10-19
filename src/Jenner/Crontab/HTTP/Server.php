<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 16:28
 */

namespace Jenner\Crontab\HTTP;

use Jenner\Crontab\HttpDaemon;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use React\Http\Request;
use React\Http\Response;

class Server
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var HttpDaemon
     */
    protected $daemon;

    /**
     * @var TimerInterface
     */
    protected $crontab_timer;

    /**
     * @var array
     */
    protected $routes = array(
        'add' => 'add',
        'get_by_name' => 'getByName',
        'remove_by_name' => 'removeByName',
        'clear' => 'clear',
        'get' => 'missions',
        'start' => 'begin',
        'stop' => 'stop',
    );

    /**
     * @param $loop
     * @param HttpDaemon $daemon
     * @param TimerInterface $crontab_timer
     */
    public function __construct($loop, HttpDaemon $daemon, TimerInterface $crontab_timer)
    {
        $this->loop = $loop;
        $this->daemon = $daemon;
        $this->crontab_timer = $crontab_timer;
    }

    /**
     * @param int $port
     * @throws \React\Socket\ConnectionException
     */
    public function start($port = 6364)
    {
        $socket = new \React\Socket\Server($this->loop);

        $http = new \React\Http\Server($socket);
        $http->on('request', function (Request $request, Response $response) {
            $path = trim($request->getPath(), '/');
            if (!array_key_exists($path, $this->routes)) {
                $this->response($response, 0, "method not found", 101);
                return null;
            }

            $query_info = $request->getQuery();
            $method = array($this, $this->routes[$path]);
            return call_user_func($method, $query_info, $response);
        });

        $socket->listen($port);
    }

    /**
     * @param $params
     * @param Response $response
     */
    protected function add($params, Response $response)
    {
        $must = array('name', 'cmd', 'time');
        foreach ($must as $key) {
            if (!array_key_exists($key, $params)) {
                $this->response($response, 0, "missing param `{$key}`");
                return;
            }
        }

        $this->daemon->add($params);
        $this->response($response, 1);
    }

    /**
     * @param $params
     * @param Response $response
     */
    protected function getByName($params, Response $response)
    {
        if (!array_key_exists('name', $params)) {
            $this->response($response, 0, "missing param `name`");
            return;
        }

        $name = $params['name'];
        $task = $this->daemon->getByName($name);
        if ($task !== false) {
            $this->response($response, 1, $task);
            return;
        }

        $this->response($response, 0, "mission {$name} is not exists");
    }

    /**
     * @param $params
     * @param Response $response
     */
    protected function removeByName($params, Response $response)
    {
        if (!array_key_exists('name', $params)) {
            $this->response($response, 0, "missing param name");
            return;
        }

        $name = $params['name'];
        $this->daemon->removeByName($name);
        $this->response($response, 1);
    }

    /**
     * @param Response $response
     */
    protected function clear($params, Response $response)
    {
        $this->daemon->clear();
        $this->response($response, 1);
    }

    /**
     * @param Response $response
     */
    protected function missions($params, Response $response)
    {
        $this->response($response, 1, $this->daemon->get());
    }

    /**
     * @param $params
     * @param Response $response
     */
    protected function begin($params, Response $response)
    {
        $this->loop->addPeriodicTimer(60, array($this->daemon, 'crontabCallback'));
        $this->response($response, 1);
    }

    /**
     * @param $params
     * @param Response $response
     */
    protected function stop($params, Response $response)
    {
        $this->loop->cancelTimer($this->crontab_timer);
        $this->response($response, 1);
    }

    /**
     * @param Response $response
     * @param $status
     * @param null $data
     * @param int $code
     * @throws \Exception
     */
    protected function response(Response $response, $status, $data = null, $code = 0)
    {
        $response->writeHead(200, array('Content-Type' => 'text/plain'));
        $response_data = array(
            'status' => $status,
            'code' => $code,
        );
        is_null($data) ? null : $response_data['data'] = $data;
        $body = json_encode($response_data);

        $response->end($body);
    }
}