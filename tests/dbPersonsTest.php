<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
/*
 * testdbPersons class for RMH Homeroom
 * @author Alex Lucyk
 * @version May 1, 2011
 */
use PHPUnit\Framework\TestCase;
include_once(dirname(__FILE__).'/../domain/Person.php');
include_once(dirname(__FILE__).'/../database/dbPersons.php');
class dbPersonsTest extends TestCase {
    function testdbPersonsModule() {
        
        //creates some people to add to the database
        $person1 = new Person("Smith", "John", "male", "", "123 College Street","Ashburn", "VA", "20147", 7035551234, "", 
    				           "email@bowdoin.edu", "guest", "", "Jane Smith", "98-01-01", "Female", "", "");
        $person2 = new Person("Adams", "Will", "male", "", "12 River Road","Ashburn", "VA", "20147", 703551212, 7035553434, 
    				           "wadams@yahoo.com", "socialworker", "", null, null, null, "", "" );
        
        // Setup -- test the insert function
        $this->assertTrue(insert_dbPersons($person1));
   //     $this->assertTrue(insert_dbPersons($person2));              
        
        // Test -- test the retrieve and update functions
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_id (), "John7035551234");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_first_name (), "John");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_last_name (), "Smith");    
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_address(), "123 College Street");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_city (), "Ashburn");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_state (), "VA");    
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_zip(), "20147");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_phone1 (), 7035551234);
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_phone2 (), null);    
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_email(), "email@bowdoin.edu");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->getith_patient_name (0), "Jane Smith");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_patient_birthdate (), "98-01-01");    
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_patient_gender(), "Female");
        $this->assertTrue(retrieve_dbPersons($person1->get_id())->check_type("guest"));
                 
        $person1->set_address("5 Maine Street");
        $this->assertTrue(update_dbPersons($person1));
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_address (), "5 Maine Street");   
   /*      
        $this->assertFalse(retrieve_dbPersons($person2->get_id())->check_type("guest"));
        $person2->add_type("guest");
        $this->assertTrue(update_dbPersons($person2));
        $this->assertTrue(retrieve_dbPersons($person2->get_id())->check_type("guest"));
        $this->assertTrue(retrieve_dbPersons($person2->get_id())->check_type("socialworker"));
   */              
        // Teardown -- tests the delete function
        $this->assertTrue(delete_dbPersons($person1->get_id()));
   //     $this->assertTrue(delete_dbPersons($person2->get_id()));
        $this->assertFalse(retrieve_dbPersons($person2->get_id()));
    }
}

?>

