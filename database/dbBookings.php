<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/**
 * Functions to create, retrieve, update, and delete information from the
 * dbBookings table in the database.  This table is used with the Booking class.  
 * @version February 25, 2011
 * @author Allen and Alex
 */

include_once(dirname(__FILE__).'/../domain/Booking.php');
include_once(dirname(__FILE__).'/dbinfo.php');

/**
 * Create the dbBookings table with the following fields:
 * id:  	 primary key the form "date_in"."guest_id"
 * date_submitted: date the referral was submitted, in the form "yy-mm-dd"
 * date_in:  check-in date in the form "yy-mm-dd"
 * guest_id: id of the primary guest e.g., "John2077291234"
 * status:   current status: "pending", "reserved", "active", "closed", or "closed-deceased"
 * room_no:  id of the room; null if status == "pending", "day" if day use only
 * auto:  automobile make:model:color:state
 * patient:  name(s) of the patient(s) for whom this booking is made
 * occupants:  array of people staying in the room.  
 *		Each entry has the form $name:$relationship_to_patient
 *		e.g., array("John:father", "Jean:mother", "Teeny:sibling")
 * linked_room: (optional) id of a room where other family members are staying
 * date_out:   check-out date "yy-mm-dd" ; null if status == "active"
 * referred_by: id of the person (eg, social worker) requesting this booking
 * hospital:   name of the hospital where the patient is staying
 * department: (optional) department where the treatment occurs
 * health_questions: health_questions for the client staying at the house 
 * mgr_notes:  (optional) notes from the manager/social worker
 */
function create_dbBookings() {
    connect();
    mysql_query("DROP TABLE IF EXISTS dbBookings");
    $result=mysql_query("CREATE TABLE dbBookings (id TEXT NOT NULL, 
        date_submitted TEXT, guest_id TEXT, status TEXT, date_in TEXT, room_no TEXT, auto TEXT,
		patient TEXT, occupants TEXT, linked_room TEXT, date_out TEXT, 
        referred_by TEXT, hospital TEXT, department TEXT, health_questions TEXT, 
        payment_arrangement TEXT, overnight_use TEXT, day_use TEXT, day_use_date TEXT, mgr_notes TEXT, flag TEXT)");
    mysql_close();	
    if(!$result) {
		echo mysql_error() . ">>>Error creating dbBookings table. <br>";
	    return false;
    }
    return true;
}

/**
 * Inserts a new booking into the dbBookings table
 * @param $booking = the booking to insert
 */
function insert_dbBookings ($booking) {
    if (! $booking instanceof Booking) {
		echo ("Invalid argument for insert_dbBookings function call");
		return false;
	}
    connect();
    $query = "SELECT * FROM dbBookings WHERE id ='".$booking->get_id()."'";
    $result = mysql_query ($query);
    if (mysql_num_rows($result)!=0) {
        delete_dbBookings ($booking->get_id());
        connect();
    }
    $query="INSERT INTO dbBookings VALUES ('".
				$booking->get_id()."','".
				$booking->get_date_submitted()."','".
				$booking->get_guest_id()."','".
				$booking->get_status()."','".
				$booking->get_date_in()."','".
				$booking->get_room_no()."','".
	            $booking->get_auto()."','".
	            implode(',',$booking->get_patient())."','".
				implode(',',$booking->get_occupants())."','".
				$booking->get_linked_room()."','".
				$booking->get_date_out()."','".
				$booking->get_referred_by()."','".
				$booking->get_hospital()."','".
				$booking->get_department()."','".
				$booking->get_health_questions()."','".
				$booking->get_payment_arrangement()."','".
				$booking->overnight_use()."','".
				$booking->day_use()."','".
				$booking->get_day_use_date()."','".
				$booking->get_mgr_notes()."','".
				$booking->get_flag()."')";
	$result=mysql_query($query);
    if (!$result) {
		echo (mysql_error()."unable to insert into dbBookings: ".$booking->get_id()."\n");
		mysql_close();
    return false;
    }
    mysql_close();
    return true;
 }

/**
 * Retrieves a Booking from the dbBookings table
 * @param $id booking id
 * @return the Booking corresponding to id, or false if not in the table.
 */
