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
     * @var string shell command
     */
    protected $cmd;

    /**
     * @var string log file
     */
    protected $out;

    /**
     * @var string user of this process
     */
    protected $user;

    /**
     * @var string group of this process
     */
    protected $group;

    /**
     * @param $cmd
     * @param null $out
     */
    public function __construct($cmd, $out = null, $user = null, $group = null)
    {
        parent::__construct();
        $this->cmd = $cmd;
        $this->out = $out;
    }

    /**
     * start mission process
     */
    public function run()
    {
        $output_file = is_null($this->out) ? self::DEFAULT_FILE : $this->out;

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

        $cmd = $this->cmd . ' >> ' . $output_file;
        exec($cmd, $output, $status);

        exit($status);
    }

    /**
     * set user and group if they are not null
     */
    public function setUserAndGroup()
    {
        if (!is_null($this->user)) {
            if (!is_null($this->group)) {
                $group_info = posix_getgrnam($this->group);
                $group_id = $group_info['gid'];
                if (!posix_setgid($group_id)) {
                    throw new \RuntimeException("set group failed");
                }
            }
            $user_info = posix_getpwnam($this->user);
            $user_id = $user_info["uid"];
            if (!posix_setuid($user_id)) {
                throw new \RuntimeException("set user failed");
            }
        }
    }
} 