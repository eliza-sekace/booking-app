<?php
declare(strict_types=1);

use App\Models\Profile;
use PHPUnit\Framework\TestCase;

class ProfileModelTest extends TestCase
{
    public function testGetId():void{
        $profile = new Profile(1, 1, "John", "Doe");
        $this->assertEquals(1, $profile->getId());
    }

    public function testgetUserId():void{
        $profile = new Profile(1, 1, "John", "Doe");
        $this->assertEquals(1, $profile->getUserId());
    }

    public function testgetUserName():void{
        $profile = new Profile(1, 1, "John", "Doe");
        $this->assertEquals("John", $profile->getName());
    }
    public function testgetUserSurname():void{
        $profile = new Profile(1, 1, "John", "Doe");
        $this->assertEquals("Doe", $profile->getSurname());
    }

    public function testgetUserFullname():void{
        $profile = new Profile(1, 1, "John", "Doe");
        $this->assertEquals("John Doe", $profile->getFullName());
    }

}