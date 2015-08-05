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

class Daemon
{
    protected $crontab_config;

    protected $logger;

    /**
     * @param $crontab_config
     * @param $logfile
     */
    public function __construct($crontab_config, $logfile){
        $this->crontab_config = $crontab_config;
        $logger = new Logger("php_crontab");
        if(!empty($logfile)){
            $logger->pushHandler(new StreamHandler($logfile));
        }else{
            $logger->pushHandler(new NullHandler());
        }
    }

    public function setLogger(Logger $logger){
        $this->logger = $logger;
    }

    public function start(){
        $timer = new EvPeriodic(0., 1., null, function($w, $revents){
            echo microtime(), PHP_EOL;
        });

        Ev::run();
    }
}