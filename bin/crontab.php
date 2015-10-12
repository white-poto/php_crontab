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

class Crontab
{

    /**
     * @var array
     */
    protected $params;

    protected $args = array(
        'help' => 'h',
        'config:' => 'c:',
        'port:' => 'p:',
        'pid-file:' => 'f:',
        'log:' => 'l:',
    );

    public function start()
    {
        $this->params = getopt(implode('', array_values($this->args)), array_keys($this->args));
        print_r($this->params);

        var_dump($this->argExists('help'));
        var_dump($this->arg('port'));
    }

    protected function argExists($name){
        if (array_key_exists($name, $this->params)) {
            return true;
        } elseif (array_key_exists($this->args[$name], $this->params)) {
            return true;
        }

        return false;
    }

    protected function arg($name)
    {
        echo rtrim($this->args[$name . ':'], ':') . PHP_EOL;
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }elseif (array_key_exists(rtrim($this->args[$name . ':'], ':'), $this->params)) {
            return $this->params[$this->args[$name]];
        }

        return null;
    }
}