<?php

namespace Slexx\CL;

class LogicalOperator implements LexemInterface
{
    /**
     * @var Token
     */
    protected $operator;

    /**
     * @var mixed
     */
    protected $left;

    /**
     * @var mixed
     */
    protected $right;

    /**
     * LogicalOperator constructor.
     * @param mixed $left
     * @param int $operator
     * @param mixed $right
     */
    public function __construct($left, Token $operator, $right)
    {
        $this->operator = $operator;
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @return Token
     */
    public function getOperator(): Token
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return mixed
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param string $method
     * @param \array[] ...$args
     * @return string
     */
    public function leftCompile(string $method, ...$args): string
    {
        return ($this->left instanceof ComparisonOperator ? call_user_func_array([$this->left, $method], $args) : '(' . call_user_func_array([$this->left, $method], $args)  . ')');
    }

    /**
     * @param string $method
     * @param \array[] ...$args
     * @return string
     */
    public function rightCompile(string $method, ...$args): string
    {
        return ($this->right instanceof ComparisonOperator ? call_user_func_array([$this->right, $method], $args) : '(' . call_user_func_array([$this->right, $method], $args)  . ')');
    }

    /**
     * @param string $table
     * @param string|null [$column]
     * @return string
     */
    public function compileToSQL(string $table, $column = null): string
    {
        return $this->leftCompile('compileToSQL', $table, $column) .
               ' ' . [Tokenizer::T_AND => 'AND', Tokenizer::T_OR => 'OR'][$this->operator->getType()] . ' ' .
               $this->rightCompile('compileToSQL', $table, $column);
    }

    /**
     * @param string $code
     * @return string
     */
    public function compileToPHP(string $code): string
    {
        return $this->leftCompile('compileToPHP', $code) .
               ' ' . [Tokenizer::T_AND => '&&', Tokenizer::T_OR => '||'][$this->operator->getType()] . ' ' .
               $this->rightCompile('compileToPHP', $code);
    }

    /**
     * @param int  $value
     * @return bool
     */
    public function check(int  $value): bool
    {
        if ($this->operator->isAnd()) {
            return $this->left->check($value) && $this->right->check($value);
        }
        return $this->left->check($value) || $this->right->check($value);
    }
}