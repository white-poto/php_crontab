<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/21
 * Time: 18:56
 */

namespace Jenner\Crontab\Parser;


class ConfigParse
{
    /**
     * parse the system crontab service's config file
     * @param $file
     * @return array
     */
    public static function parseFile($file)
    {
        if (!file_exists($file) || !is_readable($file)) {
            $message = "crontab config file is not exists or not readable";
            throw new \RuntimeException($message);
        }

        $tasks = array();
        $lines = file($file);
        foreach ($lines as $line) {
            $job = new JobParse($line);
            $tasks[] = $job->getTask();
        }

        return $tasks;
    }


}