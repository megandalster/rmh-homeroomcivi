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
			// Filter the date for any nasty characters that will break SQL or html
			//$enddate = trim(str_replace('\\\'','',htmlentities(str_replace('&','and',$_GET['date']))));
			// Check if a custom date was submitted
			if($_POST['submit'] == "Submit"){
			    $roomNo = $_POST['roomno'];
			    $endDay = $_POST['endday'];
				$endMonth = $_POST['endmonth'];
				$endYear = substr($_POST['endyear'],2,2);
				
				if($endDay && $endMonth && $endYear){
					// construct a date string
					$enddate = $endYear."-".$endMonth."-".$endDay;
					//sanitize it again just in case
					$enddate = trim(str_replace('\\\'','',htmlentities(str_replace('&','and',$enddate))));
				}
				else $enddate = $_GET['enddate'];
				$dateDay = $_POST['day'];
				$dateMonth = $_POST['month'];
				$dateYear = substr($_POST['year'],2,2);
				
				if($dateDay && $dateMonth && $dateYear){
					// construct a date string
					$date = $dateYear."-".$dateMonth."-".$dateDay;
					//sanitize it again just in case
					$date = trim(str_replace('\\\'','',htmlentities(str_replace('&','and',$date))));
				}
				else $date = $_GET['date'];   
			}
			else{
				// no date submitted, so set $date and $enddate to today
				$date = $_GET['date'];
				$enddate = $_GET['enddate'];
				$roomNo = "";
			}		
			$od = new OccupancyData($date, $enddate, $roomNo);
			$formattedDate = date("F j, Y",strtotime($date));
			$formattedEndDate = date("F j, Y",strtotime($enddate));
	    //    echo("<p>The data below has been exported as a spreadsheet file.  To download and view it, <br>
	    //    set your browser to rmhportland/volunteers/homeroom/dataexport.csv.");
			show_options();	    
		//	export_data($od, $date, $enddate, $formattedDate, $formattedEndDate, $roomNo);	
			// String of this date, including the weekday and such
			if ($od instanceof OccupancyData){
				include_once("dataView.inc");
			}
			else
				echo ("<h3>Occupancy Data for ".$formattedDate." to ".$formattedEndDate." not found</h3><br>");
			?>
			<!--  the footer goes here now -->
			
		</div><?php include_once("footer.inc");?>
	</div>
</body>
</html>

<?php 
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
// Function that displays date range for statistics
function show_options(){
	echo ("<br />"); // new line break
	echo ("<form name=\"chooseDate\" method=\"post\">");
	echo ("<p style=\"text-align:left\">");
	echo ("To view data for a different time period or a certain room number, choose a different
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;start date ");
	echo ("Month: <select name=\"month\">");
      $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	  echo("<option> </option>");
      for ($i = 1 ; $i <= 9 ; $i ++){
          if('0'.$i == substr($patient_DOB,3,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$months[$i-1]."</option>");
          else
             echo ("<option value='0".$i."'>".$months[$i-1]."</option>");
      }
      for ($i = 10 ; $i <= 12 ; $i ++){
          if($i == substr($patient_DOB,3,2))
             echo ("<option value=".$i." selected = 'yes' >".$months[$i-1]."</option>");
          else
             echo ("<option value=".$i.">".$months[$i-1]."</option>");
      }
    echo("</select>");
	
	echo (" Day: <select name=\"day\">");
	echo ("<option value=''></option>");
	for($i = 1; $i<=31; $i++){
		echo ("<option value=\"");
		if($i < 10){
			echo ("0".$i."\">".$i."</option>");
		}else{
			echo($i."\">".$i."</option>");
		}
	}
	echo ("</select>");
	echo (" Year: <input type=\"text\" size=\"6\" maxLength=\"4\" name=\"year\"/>");
	
	echo ("<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;and/or end date ");
	echo ("Month: <select name=\"endmonth\">");
	echo ("<option value=''></option>");
      $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	  echo("<option> </option>");
      for ($i = 1 ; $i <= 9 ; $i ++){
          if('0'.$i == substr($patient_DOB,3,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$months[$i-1]."</option>");
          else
             echo ("<option value='0".$i."'>".$months[$i-1]."</option>");
      }
      for ($i = 10 ; $i <= 12 ; $i ++){
          if($i == substr($patient_DOB,3,2))
             echo ("<option value=".$i." selected = 'yes' >".$months[$i-1]."</option>");
          else
             echo ("<option value=".$i.">".$months[$i-1]."</option>");
      }
    echo("</select>");
	
	echo (" Day: <select name=\"endday\">");
	echo ("<option value=''></option>");
	for($i = 1; $i<=31; $i++){
		echo ("<option value=\"");
		if($i < 10){
			echo ("0".$i."\">".$i."</option>");
		}else{
			echo($i."\">".$i."</option>");
		}
	}
	echo ("</select>");
	echo (" Year: <input type=\"text\" size=\"6\" maxLength=\"4\" name=\"endyear\"/>");
	
	$rooms = retrieveall_rooms();
	echo ("<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;and/or room number: <select name=\"roomno\">");	
	echo ("<option value=''></option>");
	foreach ($rooms as $aRoom) {
		echo ("<option value=\"");
		echo($aRoom->get_room_no()."\">".$aRoom->get_room_no()."</option>");
	}
	echo ("</select>");
	
	echo ("<br> and hit ");
	echo ("<input type=\"submit\" name=\"submit\" value=\"Submit\"/>".".");
	
	echo ("</form>");
}
?>
