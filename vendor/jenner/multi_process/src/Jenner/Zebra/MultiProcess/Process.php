<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-24
 * Time: 下午14:28
 *
 */
namespace Jenner\Zebra\MultiProcess;

class Process
{

    protected $callback;
    protected $internalId;
    protected $shmSegment;
    protected $started = false;
    protected $finished = false;
    protected $status;
    protected $pid;

    public function __construct($callback, $name)
    {
        $this->callback = $callback;
        $this->internalId = $name;
    }

    public function run()
    {
        $this->setStarted();
        $callback = $this->callback;
        $callback($this);
        exit;
    }

    public function getPid()        { return $this->pid;      }
    public function getInternalId() { return $this->internalId;     }
    public function getStatus()     { return $this->status;   }
    public function isStarted()     { return $this->started;  }
    public function isFinished()    { return $this->finished; }
    public function isAlive()       { return $this->isStarted() && !$this->isFinished(); }
    public function getShmSegment() { return $this->shmSegment; }

    // Methods for ProcessManager

    public function setPid($pid)                 { $this->pid = $pid;         }
    public function setSHMSegment(\Zebra\Ipcs\SHMCache $shm) { $this->shmSegment = $shm;  }
    public function setStarted($started=true)    { $this->started = $started; }
    public function setFinished($finished=true, $status=null)
    {
        $this->finished = $finished;
        $this->status = $status;
    }

}
