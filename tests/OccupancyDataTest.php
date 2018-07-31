<?php
use PHPUnit\Framework\TestCase;
include_once(dirname(__FILE__).'/../domain/OccupancyData.php');
include_once(dirname(__FILE__).'/../domain/Booking.php');
class OccupancyDataTest extends TestCase {
	function testOccupancyDataModule() {
		$jan0112 = date("y-m-d", mktime(0, 0, 0, 1, 1, 2012));
		$jan0212 = date("y-m-d", mktime(0, 0, 0, 1, 2, 2012));
        $d = new OccupancyData($jan0112,$jan0212,"");
        $rc = $d->get_room_counts();
        $this->assertEquals($rc["223"], 1.0);
		$this->assertEquals($rc["125"], 0);		 
  	}
}

?>
