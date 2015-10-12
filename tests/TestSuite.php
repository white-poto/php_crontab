<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/12
 * Time: 14:54
 */
class TestSuite 
{

    public function assertEqual($except, $compare){
        if($except == $compare){
            return ;
        }
        $bt = debug_backtrace(false);
        echo sprintf("Assertion failed: %s:%d (%s)\n",
            $bt[0]["file"], $bt[0]["line"], $bt[1]["function"]);
    }

    public function assertFalse($bool){
        if(!$bool){
            return;
        }
        $bt = debug_backtrace(false);
        echo sprintf("Assertion failed: %s:%d (%s)\n",
            $bt[0]["file"], $bt[0]["line"], $bt[1]["function"]);
    }

    public function assertTrue($bool)
    {
        if($bool){
            return;
        }
        $bt = debug_backtrace(false);
        echo sprintf("Assertion failed: %s:%d (%s)\n",
            $bt[0]["file"], $bt[0]["line"], $bt[1]["function"]) . PHP_EOL;
    }

    public static function run($classname){
        if(!class_exists($classname)){
            throw new RuntimeException("class {$classname} not exists");
        }

        $obj = new $classname;
        $reflect = new ReflectionObject($obj);
        $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach($methods as $method){
            $method_name = $method->getName();
            if(strpos($method_name, 'test') !== 0) continue;
            call_user_func($obj, $method_name);
        }
    }
}