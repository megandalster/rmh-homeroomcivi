<?php
use PHPUnit\Framework\TestCase;
include_once(dirname(__FILE__).'/../domain/Room.php');
include_once(dirname(__FILE__).'/../database/dbRooms.php');
class dbRoomsTest extends TestCase{
	function testdbRooms(){
		// Create some rooms to add to the database
		$room1 = new Room("126", "2T", "3", "y", "clean", "", "");
		$room2 = new Room("223", "Q", "2", "n", "reserved", "", "");
		
		// Test the delete functions
		$this->assertTrue(delete_dbRooms($room1->get_room_no()));
		$this->assertTrue(delete_dbRooms($room2->get_room_no()));
		
		// Test the insert function
		$this->assertTrue(insert_dbRooms($room1));
		$this->assertTrue(insert_dbRooms($room2));
		$this->assertEquals(retrieve_dbRooms($room1->get_room_no(),"",""),$room1);
		
		// test the retrieve function
		$this->assertEquals(retrieve_dbRooms($room2->get_room_no(),"","")->get_room_no(),"223");
		$this->assertEquals(retrieve_dbRooms($room2->get_room_no(),"","")->get_status(),"clean");
		
		// Test the update functions -- check that unbooking leaves the room dirty
		$this->assertEquals($room2->reserve_me("13-07-26Alison2076942604"),$room2);
		$this->assertEquals(($room2->unbook_me("13-07-26Alison2076942604"))->get_status(),"dirty");
		
		// Restore the rooms in the database
		$this->assertTrue(insert_dbRooms(new Room("126", "2T", "3", "y", "clean", null, "")));
		$this->assertTrue(insert_dbRooms(new Room("223", "Q", "2", "n", "clean", null, "")));
		
		echo ("testdbRooms complete\n");
	}
}