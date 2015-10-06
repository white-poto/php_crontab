<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 11:36
 */

namespace Jenner\Crontab\HTTP;

use React\EventLoop\LoopInterface;

class Server
{

    /**
     * @var LoopInterface
     */
    protected $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function start($host, $port)
    {
        $socket = new \React\Socket\Server($this->loop);
        $socket->on('connection', function ($conn) {

        });
        $socket->listen(1337);
    }
}