<?php

namespace Slexx\CL;

use phpDocumentor\Reflection\DocBlock\Tags\Param;

class ComparisonOperator implements LexemInterface
{
    /**
     * @var Token
     */
    protected $operator;

    /**
     * @var Token
     */
    protected $value;

    /**
     * ComparisonOperator constructor.
     * @param Token $operator
     * @param Token $value
     */
    public function __construct(Token $operator, Token $value)
    {
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return Token
     */
    public function getOperator(): Token
    {
        return $this->operator;
    }

    /**
     * @return Token
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return string
     */
    protected static function escapeSQL(string $value): string {
        $return = '';
        for($i = 0; $i < strlen($value); ++$i) {
            $char = $value[$i];
            $ord = ord($char);
            if($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
                $return .= $char;
            else
                $return .= '\\x' . dechex($ord);
        }
        return $return;
    }

    /**
     * @param string $table
     * @param string|null [$column]
     * @return string
     */
    public function compileToSQL(string $table, $column = null): string
    {
        return '`' . self::escapeSQL($table) . '`' . ($column !== null ? '.`' . self::escapeSQL($column) . '` ' : ' ') . [
            Tokenizer::T_EQ => '=',
            Tokenizer::T_NQ => '!=',
            Tokenizer::T_GT => '>',
            Tokenizer::T_GE => '>=',
            Tokenizer::T_LT => '<',
            Tokenizer::T_LE => '<=',
        ][$this->operator->getType()] . ' ' . ($this->value->isNumeric() ? $this->value : '\'' . $this->value . '\'');
    }

    /**
     * @param string $code
     * @return string
     */
    public function compileToPHP(string $code): string
    {
        return $code . ' ' . [
            Tokenizer::T_EQ => '===',
            Tokenizer::T_NQ => '!=',
            Tokenizer::T_GT => '>',
            Tokenizer::T_GE => '>=',
            Tokenizer::T_LT => '<',
            Tokenizer::T_LE => '<=',
        ][$this->operator->getType()] . ' ' . ($this->value->isNumeric() ? $this->value : 'strtotime(\'' . $this->value . '\')');
    }

    /**
     * @param int $value
     * @return bool
     */
    public function check(int $value): bool
    {
        $toCheck = null;
        if ($this->value->isDate() || $this->value->isDateTime()) {
            $toCheck = strtotime($this->value->getToken());
        } else {
            $toCheck = $this->value->isInt() ? (int) $this->value->getToken() : (float) $this->value->getToken();
        }

        if ($this->operator->isEq()) return $value === $toCheck;
        else if ($this->operator->isNq()) return $value !== $toCheck;
        else if ($this->operator->isGt()) return $value > $toCheck;
        else if ($this->operator->isGe()) return $value >= $toCheck;
        else if ($this->operator->isLt()) return $value < $toCheck;
        else if ($this->operator->isLe()) return $value <= $toCheck;
    }
}