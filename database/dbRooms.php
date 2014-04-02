<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/**
 * Functions used to create and modify the dbRoom database.
 * @version February 27, 2011
 * @author Jesus
 */

include_once(dirname(__FILE__).'/../domain/Room.php');
include_once(dirname(__FILE__).'/dbinfo.php');
include_once(dirname(__FILE__).'/dbBookings.php');

/**
 * Creates a dbRooms table with the following parameters:
 * room_no: the room number eg "233"
 * beds: bed configuration: string of 2T, 1Q, Q, etc.
 * capacity: maximum number of guests in a room
 * bath: "y" or "n" depending if a private bath is available
 * status: string of either "clean", "dirty", "booked", "off-line"
 * booking: the current booking id for this room
 * room_notes: any special comments about a room
 */
function create_dbRooms(){
	// Connect to the database
	connect();
	mysql_query("DROP TABLE IF EXISTS dbRooms");
	// Create the tables.
	$result = mysql_query("CREATE TABLE dbRooms (
							room_no VARCHAR(25) NOT NULL,
							beds TEXT,
							capacity VARCHAR(8),
							bath TEXT,
							status TEXT,
							booking text,
							room_notes TEXT,
							PRIMARY KEY (room_no))");
	// check if the creation was successful
	if(!$result){
		// Print the error
		echo mysql_error() . ">>>Error creating dbRooms table. <br>";
		return false;
	}
	$room_data = array ("125y2T"=>2,"126yQ/3T"=>4,"151y2T"=>2,"152y2T"=>2,"214nQ"=>2,"215n2T"=>2,"218yQ"=>2,
	"223nQ"=>2,"224n3T"=>3,"231y2T"=>2,"232n2T"=>2,"233n3T"=>3,"243yQ/3T"=>4,"244nQ"=>2,
	"245nQ"=>2,"250y2T"=>2,"251y2T"=>2,"252yQ"=>2,"253yQ"=>2,"254y2T"=>2,"255y2T"=>2, "UNKnQ"=>2);// Initialize all 21 rooms as clean and unbooked. 
	foreach ($room_data as $room_no => $capacity){
		$bath = substr($room_no, 3, 1);
		$beds = substr($room_no, 4);
		$room_no = substr($room_no, 0, 3);
		$a_room = new Room($room_no, $beds, $capacity, $bath, "clean", null, "");
		insert_dbRooms($a_room);
	}
	// Creation was succesful. Return true
	return true;
}
/*
 * retrieve all room_no:booking_id pairs for a given date
 */
function retrieve_all_rooms($date) {
    $room_data = array ("125y2T"=>2,"126yQ/3T"=>4,"151y2T"=>2,"152y2T"=>2,"214nQ"=>2,"215n2T"=>2,"218yQ"=>2,
	"223nQ"=>2,"224n3T"=>3,"231y2T"=>2,"232n2T"=>2,"233n3T"=>3,"243yQ/3T"=>4,"244nQ"=>2,
	"245nQ"=>2,"250y2T"=>2,"251y2T"=>2,"252yQ"=>2,"253yQ"=>2,"254y2T"=>2,"255y2T"=>2);
    if ($date >= date('y-m-d'))
    	$active_bookings = retrieve_active_dbBookings($date);
    else 
    	$active_bookings = retrieve_past_active_dbBookings($date);
    	
    $my_rooms = array();
	foreach ($room_data as $room => $capacity){
	    $room_no = substr($room, 0, 3);
	    if (isset($active_bookings[$room_no]))
		    $my_rooms[] = $room_no . ":" . $active_bookings[$room_no];
		else $my_rooms[] =  $room_no . ":";
	}
	
	//After getting all of the regular bookings we find the day use boookings
	if ($date >= date('y-m-d'))
	{
		$day_use_pendingBookings = retrieve_pendingDayUse_dbBookings($date);		
		$day_use_activeBookings = retrieve_active_day_use_dbBookings($date);
		$total_day_use_rooms_to_make = count($day_use_activeBookings) + count($day_use_pendingBookings);
		$numOfActive =  count($day_use_activeBookings);
		for($i=0; $i<($total_day_use_rooms_to_make/7); $i++)
		{
			for($j=0; $j<7; $j++)
			{
				$room_num = "0".$i.$j;
				if( (7*$i+$j) == ($total_day_use_rooms_to_make))
				{
					break;
				}
				
				if(isset($day_use_activeBookings[$room_num]))
				{					
					$my_rooms[] = $room_num . ":" . $day_use_activeBookings[$room_num];
					//since its an active booking retrieve its room's status.
					$currentRoom = retrieve_dbRooms($room_num,$date,$day_use_activeBookings[$room_num]);
					$currentStatus = $currentRoom->get_status();
					$currentBookingID = $currentRoom->get_booking_id();
					
					$day_use_room = new Room($room_num, null, null, null, $currentStatus, $currentBookingID, "");
				}
				else
				{
					$my_rooms[] =  $room_num . ":";
					$day_use_room = new Room($room_num, null, null, null, "clean", null, "");
				}
					
				insert_dbRooms($day_use_room);
			}
		}
	}
	
	else
	{
		$day_use_past_active_bookings = retrieve_past_active_day_use_dbBookings($date);
		$total_day_use_rooms_to_make = count($day_use_past_active_bookings);
		
		for($i=0; $i<$total_day_use_rooms_to_make/7; $i++)
		{
			for($j=0; $j<7; $j++)
			{
				if( (7*$i+$j) == ($total_day_use_rooms_to_make))
				{
					break;
				}
				$room_num = "0".$i.$j;
				if(isset($day_use_past_active_bookings[$room_num]))
				{
					$my_rooms[] = $room_num . ":" . $day_use_past_active_bookings[$room_num];
					$currentRoom = retrieve_dbRooms($room_num,$date,$day_use_past_active_bookings[$room_num]);
					$currentStatus = $currentRoom->get_status();
					$currentBookingID = $currentRoom->get_booking_id();
					
					$day_use_room = new Room($room_num, null, null, null, $currentStatus, $currentBookingID, "");
				}
				else
				{
					$my_rooms[] =  $room_num . ":";
					$day_use_room = new Room($room_num, null, null, null, "clean", null, "");
				}
				insert_dbRooms($day_use_room);
			}
		}
	}
	return $my_rooms;
}

