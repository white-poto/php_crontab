<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/5
 * Time: 17:11
 */

namespace Jenner\Zebra\Crontab;


class Daemon
{
    public function start(){
        $timer = new EvPeriodic(0., 1., null, function($w, $revents){
            echo microtime(), PHP_EOL;
        });

        Ev::run();
    }
}