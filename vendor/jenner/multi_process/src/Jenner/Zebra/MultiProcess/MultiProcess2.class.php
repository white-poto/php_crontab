<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-19
 * Time: 下午7:23
 */
namespace Jenner\Zebra\MultiProcess;

class MultiProcess2
{

    private $producer = array();

    private $worker = array();

    private $woker_count;

    public function __construct($worker_count = 1)
    {
        $this->woker_count = $worker_count;
    }

    public function add_producer(BaseWorker $worker)
    {
        $this->producer[] = $worker;
    }

    public function add_worker(BaseWorker $worker)
    {
        $this->worker[] = $worker;
    }

    public function start()
    {
        $pid = pcntl_fork();

        if ($pid < 0) {
            throw new Exception('count not fork');
        } elseif ($pid == 0) { //parent

            if (empty($this->producer)) exit;

            $cur_size = 0;
            while(true){
                $pid = pcntl_fork();
                if ($pid == -1) {
                    die("could not fork");
                } else if ($pid) {// parent

                    $cur_size++;
                    if($cur_size>=$this->work_count){
                        $sunPid = pcntl_wait($status);
                        $cur_size--;
                    }

                } else {// worker


                    exit();
                }
            }

        } else { //sun parent

        }
    }

}

class BaseWorker
{

    private $runtime;

    public function __construct($runtime)
    {
        if (empty($runtime))
            throw new Exception('BaseWorker construct runtime can not be empty');

        $this->runtime = $runtime;
    }

    public function start()
    {
        while ($this->runtime != 0) {
            $this->go();
            $this->runtime--;
        }
    }

    //业务代码
    protected function run()
    {
    }
}