<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/**
 * Functions to insert, delete, retrieve, and update information
 * from the dbRoomLogs table in the database. This table is used
 * with the roomLog class
 * @version 3/4/11
 * @author Jesus
 */

// We must include the dbRoom.php so that we
// also add rooms
include_once(dirname(__FILE__).'/../domain/RoomLog.php');
include_once(dirname(__FILE__).'/../domain/Booking.php');
include_once(dirname(__FILE__).'/../domain/Room.php');
include_once(dirname(__FILE__).'/dbRooms.php');
include_once(dirname(__FILE__).'/dbBookings.php');
include_once(dirname(__FILE__).'/dbinfo.php');


/**
 * Create the dbRoomLogs table in the database with the following fields:
 * id: "yy-mm-dd": the RoomLog's unique key
 * rooms: room_no:booking_id pairs separated by commas
 * log_notes: manager's notes about the room log
 * status: "unpublished," "published," or "archived"
 */
function create_dbRoomLogs(){
	//Connect to the server
	$con=connect();
	// Check if the table exists already
	mysqli_query($con,"DROP TABLE IF EXISTS dbRoomLogs");
	// Create the table and store the result
	$result = mysqli_query($con,"CREATE TABLE dbRoomLogs (
						id VARCHAR(25) NOT NULL,
						rooms TEXT,
						log_notes TEXT,
						status TEXT,
						PRIMARY KEY (id))");
	// Check if the creation was successful
	if(!$result){
		// Print an error
		echo mysqli_error($con). ">>>Error creating dbRoomLogs table <br>";
		mysqli_close($con);
		return false;
	}
	mysqli_close($con);
	return true;
}

function build_room_log($date){
    // Connect to the database
    $con=connect();
    // Check if the room log already exists
    $query = "SELECT * FROM dbRoomLogs WHERE id = '".$date."'";
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    // If room log does not yet exist
 //   if(mysqli_num_rows($result) == 0){
    	// rebuild the past room log using functions in the RoomLog class
    	$new_roomLog = new RoomLog($date);
    	insert_dbRoomLog($new_roomLog);
        /*
        $query = "SELECT * FROM dbBookings WHERE room_no <> '' AND '".$date."' >= date_in AND ('".$date."' < date_out OR date_out = '')";
        $result = mysqli_query($con,$query);
        var_dump($result);
        $all_rooms = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $theBooking = build_booking($result_row);
            $all_rooms[] = $theBooking->get_room_no().":".$theBooking->get_id();
        }
        
        $query = "INSERT INTO dbRoomLogs VALUES('".$date."','".implode(',',$all_rooms)."','','')";
        $result = mysqli_query($con,$query);
        // Check if succesful
        if(!$result) {
            //print the error
            echo mysqli_error($con)." Could not insert into dbRoomLogs :".$date."\n";
            mysqli_close($con);
            return false;
        }
        */
//    }
    return retrieve_dbRoomLog($date);
}

/**
 * Function to insert a new RoomLog into the database
 */
function insert_dbRoomLog($roomLog){
	// Check if the roomlog was actually a room log
	if(!$roomLog instanceof RoomLog){
		// Print an error
		echo ("Invalid argument from insert_dbRoomLog function\n");
		return false;
	}
	// Connect to the database
	$con=connect();
	// Check if the roomLog already exists
	$query = "SELECT * FROM dbRoomLogs WHERE id ='".$roomLog->get_id()."'";
	$result=mysqli_query($con,$query);
	// If it exists, delete it, then replace it
	if(mysqli_num_rows($result) != 0){
		delete_dbRoomLog($roomLog->get_id());
		// Reconnect because deleting disconnects us.
		$con=connect();
	}
	// Now add it to the database
	$query="INSERT INTO dbRoomLogs VALUES('".
			$roomLog->get_id()."','".
			implode(',',$roomLog->get_rooms())."','".
			$roomLog->get_log_notes()."','".
			$roomLog->get_status()."')";
			
	$result=mysqli_query($con,$query);
	// Check if succesful
	if(!$result) {
		//print the error
		echo mysqli_error($con)." Could not insert into dbRoomLogs :".$roomLog->get_id()."\n";
		mysqli_close($con);
		return false;
	}
	// Sucess. 
	mysqli_close($con);
	return true;
}

