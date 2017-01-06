<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-7
 * Time: 下午5:23
 */

namespace Jenner\Crontab;


use Jenner\Crontab\Parser\CrontabParse;
use Jenner\SimpleFork\Process;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Mission extends Process
{
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
     * @var LoggerInterface where stdout log to
     */
    protected $out;
    /**
     * @var LoggerInterface where stderr log to
     */
    protected $err;
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
     * @param LoggerInterface $out
     * @param LoggerInterface $err
     * @param string|null $user
     * @param string|null $group
     * @param string|null $comment
     */
    public function __construct(
        $name,
        $cmd,
        $time,
        LoggerInterface $out = null,
        LoggerInterface $err = null,
        $user = null,
        $group = null,
        $comment = null
    )
    {
        parent::__construct();
        $this->name = $name;
        $this->cmd = $cmd;
        $this->time = $time;
        $this->user = $user;
        $this->group = $group;
        $this->comment = $comment;

        if (is_null($out)) {
            $this->out = new Logger(Crontab::NAME);
            $this->out->pushHandler(new NullHandler());
        } else {
            $this->out = $out;
        }
        if (is_null($err)) {
            $this->err = new Logger(Crontab::NAME);
            $this->err->pushHandler(new NullHandler());
        } else {
            $this->err = $err;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCmd()
    {
        return $this->cmd;
    }

    /**
     * @param string $cmd
     */
    public function setCmd($cmd)
    {
        $this->cmd = $cmd;
    }

    /**
     * @return LoggerInterface
     */
    public function getOut()
    {
        return $this->out;
    }

    /**
     * @param LoggerInterface $out
     */
    public function setOut(LoggerInterface $out)
    {
        $this->out = $out;
    }

    /**
     * @return LoggerInterface
     */
    public function getErr()
    {
        return $this->err;
    }

    /**
     * @param LoggerInterface $err
     */
    public function setErr(LoggerInterface $err)
    {
        $this->err = $err;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * get or set err
     *
     * @param LoggerInterface $err
     * @return null|string
     */
    public function err(LoggerInterface $err = null)
    {
        if (!is_null($err)) {
            $this->err = $err;
        } else {
            return $this->err;
        }
    }

    /**
     * if the time is right
     *
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
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime($time)
    {
        $this->time = $time;
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
            'err' => $this->err,
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
        $this->setUserAndGroup();

        $out = $this->out;
        $err = $this->err;
        $process = new \Symfony\Component\Process\Process($this->cmd, null, null, null, null);
        $process->run(function ($type, $buffer) use ($out, $err) {
            if ($type == \Symfony\Component\Process\Process::ERR) {
                $err->error($buffer);
            } else {
                $out->info($buffer);
            }
        });
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