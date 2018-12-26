<?php

namespace Slexx\CL;

class CL 
{
    /**
     * @var string
     */
    protected $input;

    /**
     * @var Tokenizer
     */
    protected $tokenizer;

    /**
     * @var Lexer
     */
    protected $lexer;

    /**
     * @var ComparisonOperator|LogicalOperator
     */
    protected $AST;

    /**
     * CL constructor.
     * @param string $input
     * @param int $type
     */
    public function __construct(string $input, int $type = Tokenizer::T_INT)
    {
        $this->input = $input;
        $this->tokenizer = new Tokenizer($this->input, $type);
        $this->lexer = new Lexer($this->tokenizer);
        $this->AST = $this->lexer->getAST();
    }

    /**
     * @return string
     */
    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * @return Tokenizer
     */
    public function getTokenizer(): Tokenizer
    {
        return $this->tokenizer;
    }

    /**
     * @return Lexer
     */
    public function getLexer(): Lexer
    {
        return $this->lexer;
    }

    /**
     * @return ComparisonOperator|LogicalOperator
     */
    public function getAST()
    {
        return $this->AST;
    }

    /**
     * @param string $table
     * @param string|null [$column]
     * @return string
     */
    public function compileToSQL(string $table, $column = null): string
    {
        return $this->AST->compileToSQL($table, $column);
    }

    /**
     * @param string $code
     * @return string
     */
    public function compileToPHP(string $code): string
    {
        return $this->AST->compileToPHP($code);
    }

    /**
     * @param int $value
     * @return bool
     */
    public function check(int $value): bool
    {
        return $this->AST->check($value);
    }

    /**
     * @param string $input
     * @param string $table
     * @param string|null [$column]
     * @return string
     */
    public static function SQL(string $input, string $table, $column = null): string
    {
        return (new self($input))->compileToSQL($table, $column);
    }

    /**
     * @param string $input
     * @param string $code
     * @return string
     */
    public static function PHP(string $input, string $code): string
    {
        return (new self($input))->compileToPHP($code);
    }
}



