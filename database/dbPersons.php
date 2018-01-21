<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
/*
 * dbPersons module for Homeroom
 * @author Alex Lucyk
 * @version May 1, 2011
 */

include_once(dirname(__FILE__).'/../domain/Person.php');
include_once(dirname(__FILE__).'/dbinfo.php');

function insert_dbPersons ($person){
    if (! $person instanceof Person) {
        return false;
    }
    $con=connect();

	$query = "SELECT * FROM dbPersons WHERE id = '" . $person->get_id() . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) != 0) {
        delete_dbPersons ($person->get_id());
        $con=connect();
    }

    $query = "INSERT INTO dbPersons VALUES ('".
                $person->get_id()."','" . 
                $person->get_first_name()."','".
                $person->get_last_name()."','".
                $person->get_gender()."','".
                $person->get_employer()."','".
                $person->get_address()."','".
                $person->get_city()."','".
                $person->get_state()."','".
                $person->get_zip()."','".
                $person->get_phone1()."','".
                $person->get_phone2()."','".
                $person->get_email()."','".
                implode(',',$person->get_patient_name())."','".
                $person->get_patient_birthdate()."','".
                $person->get_patient_gender()."','".
                $person->get_patient_relation()."','".
                implode(',',$person->get_prior_bookings())."','".
                $person->get_mgr_notes()."','".
                $person->get_county()."','".
                implode(',',$person->get_type())."','".
                $person->get_password().
                "');";
    $result = mysqli_query($con,$query);
    if (!$result) {
        echo (mysqli_error($con). " unable to insert into dbPersons: " . $person->get_id(). "\n");
        mysqli_close($con);
        return false;
    }
    mysqli_close($con);
    return true;
}
                
function retrieve_dbPersons ($id) {
	$con=connect();
    $query = "SELECT * FROM dbPersons WHERE id = '".$id."'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1){
    	mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    $thePerson = new Person($result_row['last_name'], $result_row['first_name'],
     	$result_row['gender'], $result_row['employer'], $result_row['address'], 
        $result_row['city'],$result_row['state'], $result_row['zip'],
        $result_row['phone1'], $result_row['phone2'], $result_row['email'],
        $result_row['type'], $result_row['prior_bookings'], $result_row['patient_name'],
        $result_row['patient_birthdate'],$result_row['patient_gender'],$result_row['patient_relation'],
        $result_row['password']);
    $thePerson->set_mgr_notes($result_row['mgr_notes']);
    $thePerson->set_county($result_row['county']);
//    mysqli_close($con); 
    return $thePerson;   
}
function getall_persons () {
    $con=connect();
    $query = "SELECT * FROM dbPersons ORDER BY last_name";
    $result = mysqli_query($con,$query);
    $thePersons = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $thePerson = new Person($result_row['last_name'], $result_row['first_name'],
         	$result_row['gender'], $result_row['employer'], $result_row['address'], 
            $result_row['city'],$result_row['state'], $result_row['zip'],
            $result_row['phone1'], $result_row['phone2'], $result_row['email'],
            $result_row['type'], $result_row['prior_bookings'], $result_row['patient_name'],
            $result_row['patient_birthdate'],$result_row['patient_gender'],$result_row['patient_relation'],
            $result_row['password']);
        $thePerson->set_mgr_notes($result_row['mgr_notes']);
        $thePerson->set_county($result_row['county']);
        $thePersons[] = $thePerson;
    }
 //   mysqli_close($con);
    return $thePersons; 
} 

function getall_type($type) {
   $con=connect();
    $query = "SELECT * FROM dbPersons WHERE type like '%".$type."%' ORDER BY last_name";
    $result = mysqli_query($con,$query);
    $thePersons = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $thePerson = new Person($result_row['last_name'], $result_row['first_name'],
         	$result_row['gender'], $result_row['employer'], $result_row['address'], 
            $result_row['city'],$result_row['state'], $result_row['zip'],
            $result_row['phone1'], $result_row['phone2'], $result_row['email'],
            $result_row['type'], $result_row['prior_bookings'], $result_row['patient_name'],
            $result_row['patient_birthdate'],$result_row['patient_gender'],$result_row['patient_relation'],
            $result_row['password']);
        $thePerson->set_mgr_notes($result_row['mgr_notes']);
        $thePerson->set_county($result_row['county']);
        $thePersons[] = $thePerson;
    }
 //   mysqli_close($con);
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
	   echo (mysqli_error($con)."unable to update dbPersons table: ".$person->get_id());
	   return false;
	}
}

function delete_dbPersons($id) {
	$con=connect();
    $query="DELETE FROM dbPersons WHERE id=\"".$id."\"";
	$result=mysqli_query($con,$query);
	mysqli_close($con);
	if (!$result) {
		echo (mysqli_error($con)." unable to delete from dbPersons: ".$id);
		return false;
	}
    return true;
}

    
