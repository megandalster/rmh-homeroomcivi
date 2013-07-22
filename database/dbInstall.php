<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
/**
 * Initializes the database by creating the tables:
 * dbBookings, dbLoaners, dbPersons, dbRooms, dbRoomLogs, dbWeeks, and dbLog
 * @version February 25, 2011
 * @author Allen
 */
?>

<html>
<title>
Database Initialization
</title>
<body>
<?php
	echo("Installing Tables...<br />");
	include_once(dirname(__FILE__).'/dbinfo.php');
	include_once(dirname(__FILE__).'/dbBookings.php');
	include_once(dirname(__FILE__).'/dbPersons.php');
	include_once(dirname(__FILE__).'/dbRooms.php');
	include_once(dirname(__FILE__).'/dbRoomLogs.php');
	include_once(dirname(__FILE__).'/dbLog.php');
//	include_once(dirname(__FILE__).'/dbWeeks.php');

	// connect
	$connected=connect();
 	if (!$connected) echo mysql_error();
 	echo("connected...<br />");
    echo("database selected...<br />");

	// setup all of the tables
	if(create_dbRooms())
		echo("dbRooms table initialized...<br />");
	if(create_dbRoomLogs())
		echo("dbRoomLogs table initialized...<br />");
 	if (create_dbBookings())
        echo("dbBookings table initialized...<br />");
    if (create_dbPersons())
	    echo("dbPersons table initialized...<br />");
	if (create_dbLog())
	    echo("dbLog table initialized...<br />");

	echo("Installation of mysql tables complete.<br>");
	echo(" To prevent data loss, run this program only if you want to clear all the tables.</p>");
?>
</body>
</html>