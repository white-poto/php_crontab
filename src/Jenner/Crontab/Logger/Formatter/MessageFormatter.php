<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/23
 * Time: 10:49
 */

namespace Jenner\Crontab\Logger\Formatter;


use Monolog\Formatter\FormatterInterface;

class MessageFormatter implements FormatterInterface
{

    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        return $record['message'];
    }

    /**
     * Formats a set of log records.
     *
     * @param  array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        $message = '';
        foreach($records as $record){
            $message .= $record . PHP_EOL;
        }

        return $message;
    }
}