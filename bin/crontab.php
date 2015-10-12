#!/bin/bash
<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/6
 * Time: 16:12
 */

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$crontab = new Crontab();
$crontab->start();

class Crontab {
    protected $args = array(
        'h' => 'help',
        'c:' => 'config:',
        'p:' => 'port:',
        'pf:' => 'pid-file:',
        'l:' => 'log:',
    );

    public function start(){
        $params = getopt(implode('', array_keys($this->args)), $this->args);
        print_r($params);
    }
}