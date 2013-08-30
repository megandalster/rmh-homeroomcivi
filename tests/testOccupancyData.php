<?php
include_once(dirname(__FILE__).'/../domain/OccupancyData.php');
include_once(dirname(__FILE__).'/../domain/Booking.php');
class testOccupancyData extends UnitTestCase {
	function testOccupancyDataModule() {
		$jan0112 = date("y-m-d", mktime(0, 0, 0, 1, 1, 2012));
		$jan0212 = date("y-m-d", mktime(0, 0, 0, 1, 2, 2012));
        $d = new OccupancyData($jan0112,$jan0212);
        $rc = $d->get_room_counts();
        $this->assertEqual($rc["223"], 0);
		$this->assertEqual($rc["125"], 0);		 
 		echo ("testOccupancyData complete");
  	}
}

?>
