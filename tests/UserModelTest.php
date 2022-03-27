<?php
declare(strict_types=1);

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function testGetId():void{
        $user = new User(1, "john@ex.com", "123456");
        $this->assertEquals(1, $user->getId());
    }

    public function testGetEmail():void{
        $user = new User(1, "john@ex.com", "123456");
        $this->assertEquals("john@ex.com", $user->getEmail());
    }

    public function testGetPassword():void{
        $user = new User(1, "john@ex.com", "123456");
        $this->assertEquals("123456", $user->getPassword());
    }

}