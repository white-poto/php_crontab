<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/5
 * Time: 17:11
 */

namespace Jenner\Crontab;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;

class Daemon extends AbstractDaemon
{
    /**
     * default daemon log file
     */
    const LOG_FILE = '/var/log/php_crontab.log';

    /**
     * @var array cron config
     * format[
     *  task_name => [
     *      'name'=>'task_name',
     *      'cmd'=>'shell command',
     *      'out'=>'output',
     *      'err'=>'errout'
     *      'time'=>'* * * * *',
     *      'user'=>'process user',
     *      'group'=>'process group',
     *      'comment'=>'comment',
     *  ]
     * ]
     */
    protected $tasks = array();

    /**
     * @var callable task loader, execute every 60 second
     */
    protected $task_loader;

    /**
     * @param $tasks array
     * @param LoggerInterface $logger
     */
    public function __construct($tasks = array(), LoggerInterface $logger = null)
    {
        $this->setTasks($tasks);

        if (is_null($logger)) {
            $logger = new Logger(Crontab::NAME);
            $logger->pushHandler(new NullHandler());
        }

        parent::__construct($logger);
    }

    /**
     * start crontab and loop
     */
    public function start()
    {
        $this->logger->info("crontab start");
        $crontab = $this->createCrontab();
        $loop = Factory::create();

        // add task loader timer is exists.
        if (!empty($this->task_loader) && is_callable($this->task_loader)) {
            $loop->addPeriodicTimer(60, function () {
                $this->task_loader = call_user_func($this->task_loader);
            });
        }

        // add periodic timer
        $loop->addPeriodicTimer(60, function () use ($crontab, $loop) {
            $loop->addTimer(60 - time() % 60, function () use ($crontab) {
                $pid = pcntl_fork();
                if ($pid > 0) {
                    return;
                } elseif ($pid == 0) {
                    $crontab->start(time());
                    exit();
                } else {
                    $this->logger->error("could not fork");
                }
            });
        });

        // recover the sub processes
        $loop->addPeriodicTimer(60, function () {
            while (($pid = pcntl_waitpid(0, $status, WNOHANG)) > 0) {
                $message = "process exit. pid:" . $pid . ". exit code:" . $status;
                $this->logger->info($message);
            }
        });

        $loop->run();
    }

    public function registerTaskLoader($loader)
    {
        if (!is_callable($loader)) {
            throw new \InvalidArgumentException("task loader is not callable");
        }
        $this->task_loader = $loader;
    }

    /**
     * create crontab object
     *
     * @return Crontab
     */
    protected function createCrontab()
    {
        $tasks = $this->formatTasks();
        $missions = array();
        foreach ($tasks as $task) {
            $out = MissionLoggerFactory::create($task['out']);
            $err = MissionLoggerFactory::create($task['err']);
            $mission = new Mission(
                $task['name'],
                $task['cmd'],
                $task['time'],
                $out,
                $err,
                $task['user'],
                $task['group']
            );
            $missions[] = $mission;
        }

        return new Crontab($this->logger, $missions);
    }

    /**
     * format mission
     *
     * @return array
     */
    protected function formatTasks()
    {
        $tasks = [];
        foreach ($this->tasks as $task) {
            array_key_exists('user', $task) ? null : $task['user'] = null;
            array_key_exists('group', $task) ? null : $task['group'] = null;
            $tasks[] = $task;
        }

        return $tasks;
    }

    /**
     * @param $tasks
     */
    public function setTasks($tasks)
    {
        $must = array('name', 'cmd', 'time');
        foreach ($tasks as $task) {
            foreach ($must as $key) {
                if (!array_key_exists($key, $task)) {
                    $message = "task must have a {$key} value";
                    throw new \InvalidArgumentException($message);
                }
            }

            $this->tasks[$task['name']] = $task;
        }
    }
}