<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-7
 * Time: 下午5:23
 */

namespace Jenner\Crontab;


use Jenner\SimpleFork\Process;

class Mission extends Process
{
    /**
     * default log file
     */
    const DEFAULT_FILE = '/dev/null';

    /**
     * @var Task
     */
    protected $task;


    public function __construct($task)
    {
        parent::__construct();
        $this->task = $task;
    }

    /**
     * start mission process
     */
    public function run()
    {
        $output_file = $this->task->getOut();

        if(!file_exists($output_file)){
            $create_file = touch($output_file);
            if($create_file === false){
                $message = "can not create output file";
                throw new \RuntimeException($message);
            }
        }
        if(!is_writable($output_file)){
            throw new \RuntimeException("output file is not writable");
        }

        $this->setUserAndGroup();

        $cmd = $this->task->getCmd(). ' >> ' . $output_file;
        exec($cmd, $output, $status);

        exit($status);
    }

    /**
     * set user and group if they are not null
     */
    protected function setUserAndGroup()
    {
        if (!is_null($this->task->getUser())) {
            if (!is_null($this->task->getGroup())) {
                $group_info = posix_getgrnam($this->task->getGroup());
                $group_id = $group_info['gid'];
                if (!posix_setgid($group_id)) {
                    throw new \RuntimeException("set group failed");
                }
            }
            $user_info = posix_getpwnam($this->task->getUser());
            $user_id = $user_info["uid"];
            if (!posix_setuid($user_id)) {
                throw new \RuntimeException("set user failed");
            }
        }
    }
} 