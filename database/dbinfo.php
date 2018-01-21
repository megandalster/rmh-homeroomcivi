<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
/*
 * This file contains the connection information for the database.
 * It may be modified for every installation of the software.
 * @author Max Palmer <mpalmer@bowdoin.edu>
 * @version updated 2/12/08
 */
 function connect() {
    $host = "localhost";
	$database = "rmhhomeroomdb";
	$user = "rmhhomeroomdb";
	$password = "foodyWr1";

	$connected = mysqli_connect($host,$user,$password);
	if (!$connected) { echo "not connected"; return mysqli_error($connected);}
	$selected = mysqli_select_db($connected,$database);
	if (!$selected) { echo "not selected"; return mysqli_error($connected); }
	else return $connected;
 }
?>
