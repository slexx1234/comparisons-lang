<?php

use Slexx\CL\CL;
use PHPUnit\Framework\TestCase;

class CompilePHP extends TestCase
{
    public function testEqOperator()
    {
        $this->assertEquals('$var === 5', CL::PHP('=5', '$var'));
    }

    public function testNqOperator()
    {
        $this->assertEquals('$var != 5', CL::PHP('!=5', '$var'));
    }

    public function testGtOperator()
    {
        $this->assertEquals('$var > 5', CL::PHP('>5', '$var'));
    }

    public function testGeOperator()
    {
        $this->assertEquals('$var >= 5', CL::PHP('>=5', '$var'));
    }

    public function testLtOperator()
    {
        $this->assertEquals('$var < 5', CL::PHP('<5', '$var'));
    }

    public function testLeOperator()
    {
        $this->assertEquals('$var <= 5', CL::PHP('<=5', '$var'));
    }

    public function testAndOperator()
    {
        $this->assertEquals('$var > 5 && $var < 10', CL::PHP('>5&<10', '$var'));
    }

    public function testOrOperator()
    {
        $this->assertEquals('$var > 5 || $var < 10', CL::PHP('>5|<10', '$var'));
    }

    public function testGroups()
    {
        $this->assertEquals('($var > 5 && $var < 10) || ($var > 15 && $var < 20)', CL::PHP('(>5&<10)|(>15&<20)', '$var'));
    }
}
