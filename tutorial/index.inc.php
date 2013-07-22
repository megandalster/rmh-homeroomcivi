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
		<p><strong><i>Homeroom</i> Help Pages</strong></p>
<ol>
		<li>	<a href="?helpPage=login.php">Signing in and out of the System</a></li><br>
			<ul><li><a href="?helpPage=index.php">About your Personal Home Page</a></li></ul><br>
		<li>	<strong>Working with People in the Database</strong> (Managers Only)</li><br>
			<ul><li><a href="?helpPage=searchPeople.php">Searching for People</a></li>
			    <li><a href="?helpPage=edit.php">Editing People</a></li>
			    <li><a href="?helpPage=rmh.php">Adding People </a></li>
			</ul><br>
		<li>	<a href="?helpPage=roomLog.php">Working with Room Logs</a> (Volunteers and Managers)</li><br>
				<br>
		 <li>   <a href="?helpPage=viewBookings.php">Working with Bookings and Referral Forms</a> (Social Workers and Managers)</li>
</ol>
		<p>If these help pages don't answer your questions, please contact the <a href="mailto:housemngr@rmhportland.org">House Manager</a>.</p>
	</body>
</html>

