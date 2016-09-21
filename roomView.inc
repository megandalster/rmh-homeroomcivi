<?php 
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
?>

<!-- Begin printing out the data of the room as a form -->
<?php
// Grab the booking object
$booking = retrieve_dbBookings($bookingID);

echo ("<h3>Viewing Status of Room ".$roomID);
if ($date != date('y-m-d'))
	echo " on ". date ("l M j, Y", mktime(0,0,0,substr($date,3,2),substr($date,6,2),substr($date,0,2))) . ".</h3>";
else echo " (today).</h3>";
// echo '<p> (Please change only the information you want to update.)</p>';
if ($booking)
    echo '<p>To view more guest or patient information for this booking, please go <a href="viewBookings.php?id=update&bookingid='.$bookingID.'">here.</a></p>'		
?>
<fieldset>
<form method="POST">

<!--  input fields for everything in the room -->
<?php
if($_SESSION['access_level']>2 && !$day) { 
	echo "
	Beds: <input type='text' name='beds' value='".$currentRoom->get_beds()."'/><br />
	Room Capacity: <input type='text' name='capacity' value='".$currentRoom->get_capacity()."'/><br />
	Bath: <select name='bath'>";
		// fill in the selection for the bath
		if($currentRoom->get_bath() == "y"){
			echo ("<option selected='yes'>Yes</option>");
			echo ("<option>No</option>");
		}else{
			echo ("<option>Yes</option>");
			echo ("<option selected='yes'>No</option>");
		}
	echo "</select><br />";
}
else if(!$day){
	echo "
	Beds: <input type='text' name='beds' value='".$currentRoom->get_beds()."' disabled/><br />
	Room Capacity: <input type='text' name='capacity' value='".$currentRoom->get_capacity()."' disabled/><br />
	Bath: <select name='bath' disabled>";
		// fill in the selection for the bath
		if($currentRoom->get_bath() == "y"){
			echo ("<option selected='yes'>Yes</option>");
		}else{
			echo ("<option selected='yes'>No</option>");
		}
	echo "</select><br />";
}
?>
<?php
	echo ("Change status: <select name=\"status\">"); 
	$statusArray = array("clean", "dirty","off-line");
	foreach($statusArray as $status){
		if($roomStatus == $status)
			echo ("<option selected='yes'>".$status."</option>");
		else 
			echo ("<option>".$status."</option>");
	}
	echo ("</select><br />");
?>

Room Notes: <input type="text" size="50" name="room_notes" value="<?php echo($currentRoom->get_room_notes());?>"/><br />

<?php 

// Take care of a booking. The display changes depending on whether
// or not we have a booking

// If successful, display the guests and add options to add or remove them.
if($booking)
{
	// Print booking information
	print_booking_info($booking);
	// Add an option to checkout the family
	echo ("<br />");
	if ($_SESSION['access_level']!=2 && substr($booking->get_status(),0,6)!="closed") {
	    echo ("Checkout/Checkin Family?: <select name='assign_booking'>");
	    echo ("<option selected='yes'>No</option>");
	    echo ("<option>Checkout</option>");
	    echo ("<option>Checkout (Deceased)</option>");
	    echo ("<option>Checkin</option>");
	    echo ("</select><br />");
	    echo ("<br />");
	}
}
else if ($_SESSION['access_level']!=2 && $roomStatus=="clean") 
{
	echo "</fieldset><br>";
	// add options to assign a booking
	echo ("Reserve this room: <select name='assign_booking'>");
	// First option is to leave the room alone
	echo ("<option selected='yes'>Leave Room Unassigned</option>");
	// Grab a list of all pending bookings
	if($day == 'yes')
	{
		$pendingBookings = retrieve_pendingDayUse_dbBookings($date);
		if($pendingBookings)
		{
		// Make each booking id a menu item
		foreach($pendingBookings as $booking)
		{
			echo ("<option value='" . $booking->get_id() . "'>");
			$person = retrieve_dbPersons(substr($booking->get_id(), 8));
			if ($person)
				echo ($person->get_first_name() . " " . $person->get_last_name() . " (" .date_string(substr($booking->get_id(),0,8)).")");
			else echo($booking->get_id());
			echo ("</option>");
		}
		}
	}
	else 
	{
		$pendingBookings = retrieve_all_pending_dbBookings($date);
		if($pendingBookings)
		{
		// Make each booking id a menu item
		foreach($pendingBookings as $booking){
		    if ($booking->get_status()!="pending") continue;
			if(!$booking->day_use() == 'yes')
			{
				echo ("<option value='" . $booking->get_id() . "'>");
				$person = retrieve_dbPersons(substr($booking->get_id(), 8));
				if ($person)
					echo ($person->get_first_name() . " " . $person->get_last_name() . " (" .date_string(substr($booking->get_id(),0,8)).")");
				else echo($booking->get_id());
				echo ("</option>");
			}
		}
	}
	
	}
	echo("</select><br />");
}
else echo "</fieldset><br>";
if ($_SESSION['access_level']!=2) {
    echo "Hit Submit to save these changes: "; 
    echo '<input type="submit" value = "Submit" name = "submit"/>';
}
?>
</form>
</fieldset>
<!-- Useful funtions -->
<?php 

// Function to display information about the booking
function print_booking_info($booking){
	echo "<br>";
	echo ("<tr><b>Guests Present:</b></tr>");
	$guests = $booking->get_occupants();
	// Print each occupant
	foreach($guests as $currentGuest)
	  if (strpos($currentGuest,"Present")>0) {
	    $i = strpos($currentGuest,":");
	    if (!$i) $i = strlen($currentGuest);
		echo ("<br>".substr($currentGuest,0,$i));
	  }
	// Print the patient(s) associated with the booking
	echo "<br><br><b>Patient: </b><br>". $booking->getith_patient(0);
	for ($i=1; $i<3; $i++)
	    if ($booking->getith_patient($i))
	        echo ", ".$booking->getith_patient($i);
	echo "<br>";
}
?>
