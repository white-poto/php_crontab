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
    /**
     * @var array
     */
    protected $crontab_config;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param $crontab_config
     * @param $logfile
     */
    public function __construct($crontab_config, $logfile)
    {
        $this->crontab_config = $crontab_config;

        $logger = new Logger("php_crontab");
        if (!empty($logfile)) {
            $logger->pushHandler(new StreamHandler($logfile));
        } else {
            $logger->pushHandler(new NullHandler());
        }
        $this->logger = $logger;
    }

    /**
     * 设置monolog对象
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * 开始运行，不退出模式
     */
    public function start()
    {
        $this->logger->info("crontab start");
        $crontab = new Crontab($this->crontab_config, $this->logger);
        $timer = new \EvPeriodic(0., 60., null, function ($timer, $revents) use ($crontab)  {
            $pid = pcntl_fork();
            if($pid>0){
                // todo
            }elseif($pid==0){
                $crontab->start(time());
            }else{
                $this->logger->error("could not fork");
                exit();
            }
        });

        $child = new \EvChild(0, false, function ($child, $revents) {
            pcntl_waitpid($child->rpid, $status);
            $message = "process exit. pid:" . $child->rpid . ". exit code:" . $child->rstatus;
            $this->logger->info($message);
        });

        \Ev::run();
        $this->logger->info("crontab exit");
    }
}