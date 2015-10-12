<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/14
 * Time: 16:43
 */

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . "TestSuite.php");
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . "MissionTest.php");

TestSuite::run("MissionTest");
