<?php
include_once(dirname(__FILE__).'/../domain/RoomLog.php');
class testRoomLog extends UnitTestCase {
	function testRoomLogModule() {
		// Generate a new room log
		$rl = new RoomLog("11-02-07");
		// make a time that was given to the constructor. This will
		// be used for testing
		$test_time = mktime(0,0,0,02,07,2011);
		
		$this->assertEqual($rl->get_id(),date("y-m-d", $test_time));
		$this->assertEqual($rl->get_day_of_month(), date("d", $test_time));
		$this->assertEqual($rl->get_day(), date("D", $test_time));
		$this->assertEqual($rl->get_day_of_week(), date("N", $test_time));
		$this->assertEqual($rl->get_day_of_year(), date("z", $test_time) + 1);
		$this->assertEqual($rl->get_year(), date("Y", $test_time));
		$this->assertEqual($rl->get_status(), "unpublished");
		$this->assertEqual(sizeof($rl->get_rooms()), 21);
		$this->assertEqual($rl->get_name(), "February 7, 2011");
		
		// New date for testing that has seconds/hours/minutes
		$test_end_time = mktime(23,59,59,02,07,2011);
		$this->assertEqual($rl->get_end_time(), $test_end_time);
		// Set some log notes
		$rl->set_log_notes("Some notes");
		$this->assertTrue(strcmp($rl->get_log_notes(), "Some notes") == 0);
		 
 		echo ("testRoomLog complete");
  	}
}

?>
