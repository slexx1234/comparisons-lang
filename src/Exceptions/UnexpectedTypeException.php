<?php

namespace Slexx\CL\Exceptions;

class UnexpectedTypeException extends \Exception
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $expectedType;

    /**
     * @var string
     */
    protected $input;

    /**
     * UnexpectedTypeException constructor.
     * @param string $message
     * @param int $offset
     * @param string $input
     * @param int $expectedType
     */
    public function __construct(string $message, int $offset, string $input, int $expectedType)
    {
        parent::__construct($message, 0, null);

        $this->offset = $offset;
        $this->input = $input;
        $this->expectedType = $expectedType;
    }

    /**
     * @return string
     */
    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getExpectedType(): int
    {
        return $this->expectedType;
    }
}