/**
 * Insert a room into the dbRooms table
 * @param $room = the room to insert
 */
function insert_dbRooms($room){
	// Check if the room is actually a room
	if(!($room instanceof Room)){
		// Print an error
		echo ("Invalid argument for insert_dbRooms function call");
		return false;
	}
	// Connect to the database
	connect();
	// check if the entry already exists
	$query = "Select * FROM dbRooms WHERE room_no = '".$room->get_room_no()."'";
	

		
	$result = mysql_query($query);
	if(mysql_num_rows($result) != 0){
		// if it exists, delete it
		delete_dbRooms($room->get_room_no());
		// Reconnect
		connect();
	}		
	// Insert the room into the database
	$query="INSERT INTO dbRooms VALUES ('".
			$room->get_room_no()."','".
			$room->get_beds()."','".
			$room->get_capacity()."','".
			$room->get_bath()."','".
			$room->get_status()."','".
			$room->get_booking_id()."','".
			$room->get_room_notes()."')";
	// Execute the query
	$result = mysql_query($query);
	// Check if succesful
	if(!$result){
		//print an error if it didnt work
		echo (mysql_error()."unable to insert into dbRooms: ".$room->get_room_no()."\n");
		mysql_close();
		return false;
	}
	// Close connection
	mysql_close();
	return true;
}

/**
 * Retrieves a Room from the dbRooms database
 * @param $room_no the room number to retrieve
 * @return mysql entry for the room number, or false
 */
function retrieve_dbRooms($room_no,$date,$currentBookingID){
	// connect to the database
	connect();
	// Search for the entry
	$query="SELECT * FROM dbRooms WHERE room_no =\"".$room_no."\"";
	$result = mysql_query($query);
	
	// check if it was found
	if(mysql_num_rows($result) !==1){
		// It wasnt found. 
		echo ("Room ".$room_no." was not found in the database.");
		mysql_close();
		return false;
	}
	
	// Return the entry
	$result_row = mysql_fetch_assoc($result);
	mysql_close();
	if ($date==date('y-m-d')) 
	    $theRoom = new Room($result_row['room_no'],
						$result_row['beds'],
						$result_row['capacity'],
						$result_row['bath'],
						$result_row['status'],
						$currentBookingID,
						$result_row['room_notes']);
	else if ($currentBookingID=="")
	    $theRoom = new Room($result_row['room_no'],
						$result_row['beds'],
						$result_row['capacity'],
						$result_row['bath'],
						"clean",
						"",
						"");
	else $theRoom = new Room($result_row['room_no'],
						$result_row['beds'],
						$result_row['capacity'],
						$result_row['bath'],
						"booked",
						$currentBookingID,
						"");
	return $theRoom;
}

/**
 * Updates a room in the dbRooms table by deleting it and reinserting it.
 * @param $room the room to update
 */
function update_dbRooms($room){
	// Make sure the room is actually a room
	if (!($room instanceof Room)) {
		// Print an erro
		echo ("Invalid argument for update_dbRooms function call.");
		return false;
	}
	
	// Update the table
	if (delete_dbRooms($room->get_room_no())) {
		return insert_dbRooms($room);
	}
	else {
		echo (mysql_error()."unable to update dbRooms table: ".$room->get_room_no());
		return false;
	}
}

/**
 * Deletes a room from the dbRooms table
 * @param $room_no the room number of the room to delete
 */
function delete_dbRooms($room_no){
	// Connect to the databse
	connect();
	// Attempt to delete the entry
	$query = "DELETE FROM dbRooms WHERE room_no=\"".$room_no."\"";
	$result = mysql_query($query);
	mysql_close();
	// Check if it works
	if(!$result){
		// Print an error
		echo (mysql_error()."unable to delete from dbRooms table: ".$room_no);
		return false;
	}
	return true;
}