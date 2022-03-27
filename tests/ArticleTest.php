<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class ArticleTest extends TestCase
{
    public function testExample():void{
        $stack=[];
        $this->assertSame(0, count($stack));
    }
}