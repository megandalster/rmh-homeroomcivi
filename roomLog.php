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
include_once(dirname(__FILE__)."/database/dbRoomLogs.php");
include_once(dirname(__FILE__)."/database/dbBookings.php");
include_once(dirname(__FILE__)."/database/dbRooms.php");
include_once(dirname(__FILE__)."/database/dbPersons.php");
include_once(dirname(__FILE__)."/domain/RoomLog.php");
include_once(dirname(__FILE__)."/domain/Room.php");
include_once(dirname(__FILE__)."/domain/Booking.php");
include_once(dirname(__FILE__)."/domain/Person.php");
?>

<html>
<head>
<title>Room Log</title>
<!--  Choose a style sheet -->
<link rel="stylesheet" href="styles.css" type="text/css" />
<link rel="stylesheet" href="calendar.css" type="text/css" />
</head>
<!-- Body portion starts here -->
<body>
<div id="container"><!--  the header usually goes here --> <?php include_once("header.php");?>
<div id="content"><!-- content goes here, like the roomlog --> <!-- First create a room log from the date -->
<?php
// Get the date of the room log
// Filter the date for any nasty characters that will break SQL or html
$date = trim(str_replace('\\\'','',htmlentities(str_replace('&','and',$_GET['date']))));
if ($date=="today") $date = date('y-m-d');
// Check if a custom date was submitted
if($_POST['submit'] == "Submit"){
	// make sure each entry was submitted
	$dateDay = $_POST['day'];
	$dateMonth = $_POST['month'];
	$dateYear = substr($_POST['year'], 2, 2);
	if($dateDay && $dateMonth && $dateYear){
		// construct a date string
		$date = $dateYear."-".$dateMonth."-".$dateDay;
		//sanitize it again just in case
		$date = trim(str_replace('\\\'','',htmlentities(str_replace('&','and',$date))));
	}
}
echo '<form name="chooseBooking" action="viewBookings.php" target="_blank">
		<p style="text-align: center"><b>&nbsp;&nbsp;&nbsp;&nbsp;Pending
		Bookings: </b> <select name="bookingid">';

	// Grab a list of all pending bookings
	$pendingBookings = retrieve_all_pending_dbBookings(date("y-m-d"));
	if($pendingBookings){
		// Make each booking id a menu item
		foreach($pendingBookings as $booking){
		    if ($booking->get_status()!="pending") continue;
			echo ("<option value='" . $booking->get_id() . "'>");
			$person = retrieve_dbPersons(substr($booking->get_id(), 8));
			if ($person) {
				echo ($person->get_first_name() . " " . $person->get_last_name() . " (" .date_string(substr($booking->get_id(),0,8)).")");
			}
			else echo($booking->get_id());
			echo ("</option>");
		}
	    // Then add a button
	    echo ("<input type=\"submit\" value=\"View Booking\"/>");
	}
echo '</select></form>';

if($date==date('y-m-d')){
	// Today's date, so pull up today's roomLog.
	$roomLogID = date("y-m-d");
	$roomLog = new RoomLog($roomLogID);	
}
else if ($date < date("y-m-d")){
	// Search for the room log in the database
	// old $roomLog = retrieve_dbRoomLog($date);
	$roomLog = build_room_log($date);
	$roomLogID = $date;
}
else{
	// future date, create a new room log like today's
	$roomLogID = $date;
	$roomLog = new RoomLog($roomLogID);
}
?>

<!-- Display the room log --> <?php 
    $formattedDate = date("l F j, Y",strtotime($date));
//    if(!$roomLog instanceof RoomLog) {
    	$roomLog = build_room_log($date);
    	update_dbRoomLog($roomLog);
//    }
	echo ("<h3 style=\"text-align:center\">");
	echo ("Room Log for ".$formattedDate."</h3>");

	display_navigation($date);
	// display the 21 rooms
	include_once("roomLogView.inc");
?>
<!--  the footer goes here now --></div>
<?php include_once("footer.inc");?></div>
</body>
</html>

<!-- Useful php functions -->

<?php

// Function that displays room log naviations where
// $now is the current date of the room log
function display_navigation($now){
	// Display a form that let's you choose another date
	echo ("<form name=\"chooseDate\" method=\"post\">");
	echo ("<p style=\"text-align:center\">");
	echo ("(View a different room log:  ");
	date_select_display($now,"now");

	echo ("<input type=\"submit\" name=\"submit\" value=\"Submit\"/>)");
	echo ("</form>");

	// Now add a day-by-day navigation links
	echo ("<table align=\"center\" id=\"nav\">");
	// First make a navigation row with yesterday/tomorrow links
	// Construct the dates first

	// make today a php date
	$today = strtotime($now);
	// make yesterday and tomorrow dates based on today
	$yesterday = mktime(0,0,0,date("m",$today),date("d",$today)-1,date("y",$today));
	$tomorrow = mktime(0,0,0,date("m",$today),date("d",$today)+1,date("y",$today));

	// Make the yesterday link
	echo ("<tr><td align=\"left\">");
	echo ("<a href=\"roomLog.php?date=".date("y-m-d",$yesterday)."\">");
	echo ("<< Previous day's Room Log</a></td>");

	// Make the tomorrow link
	echo ("<td align=\"right\">");
	echo ("<a href=\"roomLog.php?date=".date("y-m-d",$tomorrow)."\">");
	echo ("Next day's Room Log >></a></td></tr>");
	echo ("</table><br>");
}

?>