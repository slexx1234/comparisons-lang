<?php

namespace Slexx\CL\Exceptions;

class UnknownTokenException extends \Exception
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @var string
     */
    protected $input;

    /**
     * UnknownTokenException constructor.
     * @param string $message
     * @param int $offset
     * @param string $input
     */
    public function __construct(string $message, int $offset, string $input)
    {
        parent::__construct($message, 0, null);

        $this->offset = $offset;
        $this->input = $input;
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
}

