<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/12
 * Time: 17:06
 */

return [
    [
        'name' => 'hello',
        'cmd' => "php -r '{$hello_command}'",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
        'user' => 'www',
        'group' => 'www'
    ],
    'mission_ls' => [
        'name' => 'world',
        'cmd' => "php -r '{$world_command}'",
        'out' => '/tmp/php_crontab.log',
        'time' => '* * * * *',
    ],
];