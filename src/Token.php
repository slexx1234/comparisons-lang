<?php

namespace Slexx\CL;

class Token
{
    /**
     * @var null|int
     */
    protected $offset = null;

    /**
     * @var null|int
     */
    protected $type = null;

    /**
     * @var null|string
     */
    protected $token = null;

    /**
     * Token constructor.
     * @param int $offset
     * @param int $type
     * @param string $token
     */
    public function __construct(int $offset, int $type, string $token)
    {
        $this->offset = $offset;
        $this->type = $type;
        $this->token = $token;
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
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isComparisonOperator(): bool
    {
        return in_array($this->type, [
            Tokenizer::T_EQ,
            Tokenizer::T_NQ,
            Tokenizer::T_GT,
            Tokenizer::T_GE,
            Tokenizer::T_LT,
            Tokenizer::T_LE,
        ]);
    }

    /**
     * @return bool
     */
    public function isLogicalOperator(): bool
    {
        return $this->isAnd() || $this->isOr();
    }

    /**
     * @return bool
     */
    public function isEq(): bool
    {
        return $this->type === Tokenizer::T_EQ;
    }

    /**
     * @return bool
     */
    public function isNq(): bool
    {
        return $this->type === Tokenizer::T_NQ;
    }

    /**
     * @return bool
     */
    public function isGt(): bool
    {
        return $this->type === Tokenizer::T_GT;
    }

    /**
     * @return bool
     */
    public function isGe(): bool
    {
        return $this->type === Tokenizer::T_GE;
    }

    /**
     * @return bool
     */
    public function isLt(): bool
    {
        return $this->type === Tokenizer::T_LT;
    }

    /**
     * @return bool
     */
    public function isLe(): bool
    {
        return $this->type === Tokenizer::T_LE;
    }

    /**
     * @return bool
     */
    public function isValue(): bool
    {
        return in_array($this->type, [
            Tokenizer::T_INT,
            Tokenizer::T_FLOAT,
            Tokenizer::T_DATE,
            Tokenizer::T_DATE_TIME,
        ]);
    }

    /**
     * @return bool
     */
    public function isBracket(): bool
    {
        return in_array($this->type, [
            Tokenizer::T_OPENING_GROUP,
            Tokenizer::T_CLOSING_GROUP,
        ]);
    }

    /**
     * @return bool
     */
    public function isAnd(): bool
    {
        return $this->type === Tokenizer::T_AND;
    }

    /**
     * @return bool
     */
    public function isOr(): bool
    {
        return $this->type === Tokenizer::T_OR;
    }

    /**
     * @return bool
     */
    public function isInt(): bool
    {
        return $this->type === Tokenizer::T_INT;
    }

    /**
     * @return bool
     */
    public function isInteger(): bool
    {
        return $this->isInt();
    }

    /**
     * @return bool
     */
    public function isFloat(): bool
    {
        return $this->type === Tokenizer::T_FLOAT;
    }

    /**
     * @return bool
     */
    public function isDouble(): bool
    {
        return $this->isFloat();
    }

    /**
     * @return bool
     */
    public function isNumeric(): bool
    {
        return $this->isInt() || $this->isDouble();
    }

    /**
     * @return bool
     */
    public function isDate(): bool
    {
        return $this->type === Tokenizer::T_DATE;
    }

    /**
     * @return bool
     */
    public function isDateTime(): bool
    {
        return $this->type === Tokenizer::T_DATE_TIME;
    }
}
