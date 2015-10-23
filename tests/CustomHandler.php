<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/23
 * Time: 9:33
 */
class CustomHandler implements \Monolog\Handler\HandlerInterface
{
    public $param_1;
    public $param_2;

    public function __construct($param_1, $param_2){
        $this->param_1 = $param_1;
        $this->param_2 = $param_2;
    }

    /**
     * Checks whether the given record will be handled by this handler.
     *
     * This is mostly done for performance reasons, to avoid calling processors for nothing.
     *
     * Handlers should still check the record levels within handle(), returning false in isHandling()
     * is no guarantee that handle() will not be called, and isHandling() might not be called
     * for a given record.
     *
     * @param array $record Partial log record containing only a level key
     *
     * @return Boolean
     */
    public function isHandling(array $record)
    {
        // TODO: Implement isHandling() method.
    }

    /**
     * Handles a record.
     *
     * All records may be passed to this method, and the handler should discard
     * those that it does not want to handle.
     *
     * The return value of this function controls the bubbling process of the handler stack.
     * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
     * calling further handlers in the stack with a given log record.
     *
     * @param  array $record The record to handle
     * @return Boolean true means that this handler handled the record, and that bubbling is not permitted.
     *                        false means the record was either not processed or that this handler allows bubbling.
     */
    public function handle(array $record)
    {
        // TODO: Implement handle() method.
    }

    /**
     * Handles a set of records at once.
     *
     * @param array $records The records to handle (an array of record arrays)
     */
    public function handleBatch(array $records)
    {
        // TODO: Implement handleBatch() method.
    }

    /**
     * Adds a processor in the stack.
     *
     * @param  callable $callback
     * @return self
     */
    public function pushProcessor($callback)
    {
        // TODO: Implement pushProcessor() method.
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return callable
     */
    public function popProcessor()
    {
        // TODO: Implement popProcessor() method.
    }

    /**
     * Sets the formatter.
     *
     * @param  \Monolog\Formatter\FormatterInterface $formatter
     * @return self
     */
    public function setFormatter(\Monolog\Formatter\FormatterInterface $formatter)
    {
        // TODO: Implement setFormatter() method.
    }

    /**
     * Gets the formatter.
     *
     * @return \Monolog\Formatter\FormatterInterface
     */
    public function getFormatter()
    {
        // TODO: Implement getFormatter() method.
    }
}