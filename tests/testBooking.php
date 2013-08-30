<?php
include_once(dirname(__FILE__).'/../domain/Booking.php');
class testBooking extends UnitTestCase {
      function testBookingModule() {
         $today = date('y-m-d');
        $b = new Booking($today,"","Meghan2075551234","pending","","Tiny",
                  array("Meghan:mother", "Jean:father", "Teeny:sibling"),
                  array(), "", "", "Millie2073631234","Maine Med", "SCU", "00000000000",
                   "$10 per night", "","","","new");
         
         $this->assertTrue($b->get_id() == $today."Meghan2075551234");
         $this->assertEqual($b->get_date_submitted(),$today);
         $this->assertTrue($b->get_date_in() == "");
         $this->assertEqual($b->get_guest_id(),"Meghan2075551234");
         $this->assertEqual($b->get_status(),"pending");
         $this->assertEqual($b->get_room_no(),"");
         $this->assertEqual($b->get_patient(),"Tiny");
         $occ = $b->get_occupants();
         $this->assertTrue(in_array("Jean:father",$occ));
         $this->assertEqual($b->get_linked_room(),"");
         $this->assertEqual($b->get_date_out(),"");
         $this->assertEqual($b->get_referred_by(),"Millie2073631234");
         $this->assertEqual($b->get_hospital(),"Maine Med");
         $this->assertEqual($b->get_department(),"SCU");
         $this->assertEqual($b->get_payment_arrangement(),"$10 per night");
         $this->assertEqual($b->overnight_use(), "");
         $this->assertEqual($b->day_use(),"");
         $this->assertEqual($b->get_mgr_notes(), "");
         $this->assertEqual($b->get_flag(),"new");
         for ($i=0;$i<11;$i++)
         	$this->assertEqual($b->get_health_question($i),"0"); 
         $b->add_occupant("Jordan","brother");
         $this->assertEqual(sizeof($b->get_occupants()), 4);
         $b->remove_occupant("Jordan");
         $this->assertEqual(sizeof($b->get_occupants()), 3);
       
      	echo ("testBooking complete");
  	  }
}

?>
