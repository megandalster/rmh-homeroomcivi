<?php 
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

// Get the array of rooms
$rooms = $roomLog->get_rooms();
?>

<!-- THIS SECTION DISPLAYS THE 21 ROOMS AND THEIR INFORMATION -->
<?php 
echo ("<table align=\"center\">");

// Use loops to construct each table row and column
for($i = 0; $i < 3; $i++){
	// Three rows
	echo ("<tr>");
	for($j = 0; $j < 7; $j++){
		// Retrieve the current room
		$currentRoomID = substr($rooms[($i*7)+$j],0,3);
		$currentBookingID = substr($rooms[($i*7)+$j],4);
		//echo ("current booking and room ids".$currentRoomID);
		//echo ($currentBookingID);
		$currentRoom = retrieve_dbRooms($currentRoomID);
		$currentBooking = retrieve_dbBookings($currentBookingID);
		
		// This begins a "link" where the link is actually
		// the entire box. It only does this if date is
		// today or later
		if(strcmp($date,date("y-m-d")) >=0 || !$currentBooking || $currentBooking && $currentBooking->get_status()=="active"){
			echo ("<td id=\"nav\" onclick=\"window.location.href='room.php?room=".$currentRoomID."&date=".$date."'\"");
			echo ("style=\"vertical-align:text-top\">");
		}else{
			echo ("<td style=\"vertical-align:text-top\">");
		}

		// The ID of the room and the status
		echo ("<b style=\"float:left\">".$currentRoomID."</b>");
		// Print the status of the room
		$roomStatus = $currentRoom->get_status();
		$statusColor = null;
		// Change the color depending on the status
		if($roomStatus == "clean"){
			$statusColor = "green";
		}else if($roomStatus == "reserved"){
			$statusColor = "orange";
		}else if($roomStatus == "dirty"){
			$statusColor = "brown";
		}else if($roomStatus == "booked"){
			$statusColor = "red";
		}else if($roomStatus == "off-line"){
			$statusColor = "blue";
		}
		if($date == date("y-m-d"))  {
			echo ("<b style=\"float:right; color:".$statusColor."\">");
			echo ($roomStatus."</b>");
			echo ("<br />");
		}
		// Print the beds in the room
		echo("<b>Beds: </b>".$currentRoom->get_beds());
		// Print if there is a private bath
		if($currentRoom->get_bath() == "y"){
			echo (" bath");
		}
		if($date == date("y-m-d") && $currentRoom->get_room_notes() != "")
		    echo "<br /><b>Notes: </b><br>".$currentRoom->get_room_notes();
		// Display info from the booking, such as the guests and loaners
		if (!$currentBooking)
		    echo "<br /><br /><br />";
		else {
			echo ("<br /><b>Guests:</b><br />");
			print_booking_info($currentBooking);
		}
		// End this table item. Finish the link if it was started
		echo ("</td>");
	}
	echo ("</tr>");
}

echo ("</tr></table>");


echo("<br>");

echo ("<b>Day Use Bookings: </b>");

$day_rooms = count($rooms) - 21;

$day_rows = $day_rooms / 7;

$counter = 0;
echo ("<table align=\"center\">");
// Use loops to construct each table row and column
for($i = 0; $i < $day_rows; $i++){
	// i rows
		echo ("<tr>");
		for($j = 0; $j < 7; $j++)
		{
			if($counter < $day_rooms)
			{
				//do stuff
				$currentRoomID = substr($rooms[($i*7)+$j+21],0,3);;
				$currentBookingID = substr($rooms[($i*7)+$j+21],4);
				
				$currentRoom = retrieve_dbRooms($currentRoomID);
				$currentBooking = retrieve_dbBookings($currentBookingID);
				//echo("<script>");
				//echo("alert('$currentBookingID');");
				//echo("</script>");
			
				// This begins a "link" where the link is actually
				// the entire box. It only does this if date is
				// today or later
				if(strcmp($date,date("y-m-d")) >=0 || !$currentBooking || $currentBooking && $currentBooking->get_status()=="active"){
					echo ("<td id=\"nav\" onclick=\"window.location.href='room.php?room=".$currentRoomID."&day=yes"."&date=".$date."'\"");
					echo ("style=\"vertical-align:text-top\">");
				}else{
					echo ("<td style=\"vertical-align:text-top\">");
				}
		
				// The ID of the room and the status
				echo ("<b style=\"float:left\">".$currentRoomID."</b>");
				// Print the status of the room
				$roomStatus = $currentRoom->get_status();
				$statusColor = null;
				// Change the color depending on the status
				if($roomStatus == "reserved"){
					$statusColor = "orange";
				}else if($roomStatus == "booked"){
					$statusColor = "red";
				}
				if($date == date("y-m-d"))  {
					echo ("<b style=\"float:right; color:".$statusColor."\">");
					echo ($roomStatus."</b>");
					echo ("<br />");
				}
				
				if($date == date("y-m-d") && $currentRoom->get_room_notes() != "")
				    echo "<br /><b>Notes: </b><br>".$currentRoom->get_room_notes();
				// Display info from the booking, such as the guests and loaners
				if (!$currentBooking)
				    echo "<br /><br /><br />";
				else {
					echo ("<br /><b>Guests:</b><br />");
					print_booking_info($currentBooking);
				}
				// End this table item. Finish the link if it was started
				echo ("</td>");
					$counter++;
			}
			else
			{
				break;
			}
		}
	
	echo ("</tr>");
}

echo ("</tr></table>");



/**
 * These functions help in printing out information
 * about each individual room or recieving information
 * needed to print
 */


// Function that gets the booking from a room
function get_booking($room){
	// Get the booking id
	$bookingID = $room->get_booking();
	// Retrieve the booking from the database
	$newBooking = retrieve_dbBookings($bookingID);
	// return the new booking
	return $newBooking;
}

// Funtion to display the information found in a booking.
function print_booking_info($booking){

	$guests = $booking->get_occupants();
	$primary = retrieve_dbPersons($booking->get_guest_id());
	echo ($primary->get_first_name()." ".$primary->get_last_name()."<br />");
	// Print each occupant
	foreach($guests as $currentGuest){
		echo ($currentGuest."<br />");
	}

	// Print the patient associated with the booking
	$patient = $booking->getith_patient(0);
	echo ("<b>Patient: </b><br />".$patient);
	for ($i=1; $i<3; $i++)
	    if ($booking->getith_patient($i))
	        echo ", ".$booking->getith_patient($i);echo ("<br />");
}
?>
<!-- END OF THE SECTION THAT PRINTS OUT THE 21 ROOMS -->