<?php
use PHPUnit\Framework\TestCase;
include_once(dirname(__FILE__).'/../domain/Booking.php');
include_once(dirname(__FILE__).'/../database/dbBookings.php');
class dbBookingsTest extends TestCase {
    function testdbBookings() {
        
        // create a booking and test inserting and retrieving it from dbBookings
        $today = date('y-m-d');
        $b = new Booking($today,"","Meghan2075551234","pending","",array("Tiny"),
                  array("Meghan:mother", "Jean:father", "Teeny:sibling"),
                  "", "", "", "Millie2073631234","Maine Med", "SCU", "00000000000",
                  "$10 per night", "","","","","new");
        $this->assertTrue(insert_dbBookings($b));
        $this->assertEquals(retrieve_dbBookings($b->get_id()),$b);
        $this->assertTrue(in_array("Meghan:mother", $b->get_occupants()));
        
        //checks that the initial status is "pending"
        $this->assertEquals(retrieve_dbBookings($b->get_id())->get_status(), "pending");
        
        $this->assertEquals(retrieve_dbBookings($b->get_id())->get_flag(), "new");
        $pending_bookings = retrieve_all_pending_dbBookings();
        $n = sizeof($pending_bookings);
        $this->assertEquals($pending_bookings[$n-1]->get_id(), $b->get_id());
        // now reserve and book the room
        $this->assertEquals(($b->reserve_room("126",$today))->get_date_submitted(),$today);
        $this->assertEquals(($b->book_room("126",$today))->get_date_in(),$today);
        
        // make some changes and test updating it in the database
        $b->set_flag("viewed");
		$b->add_occupant("Jordan","brother","","");
        $bretrieved = retrieve_dbBookings($b->get_id());
        $this->assertTrue(in_array("Jordan:brother::", $bretrieved->get_occupants()));
        $this->assertEquals($bretrieved->get_status(),"active");
        $this->assertEquals($bretrieved->get_id(), $b->get_id());
        $this->assertEquals($bretrieved->get_room_no(), "126");
        $today = date('y-m-d');
        $this->assertEquals($bretrieved->get_date_in(), $today);
        $this->assertEquals($bretrieved->get_flag(), "viewed");
        
        //tests updating after a checkout
        $this->assertEquals($bretrieved->check_out($today,""),$bretrieved);
        $bretrieved2 = retrieve_dbBookings($b->get_id());
        $this->assertEquals($bretrieved2->get_status(), "closed");
        $this->assertEquals($bretrieved2->get_date_out(),$today);
        
        //tests the delete function
        $this->assertTrue(delete_dbBookings($b->get_id()));
        $this->assertFalse(retrieve_dbBookings($b->get_id()));
    }
}

?>

