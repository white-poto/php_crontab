<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/12
 * Time: 17:06
 */

return [
    [
        'name' => 'ls',
        'cmd' => "ls -al",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    [
        'name' => 'hostname',
        'cmd' => "hostname",
        'out' => '/tmp/php_crontab.log',
        'time' =>  '* * * * *',
    ],
];