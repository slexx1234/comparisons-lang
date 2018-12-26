<?php

use Slexx\CL\CL;
use PHPUnit\Framework\TestCase;
use Slexx\CL\Exceptions\GroupException;

class GroupsTest extends TestCase
{
    public function testNoOpeningBracket()
    {
        $this->expectException(GroupException::class);
        new CL('>5&<4)');
    }

    public function testNoClosingBracket()
    {
        $this->expectException(GroupException::class);
        new CL('(>5&<4');
    }

    public function testNestedGroups()
    {
        $this->expectException(GroupException::class);
        new CL('(>5&<4&(>5|<29)');
    }
}
