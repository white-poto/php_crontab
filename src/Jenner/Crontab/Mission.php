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
     * @var string mission name
     */
    protected $name;
    /**
     * @var string cli command
     */
    protected $cmd;
    /**
     * @var string crontab time config
     */
    protected $time;
    /**
     * @var string STDOUT file
     */
    protected $out;
    /**
     * @var string the user of task process
     */
    protected $user;
    /**
     * @var string the user's group of task process
     */
    protected $group;

    /**
     * @var string comment of crontab task
     */
    protected $comment;

    /**
     * @param string $name
     * @param string $cmd
     * @param string $time
     * @param string|null $out
     * @param string|null $user
     * @param string|null $group
     * @param string|null $comment
     */
    public function __construct(
        $name,
        $cmd,
        $time,
        $out = null,
        $user = null,
        $group = null,
        $comment = null
    )
    {
        parent::__construct();
        $this->name = $name;
        $this->cmd = $cmd;
        $this->time = $time;
        $this->out = $out;
        $this->user = $user;
        $this->group = $group;
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCmd()
    {
        return $this->cmd;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getOut()
    {
        return $this->out;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param $time
     * @return bool
     */
    public function needRun($time)
    {
        if ($time - CrontabParse::parse($this->getTime(), $time) == 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function info()
    {
        return array(
            'name' => $this->name,
            'cmd' => $this->cmd,
            'time' => $this->time,
            'out' => $this->out,
            'user' => $this->user,
            'group' => $this->group,
            'comment' => $this->comment,
        );
    }

    /**
     * start mission process
     */
    public function run()
    {
        $output_file = $this->out;

        $this->setUserAndGroup();

        if (!file_exists($output_file)) {
            $create_file = touch($output_file);
            if ($create_file === false) {
                $message = "can not create output file";
                throw new \RuntimeException($message);
            }
        }
        if (!is_writable($output_file)) {
            throw new \RuntimeException("output file is not writable");
        }

        $cmd = $this->cmd . ' >> ' . $output_file;
        exec($cmd, $output, $status);

        exit($status);
    }

    /**
     * set user and group if they are not null
     */
    protected function setUserAndGroup()
    {
        if (!is_null($this->user)) {
            if (!is_null($this->group)) {
                $group_info = posix_getgrnam($this->user);
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