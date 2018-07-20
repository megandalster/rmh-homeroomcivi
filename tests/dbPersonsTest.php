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
        $person1 = new Person("Smith", "John", "male", "", "123 College Street","Brunswick", "ME", "04011", 2075551234, "", 
    				           "email@bowdoin.edu", "guest", "", "Jane Smith", "98-01-01", "Female", "", "");
        $person2 = new Person("Jones", "Bob", "male", "", "100 Union Street","Bangor", "ME", "04401", 2075555678, null, 
    				           "bjones@gmail.com", "guest", "", "Dan Jones", "95-07-15", "Male", "", "" );
        $person3 = new Person("Adams", "Will", "male", "", "12 River Road","Augusta", "ME", "04330", 207551212, 2075553434, 
    				           "wadams@yahoo.com", "socialworker", "", null, null, null, "", "" );
        
        // tests the insert function
        $this->assertTrue(insert_dbPersons($person1));
        $this->assertTrue(insert_dbPersons($person2));
        $this->assertTrue(insert_dbPersons($person3));              
        
        //tests the retrieve function
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_id (), "John2075551234");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_first_name (), "John");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_last_name (), "Smith");    
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_address(), "123 College Street");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_city (), "Brunswick");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_state (), "ME");    
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_zip(), "04011");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_phone1 (), 2075551234);
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_phone2 (), null);    
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_email(), "email@bowdoin.edu");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->getith_patient_name (0), "Jane Smith");
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_patient_birthdate (), "98-01-01");    
        $this->assertEquals(retrieve_dbPersons($person1->get_id())->get_patient_gender(), "Female");
        $this->assertTrue(retrieve_dbPersons($person1->get_id())->check_type("guest"));
                 
        //tests the update function
        $person2->set_address("5 Maine Street");
        $this->assertTrue(update_dbPersons($person2));
        $this->assertEquals(retrieve_dbPersons($person2->get_id())->get_address (), "5 Maine Street");   
         
        $this->assertFalse(retrieve_dbPersons($person3->get_id())->check_type("guest"));
        $person3->add_type("guest");
        $this->assertTrue(update_dbPersons($person3));
        $p3 = retrieve_dbPersons($person3->get_id());
        $a = $p3->get_type();
        $this->assertTrue(retrieve_dbPersons($person3->get_id())->check_type("guest"));
        $this->assertTrue(retrieve_dbPersons($person3->get_id())->check_type("socialworker"));
                 
        //tests the delete function
        $this->assertTrue(delete_dbPersons($person1->get_id()));
        $this->assertTrue(delete_dbPersons($person2->get_id()));
        $this->assertTrue(delete_dbPersons($person3->get_id()));
        $this->assertFalse(retrieve_dbPersons($person3->get_id()));
                 
        echo ("testdbPersons complete\n");
    }
}

?>

