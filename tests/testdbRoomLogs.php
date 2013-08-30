<?php
include_once(dirname(__FILE__).'/../domain/RoomLog.php');
include_once(dirname(__FILE__).'/../database/dbRoomLogs.php');
class testdbRoomLogs extends UnitTestCase{
	function testdbRoomLogsModule(){
		// Creates some room logs to add to the database
		$today = date('y-m-d');
        $roomLog1 = new RoomLog($today);
		$roomLog2 = new RoomLog("11-02-07");
		// Alter the status and log notes of a room
		$roomLog2->set_log_notes("Room Log 2");
		$roomLog2->set_status("archived");
		
		// test the insert function
		$this->assertTrue(insert_dbRoomLog($roomLog1));
		$this->assertTrue(insert_dbRoomLog($roomLog2));
		
		// test the retrieve function
		$this->assertEqual(retrieve_dbRoomLog($roomLog1->get_id())->get_status(),"unpublished");
		$this->assertEqual(retrieve_dbRoomLog($roomLog2->get_id())->get_status(),"archived");
		
		// test the update function
		$roomLog1->set_log_notes("Room Log 1 notes");
		$this->assertTrue(update_dbRoomLog($roomLog1));
		$this->assertEqual(retrieve_dbRoomLog($roomLog1->get_id())->get_log_notes(),"Room Log 1 notes");
		
		// test the delete function
		$this->assertTrue(delete_dbRoomLog($roomLog1->get_id()));
		$this->assertFalse(retrieve_dbRoomLog($roomLog1->get_id()));
		$this->assertTrue(delete_dbRoomLog($roomLog2->get_id()));
	
		echo ("testdbRoomLogs complete");
	}
}