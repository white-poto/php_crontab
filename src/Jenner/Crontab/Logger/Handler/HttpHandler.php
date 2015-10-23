<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/22
 * Time: 17:04
 */

namespace Jenner\Crontab\Logger\Handler;


use Guzzle\Http\Client;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractHandler;

class HttpHandler extends  AbstractHandler
{

    /**
     * @var Client http client
     */
    protected $client;

    /**
     * @var string http url
     */
    protected $url;

    public function __construct($url)
    {
        parent::__construct();
        $this->client = new Client();
        $this->url = $url;
    }

    /**
     * Handles a record.
     *
     * All records may be passed to this method, and the handler should discard
     * those that it does not want to handle.
     *
     * The return value of this function controls the bubbling process of the handler stack.
     * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
     * calling further handlers in the stack with a given log record.
     *
     * @param  array $record The record to handle
     * @return Boolean true means that this handler handled the record, and that bubbling is not permitted.
     *                        false means the record was either not processed or that this handler allows bubbling.
     */
    public function handle(array $record)
    {
        $this->client->createRequest('POST', $this->url, null, http_build_query($record));
    }
}