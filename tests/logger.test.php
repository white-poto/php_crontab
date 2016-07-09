<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2016/7/9
 * Time: 10:02
 */


require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$logger = new \Monolog\Logger(\Jenner\Crontab\Crontab::NAME);
$stream = new \Monolog\Handler\StreamHandler($log_file);
$stream->setFormatter(new \Monolog\Formatter\LineFormatter("%message%\n", "Ymd"));
$logger->pushHandler($stream);
$logger->info("tgest  ");
$logger->error("test");