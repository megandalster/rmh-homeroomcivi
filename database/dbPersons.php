<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
/*
 * dbPersons module for RMH Homeroom
 * @author Alex Lucyk
 * @version May 1, 2011
 */

include_once(dirname(__FILE__).'/../domain/Person.php');
include_once(dirname(__FILE__).'/dbinfo.php');

function create_dbPersons() {
    connect();
    mysql_query("DROP TABLE IF EXISTS dbPersons");
    $result = mysql_query("CREATE TABLE dbPersons (id TEXT NOT NULL, first_name TEXT, last_name TEXT, address TEXT,
    					  city TEXT, state TEXT, zip TEXT, phone1 VARCHAR(12) NOT NULL, phone2 VARCHAR(12), email TEXT,
    					  patient_name TEXT, patient_birthdate TEXT, patient_relation TEXT, prior_bookings TEXT,
    					  mgr_notes TEXT, county TEXT, type TEXT, password TEXT)");
    mysql_close();
    if (!$result) {
        echo mysql_error() . "Error creating dbPersons table. <br>";
        return false;
    }
    return true;
}

function insert_dbPersons ($person){
    if (! $person instanceof Person) {
        return false;
    }
    connect();

	$query = "SELECT * FROM dbPersons WHERE id = '" . $person->get_id() . "'";
    $result = mysql_query($query);
    if (mysql_num_rows($result) != 0) {
        delete_dbPersons ($person->get_id());
        connect();
    }

    $query = "INSERT INTO dbPersons VALUES ('".
                $person->get_id()."','" . 
                $person->get_first_name()."','".
                $person->get_last_name()."','".
                $person->get_address()."','".
                $person->get_city()."','".
                $person->get_state()."','".
                $person->get_zip()."','".
                $person->get_phone1()."','".
                $person->get_phone2()."','".
                $person->get_email()."','".
                implode(',',$person->get_patient_name())."','".
                $person->get_patient_birthdate()."','".
                $person->get_patient_relation()."','".
                implode(',',$person->get_prior_bookings())."','".
                $person->get_mgr_notes()."','".
                $person->get_county()."','".
                implode(',',$person->get_type())."','".
                $person->get_password().
                "');";
    $result = mysql_query($query);
    if (!$result) {
        echo (mysql_error(). " unable to insert into dbPersons: " . $person->get_id(). "\n");
        mysql_close();
        return false;
    }
    mysql_close();
    return true;
}
                
function retrieve_dbPersons ($id) {
	connect();
    $query = "SELECT * FROM dbPersons WHERE id = '".$id."'";
    $result = mysql_query ($query);
    if (mysql_num_rows($result) !== 1){
    	mysql_close();
        return false;
    }
    $result_row = mysql_fetch_assoc($result);
    $thePerson = new Person($result_row['last_name'], $result_row['first_name'], $result_row['address'], 
        $result_row['city'],$result_row['state'], $result_row['zip'],
        $result_row['phone1'], $result_row['phone2'], $result_row['email'],
        $result_row['type'], $result_row['prior_bookings'], $result_row['patient_name'],
        $result_row['patient_birthdate'],$result_row['patient_relation'],
        $result_row['password']);
    $thePerson->set_mgr_notes($result_row['mgr_notes']);
    $thePerson->set_county($result_row['county']);
//    mysql_close(); 
    return $thePerson;   
}
function getall_persons () {
    connect();
    $query = "SELECT * FROM dbPersons ORDER BY last_name";
    $result = mysql_query ($query);
    $thePersons = array();
    while ($result_row = mysql_fetch_assoc($result)) {
        $thePerson = new Person($result_row['last_name'], $result_row['first_name'], $result_row['address'], 
            $result_row['city'],$result_row['state'], $result_row['zip'],
            $result_row['phone1'], $result_row['phone2'], $result_row['email'],
            $result_row['type'], $result_row['prior_bookings'], $result_row['patient_name'],
            $result_row['patient_birthdate'],$result_row['patient_relation'],
            $result_row['password']);
        $thePerson->set_mgr_notes($result_row['mgr_notes']);
        $thePerson->set_county($result_row['county']);
        $thePersons[] = $thePerson;
    }
 //   mysql_close();
    return $thePersons; 
}

function update_dbPersons ($person) {
if (! $person instanceof Person) {
		echo ("Invalid argument for update_dbPersons function call");
		return false;
	}
	if (delete_dbPersons($person->get_id()))
	   return insert_dbPersons($person);
	else {
	   echo (mysql_error()."unable to update dbPersons table: ".$person->get_id());
	   return false;
	}
}

function delete_dbPersons($id) {
	connect();
    $query="DELETE FROM dbPersons WHERE id=\"".$id."\"";
	$result=mysql_query($query);
	mysql_close();
	if (!$result) {
		echo (mysql_error()." unable to delete from dbPersons: ".$id);
		return false;
	}
    return true;
}

    
