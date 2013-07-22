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
	$database = "rmhhomeroomDB";
	$user = "rmhhomeroomDB";
	$password = "foodyWr1";

 	$connected = mysql_connect($host,$user,$password);
 	if (!$connected) return mysql_error();
    $selected = mysql_select_db($database, $connected);
    if (!$selected) return mysql_error();
    else return true;
 }
?>
