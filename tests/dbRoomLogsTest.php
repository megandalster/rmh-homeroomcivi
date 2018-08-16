<?php
use PHPUnit\Framework\TestCase;
include_once(dirname(__FILE__).'/../domain/RoomLog.php');
include_once(dirname(__FILE__).'/../database/dbRoomLogs.php');
class dbRoomLogsTest extends TestCase{
	function testdbRoomLogsModule(){
		
		// Setup -- create and insert a new roomlog in the database
		$today = date('y-m-d');
        $roomLog1 = new RoomLog($today);
		$this->assertTrue(insert_dbRoomLog($roomLog1));
		
		// Test the retrieve and update functions
		$this->assertEquals(retrieve_dbRoomLog($roomLog1->get_id())->get_status(),"unpublished");
		$roomLog1->set_log_notes("Room Log 1 notes");
		$this->assertTrue(update_dbRoomLog($roomLog1));
		$this->assertEquals(retrieve_dbRoomLog($roomLog1->get_id())->get_log_notes(),"Room Log 1 notes");
		
		// Teardown -- test the delete function
		$this->assertTrue(delete_dbRoomLog($roomLog1->get_id()));
		$this->assertFalse(retrieve_dbRoomLog($roomLog1->get_id()));
	}
}