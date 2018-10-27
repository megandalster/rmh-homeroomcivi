<?php
use PHPUnit\Framework\TestCase;
include_once(dirname(__FILE__).'/../domain/Room.php');
include_once(dirname(__FILE__).'/../database/dbRooms.php');
class dbRoomsTest extends TestCase{
	function testdbRooms(){
	    
		// Setup -- create some rooms to add to the database
		$room1 = new Room("998", "2T", "3", "y", "clean", "", "");
		$room2 = new Room("999", "Q", "2", "n", "reserved", "", "");
		$this->assertTrue(insert_dbRooms($room1));
		$this->assertTrue(insert_dbRooms($room2));
		
		// Test the retrieve and update functions
		$this->assertEquals(retrieve_dbRooms($room1->get_room_no(),"",""),$room1);
		$this->assertEquals(retrieve_dbRooms($room2->get_room_no(),"","")->get_room_no(),"999");
		$this->assertEquals(retrieve_dbRooms($room2->get_room_no(),"","")->get_status(),"clean");
		$this->assertEquals($room2->reserve_me("13-07-26Alison2076942604"),$room2);
		$this->assertEquals(($room2->unbook_me("13-07-26Alison2076942604")->get_status()),"dirty");
		
		// Teardown --test the delete functions
		$this->assertTrue(delete_dbRooms($room1->get_room_no()));
		$this->assertTrue(delete_dbRooms($room2->get_room_no()));
		
	}
}