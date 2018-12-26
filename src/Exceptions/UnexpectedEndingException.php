<?php

namespace Slexx\CL\Exceptions;

class UnexpectedEndingException extends \Exception
{
    /**
     * @var string
     */
    protected $input;

    /**
     * UnexpectedTokenException constructor.
     * @param string $message
     * @param string $input
     */
    public function __construct(string $message, string $input)
    {
        parent::__construct($message, 0, null);

        $this->input = $input;
    }

    /**
     * @return string
     */
    public function getInput(): string
    {
        return $this->input;
    }
}