function retrieve_dbBookings ($id) {
	connect();
    $query = "SELECT * FROM dbBookings WHERE id =\"".$id."\"";
    $result = mysql_query ($query);
    if (mysql_num_rows($result)!=1) {
	    mysql_close();
		return false;
	}
	$result_row = mysql_fetch_assoc($result);
	$theBooking = build_booking($result_row);
	mysql_close();
	return $theBooking;
}
/* 
 * auxiliary function to build a Booking from a row in the dbBookings table
 */
function build_booking($result_row) {
    $theBooking = new Booking($result_row['date_submitted'], $result_row['date_in'],
        $result_row['guest_id'], $result_row['status'], $result_row['room_no'], 
	    explode(',',$result_row['patient']), explode(',',$result_row['occupants']),
	    $result_row['auto'], $result_row['linked_room'], $result_row['date_out'], $result_row['referred_by'], 
	    $result_row['hospital'], $result_row['department'], $result_row['health_questions'], $result_row['payment_arrangement'], 
	    $result_row['overnight_use'], $result_row['day_use'], $result_row['day_use_date'], $result_row['mgr_notes'], $result_row['flag']);
   
	return $theBooking;
}
/**
 * Retrieves an array of room_no:booking_id pairs for all bookings that were "active" on a certain $date.
 * An "active" booking on a date is 
 *      "active" and date_in <= $date and date_out == null
 * assuming that $date is either today or in the future.  If $date is in the past, the roomlog will have all
 * the active booking information for that $date, so this function will not be needed.
 * @param $date 
 * @return array of active room_no:booking_id pairs on $date ordered by room_no
 */
function retrieve_active_dbBookings ($date) {
	connect();
    $query = "SELECT * FROM dbBookings WHERE (status = 'active' AND date_in <= '".$date."') OR status = 'reserved'" . 
             " ORDER BY room_no";
    $result = mysql_query ($query);
    $theBookings = array();
	while ($result_row = mysql_fetch_assoc($result)) {
	    $theBooking = build_booking($result_row);
	    $theBookings[$theBooking->get_room_no()] = $theBooking->get_id();
	}
	mysql_close();
	return $theBookings;
}

function retrieve_active_day_use_dbBookings ($date)
{
	connect();
    $query = "SELECT * FROM dbBookings WHERE ((status = 'active' AND date_in <= '".$date."') OR status = 'reserved') AND day_use = 'yes'" . 
             " ORDER BY room_no";
    $result = mysql_query ($query);
    $theBookings = array();
	while ($result_row = mysql_fetch_assoc($result)) {
	    $theBooking = build_booking($result_row);
	    $theBookings[$theBooking->get_room_no()] = $theBooking->get_id();
	}
	mysql_close();
	return $theBookings;
}

/**
 * Retrieves an array of room_no:booking_id pairs for all bookings that were "active" on a past $date.
 * An active booking on a past date is 
 *      "closed" and date_in <= $date and date_out > $date, or
 *      "active" and date_in <= $date
 * In either case, these bookings all have room numbers.  There should not be more than 21 of these.
 * @param $date 
 * @return array of active room_no:booking_id pairs on $date ordered by room_no
 */
function retrieve_past_active_dbBookings ($date) {
	connect();
	$query = "SELECT * FROM dbBookings WHERE (status = 'active' AND date_in <= '".$date. "')" .
    		 			" OR (status = 'closed' AND date_in <= '".$date. "' AND date_out > '" . $date . "')" . 
             			" ORDER BY room_no";
    $result = mysql_query ($query);
    $theBookings = array();
	while ($result_row = mysql_fetch_assoc($result)) {
	    $theBooking = build_booking($result_row);
	    $theBookings[$theBooking->get_room_no()] = $theBooking->get_id();
	}
	mysql_close();
	return $theBookings;
}

function retrieve_past_active_day_use_dbBookings ($date) {
	connect();
	$query = "SELECT * FROM dbBookings WHERE (status = 'active' AND date_in <= '".$date. "' AND day_use = 'yes')" .
    		 			" OR (status = 'closed' AND date_in <= '".$date. "' AND date_out > '" . $date . "' AND day_use = 'yes')" . 
             			" ORDER BY room_no";
    $result = mysql_query ($query);
    $theBookings = array();
	while ($result_row = mysql_fetch_assoc($result)) {
	    $theBooking = build_booking($result_row);
	    $theBookings[$theBooking->get_room_no()] = $theBooking->get_id();
	}
	mysql_close();
	return $theBookings;
}

/**
 * Retrieves an array of all Bookings that are "pending" on a certain $date.
 * A booking is pending on a certain date if 
 *      status = "pending" and date_in <= $date
 * @param $date 
 * @return array of pending bookings on $date
 */
