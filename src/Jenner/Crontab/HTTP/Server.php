<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 16:28
 */

namespace Jenner\Crontab\HTTP;


use React\EventLoop\LoopInterface;
use React\Http\Request;
use React\Http\Response;

class Server
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var
     */
    protected $missions;

    /**
     * @var array
     */
    protected $routes = array(
        'add' => 'add',
        'get_by_name' => 'getByName',
        'remove_by_name' => 'removeByName',
        'clear' => 'clear',
        'missions' => 'missions',
    );

    /**
     * @param $loop
     * @param $missions
     */
    public function __construct($loop, &$missions)
    {
        $this->loop = $loop;
        $this->missions = $missions;
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
            $path = $request->getPath();
            if(!array_key_exists($path, $this->routes)){
                $this->response($response, 0, "method not found", 101);
                return null;
            }

            $query_string = $request->getQuery();
            parse_str($query_string, $query_info);
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
        foreach($must as $key){
            if(!array_key_exists($key, $params)){
                $this->response($response, 0, "missing param `{$key}`");
                return;
            }
        }

        $this->missions[$params['name']] = $params;
        $this->response($response, 1);
    }

    /**
     * @param $params
     * @param Response $response
     */
    protected function getByName($params, Response $response)
    {
        if(!array_key_exists('name', $params)){
            $this->response($response, 0, "missing param `name`");
            return;
        }

        $name = $params['name'];
        if (array_key_exists($name, $this->missions)) {
            $this->response($response, 1, $this->missions[$name]);
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
        if(!array_key_exists('name', $params)){
            $this->response($response, 0, "missing param name");
            return;
        }

        $name = $params['name'];
        unset($this->missions[$name]);
        $this->response($response, 1);
    }

    /**
     * @param Response $response
     */
    protected function clear(Response $response)
    {
        $this->missions = array();
        $this->response($response, 1);
    }

    /**
     * @param Response $response
     */
    protected function missions(Response $response)
    {
        $this->response($response, 1, $this->missions);
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