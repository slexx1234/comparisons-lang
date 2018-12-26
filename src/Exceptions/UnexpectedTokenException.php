<?php

namespace Slexx\CL\Exceptions;

use Slexx\CL\Token;

class UnexpectedTokenException extends \Exception
{
    /**
     * @var Token
     */
    protected $token;

    /**
     * @var string
     */
    protected $input;

    /**
     * UnexpectedTokenException constructor.
     * @param string $message
     * @param string $input
     * @param Token $token
     */
    public function __construct(string $message, string $input, Token $token)
    {
        parent::__construct($message, 0, null);

        $this->input = $input;
        $this->token = $token;
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
        return $this->token->getOffset();
    }

    /**
     * @return int
     */
    public function getToken(): int
    {
        return $this->token;
    }
}

