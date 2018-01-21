<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/**
 * Functions to create, retrieve, update, and delete information from the
 * dbZipCodes table in the database.    
 * @version October 25, 2011
 * @author Allen
 */

include_once(dirname(__FILE__).'/dbinfo.php');

/**
 * Inserts a new entry into the dbZipCodes table
 * @param $loaner = the loaner to insert
 */
function insert_dbZipCode ($zip,$district,$city,$county) {
    
    $con=connect();
    $query = "SELECT * FROM dbZipCodes WHERE zip ='".$zip."'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result)!=0) {
        delete_dbZipCodes ($zip);
        $con=connect();
    }
    $query="INSERT INTO dbZipCodes VALUES ('".
				$zip."','".
				$district."','".
				$city."','".
				$county."')";
	$result=mysqli_query($con,$query);
    if (!$result) {
		echo (mysqli_error($con)."unable to insert into dbZipCodes: ".$zip."\n");
		mysqli_close($con);
        return false;
    }
    mysqli_close($con);
    return true;
 }

/**
 * Retrieves an entry from the dbZipCodes table
 * result is a 4-entry associative array [zip, district, city, county]
 */
function retrieve_dbZipCodes ($zip, $district) {
	$con=connect();
	if ($district=="")
        $query = "SELECT * FROM dbZipCodes WHERE zip = \"".$zip."\"";
    else 
        $query = "SELECT * FROM dbZipCodes WHERE district =\"".$district."\"";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result)==0) {
	    mysqli_close($con);
		return false;
	}
	$result_row = mysqli_fetch_assoc($result);
	mysqli_close($con);
	return array($result_row['zip'], $result_row['district'], $result_row['city'], $result_row['county']);
}

/**
 * Updates an entry in the dbZipCodes table by deleting it and re-inserting it
 */
function update_dbZipCodes ($zip, $district, $city, $county) {
	
	if (delete_dbZipCodes($zip))
	   return insert_dbZipCodes($zip, $district, $city, $county);
	else {
	   echo (mysqli_error($con)."unable to update dbZipCodes table: ".$zip);
	   return false;
	}
}

/**
 * Deletes an entry from the dbZipCodes table
 */
function delete_dbZipCodes($zip) {
	$con=connect();
    $query="DELETE FROM dbZipCodes WHERE id=\"".$zip."\"";
	$result=mysqli_query($con,$query);
	mysqli_close($con);
	if (!$result) {
//		echo (mysqli_error($con)."unable to delete from dbZipCodes: ".$id);
		return false;
	}
    return true;
}

?>
