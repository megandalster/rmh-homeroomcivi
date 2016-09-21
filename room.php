<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

session_start();
session_cache_expire(30);
include_once(dirname(__FILE__)."/database/dbBookings.php");
include_once(dirname(__FILE__)."/database/dbRooms.php");
include_once(dirname(__FILE__)."/database/dbPersons.php");
include_once(dirname(__FILE__)."/database/dbLog.php");
include_once(dirname(__FILE__)."/domain/Room.php");
include_once(dirname(__FILE__)."/domain/Booking.php");
include_once(dirname(__FILE__)."/domain/Person.php");
?>

<?php 
// get the room id and filter it
$roomID = sanitize($_GET['room']);
$date = $_GET['date'];
$day = $_GET['day'];
$bookingID = $_GET['bookingID'];
$roomStatus = $_GET['status'];
?>
<!-- html header stuff -->
<html>
<head>

<title>Room View</title>
<link rel="stylesheet" href="styles.css" type="text/css" />

</head>
<!--  Body portion starts here -->
<body>

<!--  encase everything in a container div -->
<div id="container">
	<!-- The header goes here -->
	<?php include_once("header.php");?>
	<!-- Content div goes here now -->
	<div id="content">
	<?php 
	// Prep work for the room
	$currentRoom = retrieve_dbRooms($roomID,$date,$bookingID);
	// Check if the room is valid and if any data was recently changed
	if($currentRoom instanceof Room){
		// Check if the room has been modified
		if($_POST['submit'] == "Submit"){
			//update the room
			update_room_info($currentRoom,$date);
			echo ("<h3 style=\"text-align:center\">Room has been updated</h3>");
			// get the updated room
			$currentRoom = retrieve_dbRooms($roomID,$date,$bookingID);
			echo ("<script>location.href='roomLog.php?date=".$date."'</script>");
		}
		// Display the room's information
		include_once("roomView.inc");
	}
	?>
		<!-- include the footer at the end -->
		
	</div>
	<?php include_once("footer.inc");?>	
</div>
<!-- useful functions -->
<?php 
// function to sanitize entries
function sanitize($string){
	return trim(str_replace('\\\'','',htmlentities(str_replace('&','and',$string))));
}

// Function that grabs all of the submitted values and updates the room
function update_room_info($currentRoom,$date){
	// Get the info of the user who is making the update
	$user = retrieve_dbPersons($_SESSION['_id']);
	$name = $user->get_first_name()." ".$user->get_last_name();
	
	// Grab all of the variables and sanitize them
	$newBeds = sanitize($_POST['beds']);
	$newCapacity = sanitize($_POST['capacity']);
	$newBath = sanitize($_POST['bath']);
	if($newBath == "Yes"){
		$newBath = "y";
	}else{
		$newBath = "n";
	}
	$newStatus = sanitize($_POST['status']);
	$newRoomNotes = sanitize($_POST['room_notes']);
	$newBooking = sanitize($_POST['assign_booking']);
		
	// Only update the status if you're a volunteer or manager
	// social workers cannot edit rooms
    if($_SESSION['access_level'] != 2){
		// add a log only if the status actually changed
		// then update the status
		if($newStatus != $currentRoom->get_status()){
			$currentRoom->set_status($newStatus);
			// Create the log message
			$message = "<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
			" has changed the status of <a href='room.php?room=".$currentRoom->get_room_no()."'>room ".
			$currentRoom->get_room_no()." to ".$currentRoom->get_status()."</a>";
			add_log_entry($message);
		}
	}
	
	// Update everything else only if you're a manager
	if($_SESSION['access_level'] == 3){
		$currentRoom->set_beds($newBeds);
		$currentRoom->set_capacity($newCapacity);
		$currentRoom->set_bath($newBath);
		$currentRoom->set_room_notes($newRoomNotes);
	}	
		// Checkout the booking if the option was selected (or checkout deceased)
	if($newBooking == "Checkout"){
			    $currentRoom->set_status("dirty");
				//retrieve the booking and check it out
				$newBooking = retrieve_dbBookings($currentRoom->get_booking_id());
				if ($newBooking) {
				    $newBooking->check_out(date($date), false);		//not deceased		
				    // Add a log to show that the family was checked out
				    // Get the info of the primary guest
				    $pGuest = retrieve_dbPersons($newBooking->get_guest_id());
				    if ($pGuest) {
				        $guestName = $pGuest->get_first_name()." ".$pGuest->get_last_name();
				
				        // Create the log message
				        $message = "<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
						" has checked out <a href='viewPerson.php?id=".$pGuest->get_id()."'>".
				        $guestName."</a>";
				        add_log_entry($message);
				    }
				}
	}
	else if($newBooking == "Checkout (Deceased)") { //closing a booking for deceased patient
			    $currentRoom->set_status("dirty");
				//retrieve the booking and check it out
				$newBooking = retrieve_dbBookings($currentRoom->get_booking_id());
				if ($newBooking) {
				    $newBooking->check_out(date("y-m-d"), true);	//deceased		
				   	// Add a log to show that the family was checked out
				   	// Get the info of the primary guest
				   	$pGuest = retrieve_dbPersons($newBooking->get_guest_id());
				   	if ($pGuest) {
				        $guestName = $pGuest->get_first_name()." ".$pGuest->get_last_name();
				        // Create the log message
				        $message = "<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
						" has checked out (deceased) <a href='viewPerson.php?id=".$pGuest->get_id()."'>".
				        $guestName."</a>";
				   	    add_log_entry($message);
				   	}
				}
		}
	else if($newBooking == "Checkin"){  // booking a previously reserved room
				$currentRoom->set_status("booked");
				// retrieve the booking and update it
				$newBooking = retrieve_dbBookings($currentRoom->get_booking_id());
				
				// Add a log to show that the family was checked in
				// Get the info of the primary guest
				$pGuest = retrieve_dbPersons($newBooking->get_guest_id());
				$guestName = $pGuest->get_first_name()." ".$pGuest->get_last_name();
				// Create the log message
				$message = "<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
				" has checked in <a href='viewPerson.php?id=".$pGuest->get_id()."'>".
				$guestName."</a>";
				// quick fix: don't add a log if the reservation was not successful
				if ($newBooking->book_room($currentRoom->get_room_no(),$date)){
					add_log_entry($message);
				}
				else add_log_entry("<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
				" failed to check in <a href='viewPerson.php?id=".$pGuest->get_id()."'>".
				$guestName."</a>");
		}
	else{  // reserving a previously empty room
		    $newBooking = retrieve_dbBookings($newBooking);
			if ($newBooking) {
				$pGuest = retrieve_dbPersons($newBooking->get_guest_id());
				$guestName = $pGuest->get_first_name()." ".$pGuest->get_last_name();
				
				// Create the log message
				$message = "<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
				" has reserved <a href='viewPerson.php?id=".$pGuest->get_id()."'>".
				$guestName."</a>";
				// quick fix: don't add a log if the reservation was not successful
				if ($currentRoom->get_room_no() < 100) // day use, go straight to booking
					if ($newBooking->book_room($currentRoom->get_room_no(),$date)){
						add_log_entry($message);
					}
					else add_log_entry("<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
						" failed to book <a href='viewPerson.php?id=".$pGuest->get_id()."'>".$guestName."</a>");
				else
					if ($newBooking->reserve_room($currentRoom->get_room_no(),$date)){
						add_log_entry($message);
					}
					else add_log_entry("<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
						" failed to reserve <a href='viewPerson.php?id=".$pGuest->get_id()."'>".$guestName."</a>");
			}
		}
}
?>

<!-- End body and html -->
</body>
</html>