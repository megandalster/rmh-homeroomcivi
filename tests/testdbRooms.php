<?php
include_once(dirname(__FILE__).'/../domain/Room.php');
include_once(dirname(__FILE__).'/../database/dbRooms.php');
class testdbRooms extends UnitTestCase{
	function testdbRoomsModule(){
		// Create some rooms to add to the database
		$room1 = new Room("126", "2T", "3", "y", "clean", null, "");
		$room2 = new Room("223", "Q", "2", "n", "clean", null, "");
		
		// Test the insert function
		$this->assertTrue(insert_dbRooms($room1));
		$this->assertTrue(insert_dbRooms($room2));
		$this->assertTrue(retrieve_dbRooms($room1->get_room_no()));
		
		// Set status for room 1
		$room1->set_status("dirty");
		// Can't book a dirty room
		$this->assertFalse($room1->book_me("11-02-07Meghan2077291234"));
		// but can book a clean room
		$this->assertTrue($room2->book_me("11-01-01Jones2077311154"));
		
		// test the retrieve function
		$this->assertEqual(retrieve_dbRooms($room2->get_room_no())->get_room_no(),"223");
		$this->assertEqual(retrieve_dbRooms($room2->get_room_no())->get_status(),"booked");
		
		// Test the update functions -- check that unbooking leaves the room dirty
		$this->assertTrue($room2->unbook_me("11-01-01Jones2077311154"));
		$this->assertEqual(retrieve_dbRooms($room2->get_room_no())->get_status(),"dirty");
		
		// Test the delete functions
		$this->assertTrue(delete_dbRooms($room1->get_room_no()));
		$this->assertTrue(delete_dbRooms($room2->get_room_no()));
		
		// Restore the rooms in the database
		$this->assertTrue(insert_dbRooms(new Room("126", "2T", "3", "y", "clean", null, "")));
		$this->assertTrue(insert_dbRooms(new Room("223", "Q", "2", "n", "clean", null, "")));
		
		echo ("testdbRooms complete");
	}
}