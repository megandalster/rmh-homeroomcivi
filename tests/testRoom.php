<?php
include_once(dirname(__FILE__).'/../domain/Room.php');
class testRoom extends UnitTestCase {
	function testRoomModule() {
	// Construct a new room
    $r = new Room("126", "2T", "3", "y", "reserved", null, "this room is fake");
     	
    // Test each of its class variables.
    $this->assertEqual($r->get_room_no(), "126");
    $this->assertTrue($r->get_beds() == "2T");
    $this->assertEqual($r->get_capacity(),3);
    $this->assertTrue($r->get_bath() == "y");
    $this->assertTrue($r->get_room_notes() == "this room is fake");
    $this->assertTrue($r->get_status() == "reserved");
    $this->assertTrue($r->get_booking_id() == null);
    $this->assertTrue($r->set_status("clean"));
    echo ("testRoom complete");
  	}
}

?>
