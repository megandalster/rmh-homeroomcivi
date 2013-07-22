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
</head>

<body>

<div id="container">
	<?php include_once("header.php");?>
	<div id="content">
	<!-- All the searching stuff goes here -->
	<?php 
	// Display some info
	echo('</p><p>You may search for bookings using any or all of the following options.'.
		'<span style="font-size:x-small">  (A search for "an" would return D'.
		'<strong>an</strong>, J<strong>an</strong>e, <strong>An</strong>'.
		'n, and Sus<strong>an</strong></span>.)</p>');
	include("searchBookings.inc");
	// Check if a search was made
	if($_POST['submit'] == "Search"){
		// Grab each search string from the form and
		// sanitize it
		$primaryFirstName = sanitize($_POST['p_first_name']);
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
		// generate the mysql_query
		$query = "SELECT * FROM dbBookings WHERE ".
			"date_submitted LIKE '".$date."' ".
			"AND guest_id LIKE '%".$primaryFirstName."%' ".
			"AND patient LIKE'%".$patientFirstName."%' ".
			"AND status LIKE '%".$type."%' ".
			"AND room_no LIKE '%".$roomNumber."%' ".
			"AND mgr_notes LIKE '%".$notes."%' ".
			"ORDER BY date_submitted DESC";
		
		// connect to the mysql server
		connect();
		// perform the query
		$result = mysql_query($query);
		
		// close connection if the result was invalid
		if(!$result){
			echo mysql_error();
		}
		
		// list the results
		echo('<p><strong>Search Results: '.mysql_num_rows($result).' bookings found...</strong>');
		echo('<hr size="1" width="30%" align="left">');
		
		// boolean to display admins
		echo('<p><table class="searchResults">');
		if(mysql_num_rows($result))
			echo ('<tr><td class=searchResults><strong>Guest(s)</strong></td>');
			echo ("<td class=searchResults><strong>Patient</strong></td>");
			echo ("<td class=searchResults><strong>Status</strong></td>");
			echo ("<td class=searchResults><strong>Submitted</strong></td>");
			echo ("<td class=searchResults><strong>Room</strong></td></tr>");
		while($thisRow = mysql_fetch_array($result, MYSQL_ASSOC)){
			// beging grabbing more results
			// Appends a 20 so it reads 2011-...
			$dateIn = "20".substr($thisRow['date_submitted'],0,8);
			
			echo ("<tr><td class=searchResults>");
			$primaryGuest = retrieve_dbPersons($thisRow['guest_id']);
			if($primaryGuest){
					$pFirstName = $primaryGuest->get_first_name();
					$pLastName = $primaryGuest->get_last_name();
					$string = $pFirstName." ".$pLastName;
			}			
			// Begin creating a row for each entry
			echo "<tr><td class=searchResults>".$string."</td>".
				"<td class=searchResults>".$thisRow['patient']."</td>".
				"<td class=searchResults>".$thisRow['status']."</td>".
				"<td class=searchResults>".$dateIn."</td>".
				"<td class=searchResults>".$thisRow['room_no']."</td>".
				"<td class=searchResults><a href=\"viewBookings.php?id=update&bookingid=".$thisRow['id'].
				"\">view</td>";
			if ($thisRow['status']=="pending") 
				echo "<td class=searchResults><a href=viewBookings.php?id=delete&bookingid=".$thisRow['id'].">delete</a></td>";
			else if ($thisRow['status']=="closed" || $thisRow['status']=="closed-deceased")	
				echo "<td class=searchResults><a href=bookingEdit.php?id=".$thisRow['guest_id'].">create new booking</a></td>";
			echo "</tr>";
			// note: can't delete an active booking or create a new referral with a non-closed booking
		}
		echo("</table></p>");
		
//		mysql_close();
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
?>
</body>
</html>