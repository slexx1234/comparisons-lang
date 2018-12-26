<?php

use Slexx\CL\CL;
use PHPUnit\Framework\TestCase;

class CompileSQL extends TestCase
{
    public function testEqOperator()
    {
        $this->assertEquals('`column` = 5', CL::SQL('=5', 'column'));
    }

    public function testNqOperator()
    {
        $this->assertEquals('`column` != 5', CL::SQL('!=5', 'column'));
    }

    public function testGtOperator()
    {
        $this->assertEquals('`column` > 5', CL::SQL('>5', 'column'));
    }

    public function testGeOperator()
    {
        $this->assertEquals('`column` >= 5', CL::SQL('>=5', 'column'));
    }

    public function testLtOperator()
    {
        $this->assertEquals('`column` < 5', CL::SQL('<5', 'column'));
    }

    public function testLeOperator()
    {
        $this->assertEquals('`column` <= 5', CL::SQL('<=5', 'column'));
    }

    public function testAndOperator()
    {
        $this->assertEquals('`column` > 5 AND `column` < 10', CL::SQL('>5&<10', 'column'));
    }

    public function testOrOperator()
    {
        $this->assertEquals('`column` > 5 OR `column` < 10', CL::SQL('>5|<10', 'column'));
    }

    public function testGroups()
    {
        $this->assertEquals('(`column` > 5 AND `column` < 10) OR (`column` > 15 AND `column` < 20)', CL::SQL('(>5&<10)|(>15&<20)', 'column'));
    }

    public function testSetTableName()
    {
        $this->assertEquals('`table`.`column` > 5', CL::SQL('>5', 'table', 'column'));
    }
}