/**
 * Function to retrieve a roomlog from the dbRoomlogs database
 * @param $id the room log id
 * @return mysql entry that corresponds to the room log, or else false
 */
function retrieve_dbRoomLog($roomLogID){
	// connect to the mysql server
	$con=connect();
	// Retrieve the entry
	$query = "SELECT * FROM dbRoomLogs WHERE id ='".$roomLogID."'";
	$result = mysqli_query($con,$query);
	// check if successful
	if(mysqli_num_rows($result) !==1){
		mysqli_close($con);
		return false;
	}
	// Store the result
	$result_row = mysqli_fetch_assoc($result);
	mysqli_close($con);
	// Create a new room log from the information given
	$theRoomLog = new RoomLog($result_row['id']);
	
	// Replace the rooms in the room log
	$roomsString = $result_row['rooms'];
	$rooms = explode(",",$roomsString);
	$theRoomLog->set_rooms($rooms);
	
	// Add extra information if present
	if($result_row['log_notes']){
		$theRoomLog->set_log_notes($result_row['log_notes']);
	}
	if($result_row['status']){
		$theRoomLog->set_status($result_row['status']);
	}
	
	// return the roomlog
	return $theRoomLog;
}

/* retrieve most recent roomlog at or prior to the given date (yy-mm-dd)
 */
function retrieve_mostrecent_dbRoomLog ($date) {
	// connect to the mysql server
	$con=connect();
	// Retrieve the entry
	$query = "SELECT * FROM dbRoomLogs WHERE id <='".$date."' ORDER BY id DESC";
	$result = mysqli_query($con,$query);
	// check if successful
	if(mysqli_num_rows($result) == 0){
		mysqli_close($con);
		return false;
	}
	// Store the first row of the result = the most recent room log
	$result_row = mysqli_fetch_assoc($result);
	mysqli_close($con);
	// Create a new room log from the information given
	$theRoomLog = new RoomLog($result_row['id']);
	
	// Replace the rooms in the room log
	$roomsString = $result_row['rooms'];
	$rooms = explode(",",$roomsString);
	$theRoomLog->set_rooms($rooms);
	
	// Add extra information if present
	if($result_row['log_notes']){
		$theRoomLog->set_log_notes($result_row['log_notes']);
	}
	if($result_row['status']){
		$theRoomLog->set_status($result_row['status']);
	}
	// return the roomlog
	return $theRoomLog;
		
}

/**
 * function to update an entry in the dbRoomLogs database
 * @param $roomLog the room log to update
 */
function update_dbRoomLog($roomLog){
	// check if the room log is a room log
	if(!$roomLog instanceof RoomLog){
		//print an error
		echo ("Invalid argument for update dbRoomLog function\n");
		return false;
	}
	
	// find the roomlog in the database
	if(delete_dbRoomLog($roomLog->get_id())){
		return insert_dbRoomLog($roomLog);
		// Update every room in the room log as well
	}else{
		echo mysqli_error($con)." unable to update dbRoomLog :".$roomLog->get_id()."\n";
		return false;
	}
}

/**
 * function to delete a dbRoomLog entry from the database
 * @param $roomLogID the id of the room log
 */
function delete_dbRoomLog($roomLogID){
	// connect to the database
	$con=connect();
	// first grab the rooms of the room log
	$query = "SELECT * FROM dbRoomLogs WHERE id ='".$roomLogID."'";
	$result = mysqli_query($con,$query);
	if(!$result){
		// print an error
		echo mysqli_error($con)." could not delete rooms from room log: ".$roomLogID."\n";
		return false;
	}
	// create an array from the rooms
	$result_row = mysqli_fetch_assoc($result);
	$rooms = explode(',',$result_row['rooms']);
	// delete each room
	foreach($rooms as $roomToDelete){
		if(!delete_dbRooms($roomToDelete)){
			//error
			echo mysqli_error($con)." could not delete a room from roomLog\n";
			return false;
		}
		$con=connect();
	}
		
	// Delete the entry
	$query="DELETE FROM dbRoomLogs WHERE id ='".$roomLogID."'";
	$result = mysqli_query($con,$query);
	// Check if successful
	if(!$result){
		//print an error
		echo mysqli_error($con)." Unable to delete dbRoomLog :".$roomLogID."\n";
		return false;
	}
	//Success
	return true;
}