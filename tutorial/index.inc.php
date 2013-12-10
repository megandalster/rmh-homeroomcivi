<?PHP
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
	session_start();
	session_cache_expire(30);
?>
<html>
	<head>
		<title>
			RMH Homebase
		</title>
	</head>
	<body>
		
<ol>
		<li>	<a href="?helpPage=login.php">Signing in and out of the System</a></li><br>
			
		<li>For Managers and Social Workers</li><br>
			<ul><li><a href="?helpPage=personEdit.php">How to add a guest </a></li>
			    <li><a href="?helpPage=bookingEdit.php">How to create a new booking</a></li>
				<li><a href="?helpPage=searchBookings.php">How to search for prior bookings</a></li>
				<li><a href="?helpPage=editPendingActiveBookings.php">How to edit a pending or active booking</a></li>
			    <li><a href="?helpPage=data.php">How to use the data page</a></li>
			    <li><a href="?helpPage=searchPeople.php">How to search for guests</a></li>
			</ul><br>
		<li>For Managers and Volunteers</li><br>
		    <ul>
		        <li><a href="?helpPage=room.php">How to use room logs: check in/out, change status, "off-line"</a></li>
		 </ul>
</ol>
		<p>If these help pages don't answer your questions, please contact the <a href="mailto:housemngr@rmhportland.org">House Manager</a>.</p>
	</body>
</html>

