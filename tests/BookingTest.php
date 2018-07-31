<?php
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__).'/../domain/Booking.php');
class BookingTest extends TestCase {
    function testBooking() {
        $today = date('y-m-d');
        $b = new Booking($today,"","Meghan2075551234","pending","",array("Tiny"),
                  array("Meghan:mother", "Jean:father", "Teeny:sibling"),
                  "", "", "", "Millie2073631234","Maine Med", "SCU", "00000000000",
                   "$10 per night", "","","","","new");
         
        $this->assertTrue($b->get_id() == $today."Meghan2075551234");
        $this->assertEquals($b->get_date_submitted(),$today);
        $this->assertTrue($b->get_date_in() == "");
        $this->assertEquals($b->get_guest_id(),"Meghan2075551234");
        $this->assertEquals($b->get_status(),"pending");
        $this->assertEquals($b->get_room_no(),"");
        $this->assertEquals($b->getith_patient(0),"Tiny");
        $occ = $b->get_occupants();
        $this->assertTrue(in_array("Jean:father",$occ));
        $this->assertEquals($b->get_linked_room(),"");
        $this->assertEquals($b->get_date_out(),"");
        $this->assertEquals($b->get_referred_by(),"Millie2073631234");
        $this->assertEquals($b->get_hospital(),"Maine Med");
        $this->assertEquals($b->get_department(),"SCU");
        $this->assertEquals($b->get_payment_arrangement(),"$10 per night");
        $this->assertEquals($b->overnight_use(), "");
        $this->assertEquals($b->day_use(),"");
        $this->assertEquals($b->get_mgr_notes(), "");
        $this->assertEquals($b->get_flag(),"new");
        for ($i=0;$i<11;$i++)
            $this->assertEquals($b->get_health_question($i),"0"); 
        $b->add_occupant("Jordan","brother","","");
        $this->assertEquals(sizeof($b->get_occupants()), 4);
        $b->remove_occupant("Jordan");
        $this->assertEquals(sizeof($b->get_occupants()), 3);
    }
}
?>
