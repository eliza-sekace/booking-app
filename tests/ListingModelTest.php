<?php
declare(strict_types=1);

use App\Models\Listing;
use PHPUnit\Framework\TestCase;

class ListingModelTest extends TestCase
{
    public function testGetId():void{
        $listing = new Listing(1, 1, "John", "apt street", "description", "2022-03-22", null, null, 50 );
        $this->assertEquals(1, $listing->getId());
    }

    public function testGetUserId():void{
        $listing = new Listing(1, 1, "John", "apt street", "description", "2022-03-22", null, null, 50 );
        $this->assertEquals(1, $listing->getUserId());
    }

    public function testGetName():void{
        $listing = new Listing(1, 1, "John", "apt street", "description", "2022-03-22", null, null, 50 );
        $this->assertEquals("John", $listing->getName());
    }

    public function testGetAddress():void{
        $listing = new Listing(1, 1, "John", "apt street", "description", "2022-03-22", null, null, 50 );
        $this->assertEquals("apt street", $listing->getAddress());
    }

    public function testGetDescription():void{
        $listing = new Listing(1, 1, "John", "apt street", "description", "2022-03-22", null, null, 50 );
        $this->assertEquals("description", $listing->getDescription());
    }

    public function testGetAvailableFrom():void{
        $listing = new Listing(1, 1, "John", "apt street", "description", "2022-03-22", null, null, 50 );
        $this->assertEquals("2022-03-22", $listing->getAvailableFrom());
    }

    public function testGetAvailableTill():void{
        $listing = new Listing(1, 1, "John", "apt street", "description", "2022-03-22", null, null, 50 );
        $this->assertEquals(null, $listing->getAvailableTill());
    }

}