<?php

namespace Slexx\CL;

interface LexemInterface
{
    /**
     * @param string $table
     * @param string|null [$column]
     * @return string
     */
    public function compileToSQL(string $table, $column = null): string;

    /**
     * @param string $code
     * @return string
     */
    public function compileToPHP(string $code): string;

    /**
     * @param int $value
     * @return bool
     */
    public function check(int $value): bool;
}