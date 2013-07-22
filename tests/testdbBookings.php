<?php
include_once(dirname(__FILE__).'/../domain/Booking.php');
include_once(dirname(__FILE__).'/../database/dbBookings.php');
include_once(dirname(__FILE__).'/../domain/Loaner.php');
include_once(dirname(__FILE__).'/../database/dbLoaners.php');
class testdbBookings extends UnitTestCase {
    function testdbBookingsModule() {
        
        // create a booking and test inserting and retrieving it from dbBookings
        $today = date('y-m-d');
        $b = new Booking($today,"","Meghan2075551234","pending","","Tiny",
                  array("Meghan:mother", "Jean:father", "Teeny:sibling"),
                  array(), "", "", "Millie2073631234","Maine Med", "SCU", "00000000000",
                   "$10 per night", "","","","new");
        $this->assertTrue(insert_dbBookings($b));
        $this->assertTrue(retrieve_dbBookings($b->get_id()));
        //$this->assertTrue(in_array("Meghan2077291234", $b->get_guest_id()));
        
        //checks that the initial status is "pending"
        $this->assertTrue(retrieve_dbBookings($b->get_id())->get_status(), "pending");
        
        //checks that initial flag is "new"
        $this->assertTrue(retrieve_dbBookings($b->get_id())->get_flag(), "new");
        $pending_bookings = retrieve_all_pending_dbBookings();
 //       $this->assertEqual($pending_bookings[0]->get_id(), $b->get_id());
        
        // make some changes and test updating it in the database
        // Add a loaner to the booking
		$b->add_loaner("remote3");
		$b->add_occupant("Jordan","brother");
		$b->set_flag("viewed");
        $this->assertTrue($b->assign_room("126",$today));
        $bretrieved = retrieve_dbBookings($b->get_id());
        $this->assertTrue(in_array("Jordan: brother", $bretrieved->get_occupants()));
        $this->assertTrue(in_array("remote3", $bretrieved->get_loaners()));
        $this->assertEqual($bretrieved->get_status(),"active");
        $this->assertEqual($bretrieved->get_id(), $b->get_id());
        $this->assertEqual($bretrieved->get_room_no(), "126");
        $today = date('y-m-d');
        $this->assertEqual($bretrieved->get_date_in(), $today);
        $this->assertEqual($bretrieved->get_flag(), "viewed");
        
        //tests updating after a checkout
        $this->assertTrue($bretrieved->check_out($today));
        $bretrieved2 = retrieve_dbBookings($b->get_id());
        $this->assertEqual($bretrieved2->get_status(), "closed");
        $this->assertEqual($bretrieved2->get_date_out(),$today);
        
        //tests the delete function
        $this->assertTrue(delete_dbBookings($b->get_id()));
        $this->assertFalse(retrieve_dbBookings($b->get_id()));
        
        
                 
        echo ("testdbBookings complete");
    }
}

?>

