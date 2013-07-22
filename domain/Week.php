<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
/*
 * Week is an array of 7 RoomLogs.  Weeks start on Mondays.
 * For any date given to the constructor function, the script will find the 
 * Monday of that week and generate dates from there
 * @authors Max, Allen
 * @version 2/1/2011
 */
class Week {
	private $id;	    // the first day of the week, "yy-mm-dd"
 	private $roomlogs;  // array of 7 RoomLogs, beginning Monday
	private $name;      // the name of the week (eg, "March 7, 2011 to March 13, 2011")
	private $end_of_week_timestamp;	// the mktime timestamp of the end of the week

 	/**
 	 * Creates a new week's worth of RoomLogs.
 	 */
 	function __construct($roomlogs) {
 		$this->roomlogs=$roomlogs;
		$this->id=$this->roomlogs[0]->get_id();
		$this->name=$this->roomlogs[0]->get_name()." to ".$this->roomlogs[6]->get_name();
		$this->end_of_week_timestamp=$this->roomlogs[6]->get_end_time();
 	}

	function get_id() {
		return $this->id;
	}
	function get_roomlogs() {
		return $this->roomlogs;
	}
	function get_name() {
		return $this->name;
	}
	function get_end() {
		return $this->end_of_week_timestamp;
	}
}
?>
