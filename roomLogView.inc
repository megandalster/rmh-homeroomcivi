
<?php 
/*
 * Copyright 2020 by Alex Lucyk, Jesus Naero, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
$room_data = array ("105"=>"2yQ","106"=>"2yQ","108"=>"2yQ", "125"=>"2n2T","126"=>"4yQ/2T","151"=>"2y2T","152"=>"2y2T",
    "202"=>"3yQ","203"=>"3yQ","204"=>"2yQ","205"=>"2yQ", "206"=>"2yQ", "214"=>"2nQ","215"=>"2n2T",
    "218"=>"2yQ", "223"=>"2nQ/T","224"=>"3n3T","231"=>"3y3T","232"=>"2n2T","233"=>"3n3T","243"=>"4yQ/3T",
    "244"=>"2nQ", "245"=>"2nQ","250"=>"2yQ, horn,","251"=>"2yQ, strobe,","252"=>"2yQ, horn,","253"=>"2yQ, horn,","254"=>"2y2T, horn,",
    "255"=>"2y2T", "301"=>"2yQ","302"=>"2yQ","304"=>"2yT","305"=>"2yQ","306"=>"2y2T"
);
// Get the array of rooms and current bookings
$rooms = $roomLog->get_rooms();
// echo "rooms = "; var_dump($rooms);
$pendingdayusecount = count(retrieve_pendingDayUse_dbBookings ($date));
?>

<!-- THIS SECTION DISPLAYS THE 34 ROOMS AND THEIR INFORMATION -->
<?php 
echo ("<table align=\"center\">");

// Use loops to construct each table row and column
for($i = 0; $i < 5; $i++){
    // Three rows
    echo ("<tr>");
    for($j = 0; $j < 7; $j++){
		// Retrieve the current room
		$currentRoomID = substr($rooms[($i*7)+$j],0,3);
		$currentBookingID = substr($rooms[($i*7)+$j],4);
		if ($currentRoomID=="")
		    continue;
		//echo ("current room and booking ids".$currentRoomID);
		//echo ($currentBookingID);
		$currentRoom = retrieve_dbRooms($currentRoomID,$date,$currentBookingID);
		if (!$currentRoom){ // if the room was dropped from the db, rebuild and add back in
		    $beds = substr($room_data[$currentRoomID],2);
		    $capacity = substr($room_data[$currentRoomID],0,1);
		    $bath = substr($room_data[$currentRoomID],1,1);
		    insert_dbRooms(new Room($currentRoomID, $beds, $capacity, $bath, "clean", $currentBookingID, ""));
		}
		$currentBooking = retrieve_dbBookings($currentBookingID);
		if ($date==date('y-m-d'))
            $roomStatus = $currentRoom->get_status();
        else if (!$currentBooking)
            $roomStatus = "clean";
        else $roomStatus = "booked";
        $statusmsg = $roomStatus;
        switch ($roomStatus) {
            case "clean": $statusColor = "green"; $statusbg="PaleGreen"; break;
            case "reserved": $statusColor = "orange"; $statusbg="White"; break;
            case "dirty": $statusColor = "red"; $statusmsg = "to be cleaned"; $statusbg="Pink"; break;
            case "booked": $statusColor = "brown"; $statusbg="White"; break;
            case "off-line": $statusColor = "blue"; $statusbg="Gainsboro"; break;
            default: $statusColor = null; $statusbg="White"; 
        }
        
		// This begins a "link" where the link is actually
		// the entire box. 
			echo ("<td id=\"nav\" onclick=\"window.location.href='room.php?room=".$currentRoomID."&date=".$date."&bookingID=".$currentBookingID."&status=".$roomStatus."'\"");
			echo ("style='vertical-align:text-top; background-color:".$statusbg."'>");

		// The ID of the room and the status
		echo ("<b style=\"float:left\">".$currentRoomID."</b>");
		// Print the status of the room
		
		if($date == date("y-m-d"))  
		    if (!$currentBooking){
			    echo ("<b style=\"float:right; color:".$statusColor."\">");
			    echo ($statusmsg."</b>");
		    }
		    else if ($currentBooking->get_status()=="reserved")
		        echo ("<b style=\"float:right; color:orange\">reserved</b>");
		// Print the beds in the room
		echo("<br><b>Beds: </b>".$currentRoom->get_beds());
		// Print if there is a private bath
		if($currentRoom->get_bath() == "y"){
			echo (" bath");
		}
		if($date == date("y-m-d") && $currentRoom->get_room_notes() != "")
		    echo "<br /><b>Notes: </b><br><span style='font-weight:bold;color:FF0000'>".$currentRoom->get_room_notes().'</span>';
		// Display info from the booking, such as the guests and loaners
		if (!$currentBooking)
		    echo "<br /><br /><br />";
		else 
			print_booking_info($currentBooking);
		// End this table item. Finish the link if it was started
		echo ("</td>");
	}
	echo ("</tr>");
}

echo ("</tr></table>");


echo("<p>");

$day_rooms = count($rooms) - 34 + $pendingdayusecount;
if ($day_rooms > 0) {
    echo ("<b>Day Use Bookings: </b><br>");
    $counter = 0;
    echo ("<br><table align=\"center\">");
    // Use loops to construct each table row and column
    echo ("<tr>");
    for($j = 0; $j < $day_rooms; $j++)
		{
			$currentRoomID = substr($rooms[$counter+21],0,3);	
			$currentBookingID = substr($rooms[$counter+21],4);
			if ($currentRoomID!="" && $currentRoomID==$j) {
				$currentRoom = retrieve_dbRooms($currentRoomID,$date,$currentBookingID);
				$currentBooking = retrieve_dbBookings($currentBookingID);
			}
			else {
				$currentRoomID = "00".$j;
				insert_dbRooms(new Room($currentRoomID, null, null, null, "clean", null, ""));  //make sure there's a room there
				$currentBooking=null;
			}
			if ($currentBooking) {
				if ($date==date('y-m-d'))
            		$roomStatus = $currentRoom->get_status();
        		else if (!$currentBooking)
            		$roomStatus = "clean";
        		else $roomStatus = "booked";
				
        		// This begins a "link" where the link is actually
				// the entire box. 
				echo ("<td id=\"nav\" onclick=\"window.location.href='room.php?room=".$currentRoomID."&day=yes"."&date=".$date."&bookingID=".$currentBookingID."&status=".$roomStatus."'\"");
				echo ("style=\"vertical-align:text-top\">");
		
				// The ID of the room and the status
				echo ("<b style=\"float:left\">".$currentRoomID."</b>");
				if($date == date("y-m-d") && $currentRoom->get_room_notes() != "")
				    echo "<br /><b>Notes: </b><br>".$currentRoom->get_room_notes();
				// Display info from the booking, such as the guests and loaners
				echo ("<br /><b>Guests:</b><br />");
				print_booking_info($currentBooking);
				echo ("</td>");
				$counter++;
			}
			else {
			    echo ("<td id=\"nav\" onclick=\"window.location.href='room.php?room=".$currentRoomID."&day=yes"."&date=".$date."&bookingID=&status=".$roomStatus."'\"");
				echo ("style=\"vertical-align:text-top\">");
				
				echo ("<b style=\"float:left\">".$currentRoomID."</b>");
				echo "<br /><br /><br />";
				echo ("</td>");
			}	
		}	
    echo ("</tr>");
    echo ("</tr></table>");
}


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
	echo ("<br><b>Guests:</b>");
	$guests = $booking->get_occupants();
	// Print each occupant
	foreach($guests as $currentGuest) {
	  if (strpos($currentGuest,"Present")>0) {
	  	$j = strpos($currentGuest,":");
	    if (!$j) $j = strlen($currentGuest);
		echo ("<br>".substr($currentGuest,0,$j));
	  }
	}
	// Print the patient associated with the booking
	echo ("<br><b>Patient: </b><br>".$booking->getith_patient(0));
	for ($i=1; $i<3; $i++)
	    if ($booking->getith_patient($i))
	        echo ", ".$booking->getith_patient($i);
}
?>
