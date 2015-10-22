#!/bin/bash
<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 16:12
 */

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

try {
    $crontab = new Crontab();
    $crontab->start();
} catch (Exception $e) {
    $crontab->keepPidFile();
    echo "Exception:" . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}


class Crontab
{

    /**
     * @var \Jenner\Crontab\AbstractDaemon
     */
    protected $daemon;
    /**
     * @var array
     */
    protected $params;

    /**
     * @var string crontab missions config file
     */
    protected $config_file;

    /**
     * @var int http server port
     */
    protected $port;

    /**
     * @var string pid file
     */
    protected $pid_file = './php_crontab.pid';

    /**
     * @var string crontab log
     */
    protected $log;

    /**
     * @var array missions
     */
    protected $missions;

    /**
     * @var bool delete pid file or not
     */
    protected $keep_pid_file = false;

    /**
     * @var array console args
     */
    protected $args = array(
        'help' => 'h',
        'config:' => 'c:',
        'port:' => 'p:',
        'pid-file:' => 'f:',
        'log:' => 'l:',
    );

    /**
     *
     */
    protected function help()
    {
        echo <<<GLOB_MARK
php_crontab help:
-c  --config    crontab tasks config file
-p  --port      http server port
-f  --pid-file  daemon pid file
-l  --log       crontab log file
GLOB_MARK;
        echo PHP_EOL;
        exit;
    }

    /**
     *
     */
    public function start()
    {
        $this->init();
        $this->checkPidFile();
        $this->daemon = $this->factory();
        $this->daemon->start();
    }

    /**
     * @return bool
     */
    protected function checkPidFile()
    {
        register_shutdown_function(array($this, 'deletePidFile'));

        if (file_exists($this->pid_file)) {
            if (!is_readable($this->pid_file) || !is_writable($this->pid_file)) {
                throw new RuntimeException("the pid file is not readable or writable");
            }
            $pid = file_get_contents($this->pid_file);
            if ($pid != getmypid()) {
                $message = "the pid file is exists. " .
                    "so maybe the crontab is already running. PID:" . $pid .
                    ". pid file:" . $this->pid_file . PHP_EOL;
                throw new RuntimeException($message);
            }
        } else {
            $touch = touch($this->pid_file);
            if (!$touch) {
                throw new RuntimeException("create pid file failed");
            }
        }

        $put = file_put_contents($this->pid_file, getmypid());
        if (!$put) {
            throw new RuntimeException("write pid file failed");
        }

        return true;
    }

    /**
     * @throws Exception
     */
    protected function init()
    {
        $this->params = getopt(implode('', array_values($this->args)), array_keys($this->args));

        if ($this->argExists('help') || empty($this->params)) {
            $this->help();
        }

        if (!$this->argExists('config')) {
            throw new Exception("the config arg is required");
        }

        $this->config_file = $this->arg('config');
        if (!file_exists($this->config_file) || !is_readable($this->config_file)) {
            $message = "config file is not exists or is not readable";
            throw new RuntimeException($message);
        }
        $this->missions = include $this->config_file;

        if ($this->argExists('port')) {
            $this->port = $this->arg('port');
        }

        if ($this->argExists('pid-file')) {
            $this->pid_file = $this->arg('pid-file');
        }

        if ($this->argExists('log')) {
            $this->log = $this->arg('log');
        }
    }

    /**
     * @return \Jenner\Crontab\AbstractDaemon
     */
    public function factory()
    {
        $logger = new \Monolog\Logger('php_crontab');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($this->log));
        if (!empty($this->port)) {
            return new \Jenner\Crontab\HttpDaemon($this->missions, $logger, $this->port);
        }
        return new \Jenner\Crontab\Daemon($this->missions, $logger);
    }

    /**
     * @param $name
     * @return bool
     */
    protected function argExists($name)
    {
        if (array_key_exists($name, $this->params)) {
            return true;
        } elseif (array_key_exists($this->args[$name], $this->params)) {
            return true;
        } elseif (array_key_exists(rtrim($this->args[$name . ':'], ':'), $this->params)) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @return null
     */
    protected function arg($name)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        } elseif (array_key_exists(rtrim($this->args[$name . ':'], ':'), $this->params)) {
            return $this->params[rtrim($this->args[$name . ':'], ':')];
        }

        return null;
    }

    /**
     *
     */
    public function keepPidFile()
    {
        $this->keep_pid_file = true;
    }

    public function deletePidFile()
    {
        if (file_exists($this->pid_file) && !$this->keep_pid_file) {
            @unlink($this->pid_file);
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->deletePidFile();
    }


}