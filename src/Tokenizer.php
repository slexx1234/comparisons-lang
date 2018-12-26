<?php

namespace Slexx\CL;

use Slexx\CL\Exceptions\GroupException;
use Slexx\CL\Exceptions\UnknownTokenException;
use Slexx\CL\Exceptions\UnexpectedTypeException;

class Tokenizer implements \Iterator
{
    const T_EQ = 1; // =
    const T_NQ = 2; // !=
    const T_GT = 3; // >
    const T_GE = 4; // >=
    const T_LT = 5; // <
    const T_LE = 6; // <=
    const T_OR = 7; // |
    const T_AND = 8; // &
    const T_INT = 9;
    const T_INTEGER = 9;
    const T_FLOAT = 10;
    const T_DOUBLE = 10;
    const T_DATE = 11;
    const T_DATE_TIME = 12;
    const T_OPENING_GROUP = 13; // (
    const T_CLOSING_GROUP = 14; // )

    const PATTERNS = [
        self::T_NQ => '!=',
        self::T_GE => '>=',
        self::T_LE => '<=',
        self::T_EQ => '=',
        self::T_GT => '>',
        self::T_LT => '<',
        self::T_OR => '\|',
        self::T_AND => '&',
        self::T_DATE_TIME => '\d{4}-\d{2}-\d{2} \d\d:\d\d:\d\d',
        self::T_DATE => '\d{4}-\d{2}-\d{2}',
        self::T_FLOAT => '-?\d\.\d+',
        self::T_INT => '-?\d+',
        self::T_OPENING_GROUP => '\(',
        self::T_CLOSING_GROUP => '\)',
    ];

    /**
     * @var null|string
     */
    protected static $regex = null;

    /**
     * @var Token[]
     */
    protected $tokens = [];

    /**
     * @var string
     */
    protected $input;

    /**
     * @var int
     */
    protected $valueType;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @return Token[]
     */
    public function getTokens()
    {
        return $this->tokens;
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
    public function getValueType(): int
    {
        return $this->valueType;
    }

    /**
     * @return string
     */
    public static function getRegex(): string
    {
        if (self::$regex !== null) {
            return self::$regex;
        }

        self::$regex = '/^(' . implode('|', self::PATTERNS) . ')/A';
        return self::$regex;
    }

    /**
     * Tokenizer constructor.
     * @param string $input
     * @param int [$valueType]
     * @throws UnknownTokenException
     * @throws UnexpectedTypeException
     * @throws GroupException
     */
    public function __construct(string $input, int $valueType = self::T_INT)
    {
        $this->input = $input;
        $this->valueType = $valueType;

        $toSplit = $input;
        $offset = 0;
        $regex = self::getRegex();
        $groups = 0;

        while(strlen($toSplit) > 0) {
            preg_match($regex, $toSplit, $matches);

            if (count($matches) === 0) {
                throw new UnknownTokenException('Unknown token at offset ' . $offset . ' (' . $toSplit . ')', $offset, $input);
            }

            $token = $matches[0];
            $type = null;

            foreach(self::PATTERNS as $t => $pattern) {
                if (preg_match('/^' . $pattern . '$/', $token)) {
                    $type = $t;
                    break;
                }
            }

            if ($type === self::T_CLOSING_GROUP) {
                $groups--;
            }
            if ($type === self::T_OPENING_GROUP) {
                $groups++;
            }

            if ($groups < 0) {
                throw new GroupException('Unexpected group closing!', $offset, $input);
            }
            if ($groups > 1) {
                throw new GroupException('Groups cannot be nested!', $offset, $input);
            }

            if (in_array($type, [self::T_FLOAT, self::T_INT, self::T_DATE_TIME, self::T_DATE]) && $type !== $valueType) {
                throw new UnexpectedTypeException('Unexpected type at offset ' . $offset . ' (' . $toSplit . '). Expected ' . [
                        self::T_FLOAT => 'Tokenizer::F_FLOAT',
                        self::T_INT => 'Tokenizer::T_INT',
                        self::T_DATE_TIME => 'Tokenizer::T_DATE_TIME',
                        self::T_DATE => 'Tokenizer::T_DATE'
                    ][$valueType] . '!', $offset, $input, $valueType);
            }

            $this->tokens[] = new Token($offset, $type, $token);
            $offset = strlen($token);
            $toSplit = substr($toSplit, strlen($token));
        }

        if ($groups !== 0) {
            throw new GroupException('Expected group closing!', $offset, $input);
        }
    }

    /**
     * Returns current token.
     * @return null|Token
     */
    public function current()
	{
		return $this->tokens[$this->position] ?? null;
	}

    /**
     * @return null|Token
     */
    public function nextToken()
    {
        return $this->tokens[$this->position+1] ?? null;
    }

    /**
     * @return null|Token
     */
    public function prevToken()
    {
        return $this->tokens[$this->position-1] ?? null;
    }

    /**
     * @return Tokenizer
     */
    public function reset(): self
    {
        $this->position = 0;
        return $this;
    }

    /**
     * Moves cursor to next token.
     * @return Tokenizer
     */
    public function next(): self
    {
        $this->position++;
        return $this;
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * @return Tokenizer
     */
    public function rewind(): self
    {
        return $this->reset();
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->tokens[$this->position]);
    }
}

