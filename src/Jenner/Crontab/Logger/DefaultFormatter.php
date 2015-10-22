<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/22
 * Time: 16:28
 */

namespace Jenner\Crontab\Logger;


use Monolog\Formatter\FormatterInterface;

class DefaultFormatter implements FormatterInterface
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
        return implode(PHP_EOL, array_column($records, 'message'));
    }
}