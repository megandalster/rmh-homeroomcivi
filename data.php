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
include_once(dirname(__FILE__)."/domain/Room.php");
include_once(dirname(__FILE__)."/domain/Booking.php");
include_once(dirname(__FILE__)."/domain/OccupancyData.php");
?>
<html>
<head>
<title>Room occupancy data</title>
<!--  Choose a style sheet -->
<link rel="stylesheet" href="styles.css" type="text/css" />
<link rel="stylesheet" href="calendar.css" type="text/css" />
<link href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script>
$(function() {
$( "#range_Start_DatePicker" ).datepicker();
$( "#range_End_DatePicker" ).datepicker();
});
</script>
</head>
<!-- Body portion starts here -->
<body>
	<div id="container">
		<!--  the header usually goes here -->
		<?php include_once("header.php");?>
		<div id="content">
			<!-- content goes here -->
			<?php 
			// Get start and end dates for reporting
			$start_date = $_GET['date'];
			$end_date = $_GET['enddate'];
			$roomNo = "";
			// Check if a custom date was submitted
			if($_POST['submit'] == "Submit"){
			    $roomNo = $_POST['roomno'];
			    if ($_POST['range_Start_DatePicker']) {
			        $time = strtotime($_POST['range_Start_DatePicker']);
			        $start_date = date('y-m-d', $time);
			    }
			    if ($_POST['range_End_DatePicker']) {
			        $endTime = strtotime($_POST['range_End_DatePicker']);
			        $end_date = date('y-m-d', $endTime);
			    }
			}		
			
			$od = new OccupancyData($start_date, $end_date, $roomNo);
			
	    //    echo("<p>The data below has been exported as a spreadsheet file.  To download and view it, <br>
	    //    set your browser to rmhportland/volunteers/homeroom/dataexport.csv.");
			show_options($start_date,$end_date);	    
		//	export_data($od, $date, $enddate, $formattedDate, $formattedEndDate, $roomNo);	
			// String of this date, including the weekday and such
			if ($od instanceof OccupancyData){
				include_once("dataView.inc");
			}
			else {
			    echo ("<h3>Occupancy Data for ".$start_date." to ".$end_date." not found</h3><br>");
			}
			?>
			<!--  the footer goes here now -->
			
		</div><?php include_once("footer.inc");?>
	</div>
</body>
</html>

<?php 
// Function that displays date range for statistics
function show_options($start_date,$end_date){
    echo ("<br />"); // new line break
    echo ("<form name=\"chooseDate\" method=\"post\">");
    echo ("<p style=\"text-align:left\">");
    $formattedDate = date("F j, Y",strtotime($start_date));
    $formattedEndDate = date("F j, Y",strtotime($end_date));

    echo ("<p>To view data for a different time period or a certain room number, choose a different
			<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;start date ");
    echo '<input type="text" id="range_Start_DatePicker" name="range_Start_DatePicker" value="'. $formattedDate.'" size="15" />';
    echo ("&nbsp;&nbsp;&nbsp;&nbsp;and/or end date ");
    echo '<input type="text" id="range_End_DatePicker" name="range_End_DatePicker" value="'.$formattedEndDate.'" size="15" />';
    
    $rooms = retrieveall_rooms();
    echo ("&nbsp;&nbsp;&nbsp;&nbsp;and/or room number: <select name=\"roomno\">");
    echo ("<option value=''>--all--</option>");
    foreach ($rooms as $aRoom) {
        echo ("<option value='");
        echo($aRoom->get_room_no());
        if ($aRoom->get_room_no()==$_POST['roomno'])
            echo "' SELECTED>";
        else echo "'>";
        echo $aRoom->get_room_no()."</option>";
    }
    echo ("</select>");
    
    echo ("<br><br> and hit ");
    echo ("<input type=\"submit\" name=\"submit\" value=\"Submit\"/>".".");
    
    echo ("</form>");
}

function export_data ($od, $date, $enddate, $formattedDate, $formattedEndDate, $roomNo) {
	// download the data to the desktop
	$filename = "dataexport.csv";
	$handle = fopen($filename, "w");
    if ($roomNo=="") $trailer = " all rooms.";
    else $trailer = " room ".$roomNo." only.";
	$myArray = array("Occupancy ", "Data for ",$formattedDate." to ", $formattedEndDate, $trailer);
	fputcsv($handle, $myArray);
				
	$fc = $od->get_family_counts();
	$bc = $od->get_booking_counts();
	$gc = $od->get_guest_counts();
	$myArray = array("Room #", "Families", "Bookings", "Nights", "Guests");
	fputcsv($handle, $myArray);
	foreach ($od->get_room_counts() as $room_no=>$count){
		$myArray = array($room_no, $fc[$room_no], $bc[$room_no], $count, $gc[$room_no]);
		fputcsv($handle, $myArray);
	}
	$gc = $od->get_address_guest_counts();
	$myArray = array("State/County", "Bookings", "Guests");
	fputcsv($handle, $myArray);
	foreach ($od->get_address_counts() as $zip=>$count){
		$myArray = array($zip, $count, $gc[$zip]);
		fputcsv($handle, $myArray);
	}
	$gc = $od->get_age_guest_counts();
	$myArray = array("Patient Age", "Bookings", "Guests");
	fputcsv($handle, $myArray);
	foreach ($od->get_age_counts() as $age=>$count){
		$myArray = array($age, $count, $gc[$age]);
		fputcsv($handle, $myArray);
	}
	$gc = $od->get_hospital_guest_counts();
	$myArray = array("Hospital", "Bookings", "Guests");
	fputcsv($handle, $myArray);
	foreach ($od->get_hospital_counts() as $hospital=>$count){
		$myArray = array($hospital, $count, $gc[$hospital]);
		fputcsv($handle, $myArray);
	}
	fclose($handle);
}

?>
