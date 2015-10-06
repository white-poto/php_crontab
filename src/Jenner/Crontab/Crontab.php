<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-7
 * Time: 下午5:09
 */

namespace Jenner\Crontab;

use Jenner\SimpleFork\Pool;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Crontab
{
    /**
     *
     */
    const DEFAULT_FILE = '/var/log/php_crontab.log';

    /**
     * @var array of Task
     */
    protected $tasks = array();

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var integer start time
     */
    protected $start_time;

    public function __construct(LoggerInterface $logger = null, $tasks = null)
    {
        set_time_limit(0);
        if (is_null($logger)) {
            $this->logger = new Logger("php_crontab");
            $this->logger->pushHandler(new StreamHandler(self::DEFAULT_FILE));
        } else {
            $this->logger = $logger;
        }

        $this->batchAddTask($tasks);
    }

    public function addTask(Task $task)
    {
        array_push($this->tasks, $task);
    }

    public function batchAddTask($tasks)
    {
        foreach ($tasks as $task) {
            if (!($task instanceof Task)) {
                throw new \InvalidArgumentException("param tasks must be an array of Task");
            }
            array_push($this->tasks, $task);
        }
    }

    /**
     * @param $time integer start time
     */
    public function start($time = null)
    {
        try {
            if (is_null($time)) $time = time();
            $this->start_time = $time;
            $this->logger->info(
                "start. date:" . date("Y-m-d H:i:s", $time) . ". pid:" . getmypid());
            $pool = new Pool();

            foreach ($this->tasks as $task) {
                if(!$task->needRun()) continue;

                $process = new Mission($task);
                try {
                    $process->start();
                } catch (\Exception $e) {
                    $this->logException($e);
                }

                $pool->submit($process);
            }

            $pool->wait(true);
        } catch (\Exception $e) {
            $this->logException($e);
        }

        unset($pool);
    }


    protected function logException(\Exception $e)
    {
        $message = "Exception. message:" . $e->getMessage() .
            ". code:" . $e->getCode() .
            ". trace:" . $e->getTraceAsString();

        $this->logger->error($message);
    }
} 