function retrieve_pending_dbBookings ($date) {
	connect();
    $query = "SELECT * FROM dbBookings WHERE status = 'pending' AND date_in <= '".$date."' ORDER BY date_in";
    $result = mysql_query ($query);
    $theBookings = array();
	while ($result_row = mysql_fetch_assoc($result)) {
	    $theBooking = build_booking($result_row);
	    $theBookings[] = $theBooking;
	}
	mysql_close();
	return $theBookings;
}

function retrieve_pendingDayUse_dbBookings ($date) {
	connect();
	$query = "SELECT * FROM dbBookings WHERE status = 'pending' AND day_use = 'yes' AND day_use_date = '".$date."' ";
	$result = mysql_query($query);
	$theBookings = array();
	while ($result_row = mysql_fetch_assoc($result)) {
	    $theBooking = build_booking($result_row);
	    $theBookings[] = $theBooking;
	}
	mysql_close();
	return $theBookings;
}

function retrieve_all_pending_dbBookings () {
	connect();
    $query = "SELECT * FROM dbBookings WHERE status = 'pending' ORDER BY date_in";
    $result = mysql_query ($query);
    $theBookings = array();
	while ($result_row = mysql_fetch_assoc($result)) {
	    $theBooking = build_booking($result_row);
	    $theBookings[] = $theBooking;
	}
	mysql_close();
	return $theBookings;
}
//retrieve all bookings that were closed between $date and $enddate, inclusive
function retrieve_all_closed_dbBookings ($date, $enddate) {
	connect();
    $query = "SELECT * FROM dbBookings WHERE status LIKE '%closed%' AND date_out >= '"
             .$date."' AND date_out <= '".$enddate."' ORDER BY date_in";
    $result = mysql_query ($query);
    $theBookings = array();
	while ($result_row = mysql_fetch_assoc($result)) {
	    $theBooking = build_booking($result_row);
	    $theBookings[] = $theBooking;
	}
	mysql_close();
	return $theBookings;
}

/**
 * Updates a Booking in the dbBookings table by deleting it and re-inserting it
 * @param $booking the Booking to update
 */
function update_dbBookings ($booking) {
	if (! $booking instanceof Booking) {
		echo ("Invalid argument for update_dbBookings function call");
		return false;
	}
	if (delete_dbBookings($booking->get_id()))
	   return insert_dbBookings($booking);
	else {
	   echo (mysql_error()."unable to update dbBookings table: ".$booking->get_id());
	   return false;
	}
}

/**
 * Deletes a booking from the dbBookings table
 * @param $booking the id of the booking to delete
 */
function delete_dbBookings($id) {
	connect();
    $query="DELETE FROM dbBookings WHERE id=\"".$id."\"";
	$result=mysql_query($query);
	mysql_close();
	if (!$result) {
		echo (mysql_error()."unable to delete from dbBookings: ".$id);
		return false;
	}
    return true;
}
/*
 * Utility function to enerate a date display string from a given date in the form "yy-mm-dd"  
 */ 
function date_string($date) {
	$d = mktime(0,0,0,substr($date,3,2),substr($date,6,2),substr($date,0,2));
	return date("F j, Y",$d);
}
function date_select_display($now,$when) {
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	echo ("Month <select name=\"month\">");
	echo ("<option value=''></option>");
	for($i = 1; $i<=12; $i++){
		echo ("<option value=\"");
		if($i < 10){
			echo ("0".$i);
		}else{
			echo($i);
		}
		if ($when=="now" && $i==substr($now,3,2))
		    echo "\" selected>";
		else echo "\">";
		echo $months[$i-1]."</option>";
	}
	echo ("</select>");
	
	echo (" Day <select name=\"day\">");
	echo ("<option value=''></option>");
	for($i = 1; $i<=31; $i++){
		echo ("<option value=\"");
	    if($i < 10){
			echo ("0".$i);
		}else{
			echo($i);
		}
		if ($when=="now" && $i==substr($now,6,2))
		    echo "\" selected>";
		else echo "\">";
		echo $i."</option>";
	}
	echo ("</select>");
	
	echo (" Year <input type=\"text\" size=\"3\" maxLength=\"4\" name=\"year\" ");
	if ($when=="now")
	    echo 'value="20'.substr($now,0,2).'">';
	else echo '>';  
}

?>
