<?php
include_once(dirname(__FILE__).'/../domain/Person.php');
class testPerson extends UnitTestCase {
    function testPersonModule() {
        $this->assertEqual(true,true);
        $this->assertTrue(true);
        $this->assertFalse(false);
             
        //fake person to test
        $test_person = new Person("Smith", "John", "123 College Street","Brunswick", "ME", "04011", "(207)555-1234", "", 
    				"email@bowdoin.edu", "guest", "","Jane Smith", "98-01-01", "parent" ,"");
                 
        //testing getter functions
        $this->assertTrue($test_person->get_first_name() == "John");
        $this->assertTrue($test_person->get_last_name() == "Smith");
        $this->assertTrue($test_person->get_address() == "123 College Street");
        $this->assertTrue($test_person->get_city() == "Brunswick");
        $this->assertTrue($test_person->get_state() == "ME");
        $this->assertTrue($test_person->get_zip() == "04011");
        $this->assertTrue($test_person->get_phone1() == "(207)555-1234");
        $this->assertTrue($test_person->get_phone2() == "");
        $this->assertTrue($test_person->get_email() == "email@bowdoin.edu");
        $this->assertTrue($test_person->get_patient_name() == "Jane Smith");
        $this->assertTrue($test_person->get_patient_birthdate() == "98-01-01");
        $this->assertTrue($test_person->get_patient_relation() == "parent");
                 
        //tests the 'check_type' function
        $this->assertTrue($test_person->check_type("guest"));
                 
        //checks that the checkin dates array returns false correctly
        $this->assertFalse(in_array("11-02-01",$test_person->get_prior_bookings()));
                 
        //adds a checkin date and checks that it was added to the array
        $test_person->add_prior_booking("10-05-01John2075551234");
        $this->assertTrue(in_array("10-05-01John2075551234",$test_person->get_prior_bookings()));
                 
        //tests adding and checking type
        $this->assertFalse($test_person->check_type("social worker"));
        $test_person->add_type("social worker");
        $this->assertTrue($test_person->check_type("social worker"));
                 
        //checks the manager notes getter and setter
        $this->assertFalse($test_person->get_mgr_notes(), "note");
        $test_person->set_mgr_notes("here is a note");
        $this->assertEqual($test_person->get_mgr_notes(), "here is a note");
                 
        echo ("testPerson complete");
    }
}

?>
