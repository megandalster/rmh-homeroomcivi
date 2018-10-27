<?php
use PHPUnit\Framework\TestCase;
include_once(dirname(__FILE__).'/../domain/Booking.php');
include_once(dirname(__FILE__).'/../domain/Person.php');
include_once(dirname(__FILE__).'/../domain/Room.php');
include_once(dirname(__FILE__).'/../database/dbBookings.php');
include_once(dirname(__FILE__).'/../database/dbPersons.php');
include_once(dirname(__FILE__).'/../database/dbRooms.php');
class dbBookingsTest extends TestCase {
    function testdbBookings() {
        
        // Setup -- create a booking and test inserting it into dbBookings
        $today = date('y-m-d');
        $b = new Booking($today,"","Meghan2075551234","pending","",array("Tiny"),
                  array("Jean:father", "Teeny:sibling"),
                  "", "", "", "Millie2073631234","Maine Med", "SCU", "00000000000",
                  "$10 per night", "","","","","new");
        $p = new Person("Jones", "Meghan", "female", "", "123 College Street","Brunswick", "ME", "04011", "2075551234", "",
                      "email@bowdoin.edu", "guest", "","Tiny", "98-01-01", "Female" , "","");
        $this->assertTrue(insert_dbBookings($b));
        $this->assertTrue(insert_dbPersons($p));
        
        // Test -- test the retrieve and update fuanctions
        $this->assertEquals(retrieve_dbBookings($b->get_id()),$b);
        $this->assertEquals(retrieve_dbBookings($b->get_id())->get_status(), "pending");
        $this->assertEquals(retrieve_dbBookings($b->get_id())->get_flag(), "new");
        $pending_bookings = retrieve_all_pending_dbBookings();
        $n = sizeof($pending_bookings);
        $this->assertEquals($pending_bookings[$n-1]->get_id(), $b->get_id());
        
        $this->assertEquals(($b->reserve_room("126",$today)->get_date_submitted()),$today);
        $this->assertEquals(($b->book_room("126",$today)->get_date_in()),$today); 
        $b->add_occupant("Jordan","brother","","");
        $bretrieved = retrieve_dbBookings($b->get_id());
        $this->assertTrue(in_array("Jordan:brother::", $bretrieved->get_occupants()));
        $this->assertEquals($bretrieved->get_status(),"active");
        $this->assertEquals($bretrieved->get_id(), $b->get_id());
        $this->assertEquals($bretrieved->get_room_no(), "126");
        $r = retrieve_dbRooms($bretrieved->get_room_no(),$today,$bretrieved->get_id());
        $this->assertEquals($r->get_booking_id(),$bretrieved->get_id());
        $today = date('y-m-d');
        $this->assertEquals($bretrieved->get_date_in(), $today);
        $this->assertEquals($bretrieved->get_flag(), "new");
        
        //tests status after a checkout
        $this->assertEquals($bretrieved->check_out($today,""),$bretrieved);
        $bretrieved2 = retrieve_dbBookings($b->get_id());
        $this->assertEquals($bretrieved2->get_status(), "closed");
        $this->assertEquals($bretrieved2->get_date_out(),$today);
        $pretrieved = retrieve_dbPersons($p->get_id());
        $this->assertContains($bretrieved->get_id(),$pretrieved->get_prior_bookings());
        
        // Teardown -- test the delete function
        $this->assertTrue(delete_dbBookings($b->get_id()));
        $this->assertFalse(retrieve_dbBookings($b->get_id()));
        $this->assertTrue(delete_dbPersons($p->get_id()));
    }
}

?>

