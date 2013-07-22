<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/**
 * RoomLog class for RMH Homeroom.  A RoomLog is an array of Rooms for a 
 * particular date.  Each room in a RoomLog has a unique id.  Each date
 * has a room log with the same number of rooms (21).  
 * @author Allen
 * @version May 1, 2011
 */

// includes
include_once(dirname(__FILE__).'/../domain/Room.php');
include_once(dirname(__FILE__).'/../database/dbRooms.php');

class RoomLog {
	private $id;		  // "yy-mm-dd": the RoomLog's unique key
	private $month;       // Textual month of the year  (e.g., Jan)
	private $day;         // Textual day of the week (Mon - Sun)
	private $year;        // Numerical year (e.g., 2011)
	private $day_of_week; // Numerical day of the week (1-7, Mon = 1)
	private $day_of_year; // Numerical day of the year (1-366)
	private $day_of_month;// Numerical day of month (1 - 12)
	private $month_num;	  // Numerical month
	private $rooms;       // array of 21 room_no:booking_id pairs for this date
	private $log_notes;	  // manager notes for this day's log
	private $status;	  // status of this room log; "unpublished", "published" or "archived"
	/*
	 * Construct a RoomLog for a particular date and initialize its Rooms.
	 * Test that argument $id = "yy-mm-dd" is valid, using the PHP function checkdate
	 */
	function __construct($id) {
        $mm=substr($id,3,2);
        $dd=substr($id,6,2);
        $yy=substr($id,0,2);
        if (! checkdate($mm, $dd, $yy)) {
            $this->id = null;
            echo "Error: invalid date for RoomLog constructor " . $mm.$dd.$yy;
            return false;
        }
        $my_date = mktime(0, 0, 0, $mm, $dd, $yy);
		$this->id = date("y-m-d", $my_date);
		$this->month = date("M", $my_date);
		$this->day = date("D", $my_date);
		$this->year = date("Y", $my_date);
		$this->day_of_week = date("N", $my_date);
		$this->day_of_year = date("z", $my_date) + 1;
		$this->day_of_month = date("d",$my_date);
		$this->month_num = date("m",$my_date);
		$this->rooms = retrieve_all_rooms($this->id);  // get all room_no:booking_id pairs for this date
		$this->log_notes = "";
		$this->status = "unpublished";
		return true;
	}

	function get_id() {
		return $this->id;
	}
	function get_day_of_month() {
		return $this->day_of_month;
	}
	function get_day(){
		return $this->day;
	}
	function get_day_of_week(){
		return $this->day_of_week;
	}
	function get_day_of_year(){
		return $this->day_of_year;
	}
	function get_year(){
		return $this->year;
	}
	function get_rooms(){
		return $this->rooms;
	}
	function set_rooms($rooms) {
	    $this->rooms = $rooms;
	}
	function get_status() {
		return $this->status;
	}
	function set_status($s) {
		if($s=="unpublished" || $s=="published" || $s=="archived") {
			$this->status=$s;
			return true;
		}
		else
			return false;
	}
	/*
	 * return the string name of the date (eg, "February 15, 2011")
	 */
	function get_name() {
	 	return date("F j, Y",mktime(0,0,0,$this->month_num,$this->day_of_month,$this->year));
	}
	function get_end_time() {
	 	return mktime(23,59,59,$this->month_num,$this->day_of_month,$this->year);
	}
	function count_occupied_rooms() {
		$count = 0;
		foreach($this->rooms as $room) 
			if (strpos($room, ":") < strlen($room)-1)
			    $count++;
		return $count;
	}
	function get_log_notes() {
	 	return $this->log_notes;
	}
	function set_log_notes($s){
	 	$this->log_notes=$s;
	}
}

?>