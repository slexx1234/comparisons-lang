<?php

namespace Slexx\CL;

use Slexx\CL\Exceptions\UnexpectedTokenException;
use Slexx\CL\Exceptions\UnexpectedEndingException;

class Lexer
{
    /**
     * @var Tokenizer
     */
    protected $tokens;

    /**
     * @var ComparisonOperator|LogicalOperator
     */
    protected $AST;

    /**
     * Lexer constructor.
     * @param Tokenizer $tokens
     * @throws UnexpectedTokenException
     * @throws UnexpectedEndingException
     */
    public function __construct(Tokenizer $tokens)
    {
        $this->tokens = $tokens;
        $this->AST = $this->parse();
    }

    /**
     * @return ComparisonOperator|LogicalOperator
     */
    public function getAST()
    {
        return $this->AST;
    }

    /**
     * @return string
     */
    public function getInput(): string
    {
        return $this->tokens->getInput();
    }

    /**
     * @return Tokenizer
     */
    public function getTokenizer(): Tokenizer
    {
        return $this->tokens;
    }

    /**
     * @return ComparisonOperator|LogicalOperator
     * @throws UnexpectedEndingException
     * @throws UnexpectedTokenException
     */
    protected function parse()
    {
        $result = [];

        $this->tokens->reset();
        while($this->tokens->valid()) {
            $token = $this->tokens->current();
            $next = $this->tokens->nextToken();

            if ($token->isComparisonOperator()) {
                if ($next === null || !$next->isValue()) {
                    $message = 'Expected Tokenizer::T_INT, Tokenizer::T_FLOAT, Tokenizer::T_DATE or Tokenizer::T_DATE_TIME!';
                    if ($next !== null) {
                        throw new UnexpectedTokenException($message, $this->tokens->getInput(), $next);
                    } else {
                        throw new UnexpectedEndingException($message, $this->tokens->getInput());
                    }
                }

                $result[] = new ComparisonOperator($token, $next);
                $this->tokens->next();
            } else {
                $result[] = $token;
            }

            $this->tokens->next();
        }

        // Парсю группы
        $n = 0;
        while(true) {
            $start = null;
            foreach ($result as $i => $token) {
                if ($token instanceof Token && $token->isBracket()) {
                    if ($start === null) {
                        $start = $i;
                    } else {
                        $n++;
                        array_splice($result, $start, $i - $start + 1, $this->parseLogicalOperators(array_slice($result, $start + 1, $i - $start - 1)));
                        $start = null;
                        break;
                    }
                }
            }

            $hasGroups = false;
            foreach($result as $token) {
                if ($token instanceof Token && $token->isBracket()) {
                    $hasGroups = true;
                    break;
                }
            }
            if ($hasGroups === false) {
                break;
            }
        }

        return $this->parseLogicalOperators($result)[0];
    }

    /**
     * @param array $stage2
     * @return array
     * @throws UnexpectedEndingException
     * @throws UnexpectedTokenException
     */
    protected function parseLogicalOperators(array $stage2): array
    {
        $i = 0;
        while(isset($stage2[$i])) {
            $lexem = $stage2[$i];
            $buffer1 = $stage2[$i+1] ?? null;
            $buffer2 = $stage2[$i+2] ?? null;

            if ($lexem instanceof ComparisonOperator || $lexem instanceof LogicalOperator) {
                if ($buffer1 === null) {
                    return [$lexem];
                } else if ($buffer2 === null) {
                    throw new UnexpectedEndingException('Unexpected ending!', $this->tokens->getInput());
                } else if (!$buffer1 instanceof Token || !($buffer2 instanceof ComparisonOperator || $buffer2 instanceof LogicalOperator)) {
                    throw new UnexpectedTokenException('Unexpected token!', $this->tokens->getInput(), $lexem);
                } else {
                    array_splice($stage2, $i, 3, [new LogicalOperator($lexem, $buffer1, $buffer2)]);
                    $i += 2;
                    continue;
                }
            } else {
                throw new UnexpectedTokenException('Unexpected token!', $this->tokens->getInput(), $lexem);
            }

            $i++;
        }

        return $stage2;
    }
}

