<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-7
 * Time: 下午5:09
 */

namespace Jenner\Crontab;

use Jenner\SimpleFork\Pool;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Crontab
{
    const NAME = 'php_crontab';
    /**
     * @var array of Mission
     */
    protected $missions = array();

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var integer start time
     */
    protected $start_time;

    /**
     * @param LoggerInterface|null $logger
     * @param Mission[]|null $missions
     */
    public function __construct(LoggerInterface $logger = null, $missions = null)
    {
        set_time_limit(0);
        if (is_null($logger)) {
            $this->logger = new Logger(self::NAME);
            $this->logger->pushHandler(new NullHandler());
        } else {
            $this->logger = $logger;
        }

        $this->batchAddMissions($missions);
    }

    /**
     * @param Mission $mission
     */
    public function addMission(Mission $mission)
    {
        array_push($this->missions, $mission);
    }

    /**
     * @param array $missions
     */
    public function batchAddMissions($missions)
    {
        foreach ($missions as $mission) {
            $this->addMission($mission);
        }
    }

    /**
     * @param integer $time start time
     */
    public function start($time = null)
    {
        try {
            if (is_null($time)) $time = time();
            $this->start_time = $time;
            $this->logger->info(
                "start. date:" . date("Y-m-d H:i:s", $time) . ". pid:" . getmypid());
            $pool = new Pool();

            foreach ($this->missions as $mission) {
                if (!$mission->needRun($time)) continue;
                try {
                    $mission->start();
                } catch (\Exception $e) {
                    $this->logException($e);
                }


                $pool->submit($mission);
            }

            $pool->wait();
        } catch (\Exception $e) {
            $this->logException($e);
        }

        unset($pool);
    }


    /**
     * @param \Exception $e
     */
    protected function logException(\Exception $e)
    {
        $message = "Exception. message:" . $e->getMessage() .
            ". code:" . $e->getCode() .
            ". trace:" . $e->getTraceAsString();

        $this->logger->error($message);
    }

} 