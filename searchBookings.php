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
include_once("database/dbPersons.php");
?>

<html>
<head>
	<title>Search for a Referral/Booking</title>
	<link rel="stylesheet" href="styles.css" type="text/css" />
	<link rel="stylesheet" href="lib/jquery-ui.css" />
</head>

<body>

<div id="container">
	<?php include_once("header.php");?>
	<div id="content">
	<!-- All the searching stuff goes here -->
	<?php 
	// Display some info
	echo('</p><p>You may search for recent bookings using the following options.<br>'.
		'<span style="font-size:x-small">  (A search for "an" would return D'.
		'<strong>an</strong>, J<strong>an</strong>e, <strong>An</strong>'.
		'n, and Sus<strong>an</strong></span>.)</p>');
	include("searchBookings.inc");
	// Check if a search was made
	if($_POST['submit'] == "Search"){
		// Grab each search string from the form and
		// sanitize it
		$primaryFirstName = sanitize($_POST['p_first_name']);
		$primaryLastName = sanitize($_POST['p_last_name']);
		$roomNumber = sanitize($_POST['room_no']);
		$month = sanitize($_POST['month']);
		$day = sanitize($_POST['day']);
		$year = sanitize($_POST['year']);
		$type = sanitize($_POST['type']);
		$notes = sanitize($_POST['notes']);
		$patientFirstName = sanitize($_POST['pat_first_name']);
		
		// append zeroes if the numbers are 1-9
		if($day < 10 && $day){
			$day = "0".$day;
		}
		if($month < 10 && $month){
			$month = "0".$month;
		}
		if($year < 10 && $year){
			$year = "0".$year;
		}
		
		// create a date string dependent on what date entries were entered
		$date = "";
		if($year){
			$date= $year."-";
		}else{
			$date = "%-";
		}
		
		if($month){
			$date= $date.$month."-";
		}else{
			$date = $date."%-";
		}
		if($day){
			$date = $date.$day;
		}else{
			$date = $date."%";
		}
      if ($month==''&&$day==''&&$year==''&&$primaryFirstName==''&&$patientFirstName==''&&$type==''&&$roomnumber==''&&$notes=='') 
			echo "Please enter one of these: Primary Guest's First Name, Patient Name, Room No, Date Submitted, Status, or Notes.";
	  else {
		// generate the mysql query
		$query = "SELECT * FROM dbBookings WHERE ".
			"date_submitted >= '".$date."' ".
			"AND guest_id LIKE '%".$primaryFirstName."%' ".
			"AND patient LIKE'%".$patientFirstName."%' ".
			"AND status LIKE '%".$type."%' ".
			"AND room_no LIKE '%".$roomNumber."%' ".
			"AND mgr_notes LIKE '%".$notes."%' ".
			"ORDER BY date_submitted DESC";
		
		// connect to the mysql server
		$con=connect();
		// perform the query
		$result = mysqli_query($con, $query);
		mysqli_close($con);
		}
		
      if($result){
		// filter by last name
		$foundcount = 0;
		while($thisRow = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$primaryGuest = retrieve_dbPersons($thisRow['guest_id']);
			if($primaryGuest){
				$pLastName = $primaryGuest->get_last_name();
				if ($primaryLastName=="" || preg_match('/'.strtolower($primaryLastName).'/',strtolower($pLastName))) {
					$pName[$foundcount] = $primaryGuest->get_first_name()." ".$pLastName;
					$pPatient[$foundcount] = $thisRow['patient'];
					$pStatus[$foundcount] = $thisRow['status'];
					$pDateIn[$foundcount] = $thisRow['date_submitted'];
					$pRoomNo[$foundcount] = $thisRow['room_no'];
					$pId[$foundcount] = $thisRow['id'];
					$foundcount++;
				}		
			}
		}
		echo '<div id="target" style="overflow: scroll; width: variable; height: 400px;">';
		echo('<p><strong>Search Results: '.$foundcount.' bookings found...</strong>');
		echo('<hr size="1" width="30%" align="left">');
		// boolean to display admins
		if($foundcount>0) {
			echo('<p><table class="searchResults">');
			echo ('<tr><td class=searchResults><strong>Guest</strong></td>');
			echo ("<td class=searchResults><strong>Patient</strong></td>");
			echo ("<td class=searchResults><strong>Status</strong></td>");
			echo ("<td class=searchResults><strong>Date Submitted</strong></td>");
			echo ("<td class=searchResults><strong>Room</strong></td>");
		    echo ("<td class=searchResults><strong>Actions</strong></td></tr>");
		    for ($i=0; $i<$foundcount; $i++) {
			    echo ("<tr><td class=searchResults>");
				echo "<tr><td class=searchResults>".$pName[$i]."</td>".
					"<td class=searchResults>".$pPatient[$i]."</td>".
					"<td class=searchResults>".$pStatus[$i]."</td>".
					"<td class=searchResults>".nice_date($pDateIn[$i])."</td>".
					"<td class=searchResults>".$pRoomNo[$i]."</td>".
					"<td class=searchResults><a href=\"viewBookings.php?id=update&bookingid=".$pId[$i].
					"\">view</td>";
				if ($pStatus[$i]=="pending" || $pStatus[$i]=="active" || $pStatus[$i]=="reserved")
				    echo "<td class=searchResults><a href=viewBookings.php?id=delete&bookingid=".$pId[$i].">delete</a></td>";
				echo "</tr>";
			}
			echo("</table></p>");
			// note: can't delete an active booking or create a new referral with a non-closed booking
		}
		echo "</div>";
      }
	}
	
	?>
	
	<!-- The footer that we are currently using -->
	
	</div><?php include_once("footer.inc");?>
</div>

<!-- Useful php functions -->
<?php 
//Function to santize strings for searching bookings
function sanitize($string){
	return trim(str_replace('\'','&#39;',htmlentities($string)));
}
function nice_date ($d) {
    return date('M d, Y', mktime(0,0,0,substr($d,3,2),substr($d,6,2),substr($d,0,2)));
}
?>
</body>
</html>