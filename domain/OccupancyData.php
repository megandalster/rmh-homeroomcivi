<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/**
 * Occupancy Data class for RMH Homeroom.  An object in this class contains 
 * room occupancy data for a particular date range; its data comes from the 
 * dbBookings and dbPersons tables. 
 * @author Allen
 * @version May 1, 2011
 */

// includes
include_once(dirname(__FILE__).'/../database/dbBookings.php');
include_once(dirname(__FILE__).'/../database/dbRooms.php');
include_once(dirname(__FILE__).'/../database/dbPersons.php');
include_once(dirname(__FILE__).'/../domain/Booking.php');
include_once(dirname(__FILE__).'/../domain/Person.php');

class OccupancyData {
	private $date;		  // start date
	private $enddate;     // end date
	private $roomcounts;   // array of room=>days booked pairs for each room, over all dates in the range
	private $bookingcounts;   // array of room=>bookings pairs for each room, over all dates in the range
	private $guestcounts;  // array of room=>totalguests pairs for each room, over all dates in the range
	private $addresscounts;// array of zip=>count pairs for each zip code, over all dates in the range
	private $addressguestcounts; // array of zip=>totalguests pairs for each zip code, over all dates in the range
	private $agecounts;    // array of age=>count pairs for each patient birth year, over all dates in the range
	private $ageguestcounts; // array of age=>totalguests for each patient birth year, over all dates in the range
	private $hospitalcounts; // array of hospital-department=>count pairs for each hospital-department, over all dates in the range
	private $hospitalguestcounts; //array of hospital-department=>totalguests for each hospital-department, over all dates in the range
	private $closedcounts_room; //array of room=>closed count pairs
	private $closedcounts_zip; //array of zip=>closed count pairs
	private $closedcounts_age; //array of age=>closed count pairs
	private $closedcounts_hospital; //array of hospital=>closed count pairs
	private $closedcounts_room_d; //array of room=>closed count pairs
	private $closedcounts_zip_d; //array of zip=>closed count pairs
	private $closedcounts_age_d; //array of age=>closed count pairs
	private $closedcounts_hospital_d; //array of hospital=>closed count pairs
	/*
	 * Construct occupancy data for a particular date range
	 * 
	 */
	function __construct($date, $enddate) {
		$this->date = $date;
        $this->enddate = $enddate;
        $allBookings = retrieve_all_closed_dbBookings($this->date, $this->enddate);
        $this->compute_roomcounts($allBookings);
        $this->compute_addresscounts($allBookings);
        ksort($this->addresscounts);
		$this->compute_agecounts($allBookings);
		ksort($this->agecounts);
		$this->compute_hospitalcounts($allBookings);
        ksort($this->hospitalcounts);
		return true;
	}
	// compute room and guest counts
	function compute_roomcounts($allBookings) {
		$this->roomcounts = array();
		$this->bookingcounts = array();
		$this->guestcounts = array();
		$allRooms = retrieve_all_rooms($this->date);
		foreach ($allRooms as $aRoom) {
		    $this->bookingcounts[substr($aRoom,0,3)] = 0;
		    $this->roomcounts[substr($aRoom,0,3)] = 0;
		    $this->guestcounts[substr($aRoom,0,3)] = 0;
		    $this->closedcounts_room[substr($aRoom,0,3)] = 0;
		    $this->closedcounts_room_d[substr($aRoom,0,3)] = 0;
		}
		$this->bookingcounts["unknown"] = 0;
		$this->roomcounts["unknown"] = 0;
		$this->guestcounts["unknown"] = 0;
		$this->closedcounts_room["unknown"] = 0;
		$this->closedcounts_room_d["unknown"] = 0;
		
		foreach ($allBookings as $aBooking){
			if ($aBooking->get_date_in() < $this->date) 
				$bStart = mktime(0,0,0,substr($this->date,3,2),substr($this->date,6,2),substr($this->date,0,2));
			else 
				$bStart = mktime(0,0,0,substr($aBooking->get_date_in(),3,2),substr($aBooking->get_date_in(),6,2),substr($aBooking->get_date_in(),0,2));
			$bEnd = mktime(0,0,0,substr($aBooking->get_date_out(),3,2),substr($aBooking->get_date_out(),6,2),substr($aBooking->get_date_out(),0,2));
			$days = round(($bEnd-$bStart) / 86400);
			$bRoom = $aBooking->get_room_no();
			$bGuests = sizeof($aBooking->get_occupants());
			if (!$bRoom || $bRoom=="UNK") {
			    $this->bookingcounts["unknown"] += 1;
			    $this->roomcounts["unknown"] += $days;
			    $this->guestcounts["unknown"] += $bGuests;
			}
			else {
				$this->bookingcounts[$bRoom] += 1;
				$this->roomcounts[$bRoom] += $days;
				$this->guestcounts[$bRoom] += $bGuests;

				//closed counts
				if($aBooking->get_status() == "closed") {
					$this->closedcounts_room[$bRoom] += 1;
				} else if ($aBooking->get_status() == "closed-deceased") {
					$this->closedcounts_room[$bRoom] += 1;
					$this->closedcounts_room_d[$bRoom] += 1;
				}
			}
		}
		foreach ($allRooms as $aRoom) {
			if($this->closedcounts_room_d[substr($aRoom,0,3)] > 0) {
				$this->closedcounts_room[substr($aRoom,0,3)] = 
					$this->closedcounts_room[substr($aRoom,0,3)] + 
					"(" + $this->closedcounts_room_d[substr($aRoom,0,3)] + ")";
			}
		}

	}
    // compute address counts
	function compute_addresscounts($allBookings) {
		$this->addresscounts = array();
		$this->addressguestcounts = array();
		$this->addresscounts["unknown"]=0;
		$this->addressguestcounts["unknown"]=0;
		foreach ($allBookings as $aBooking){
			$g = $aBooking->get_guest_id();
			$bGuest = retrieve_dbPersons($g);
			$bGuests = sizeof($aBooking->get_occupants());
			// bZip means Maine county, state, or other country
			if ($bGuest) {
			    if ($bGuest->get_county()!="")
			        $bZip = "ME/".$bGuest->get_county();
			    else if ($bGuest->get_state()!="")
			        $bZip = $bGuest->get_state(); 
			}
			else $bZip = "unknown";   
			if (!$this->addresscounts[$bZip]) {
			    $this->addresscounts[$bZip] = 1;
			    $this->addressguestcounts[$bZip] = $bGuests;
			}
			else {
				$this->addresscounts[$bZip] += 1;
				$this->addressguestcounts[$bZip] += $bGuests;
			}
		}
	}
	// compute age counts
	function compute_agecounts($allBookings) {
		$this->agecounts = array();
		$this->agecounts["unknown"]=0;
		$this->ageguestcounts = array();
		$this->ageguestcounts["unknown"]=0;
		foreach ($allBookings as $aBooking){
			$g = $aBooking->get_guest_id();
			$bGuest = retrieve_dbPersons($g);
			$bGuests = sizeof($aBooking->get_occupants());
			if ($bGuest && $bGuest->get_patient_birthdate()!="") {
				$bDate1 = mktime(0,0,0,substr($bGuest->get_patient_birthdate(),3,2),substr($bGuest->get_patient_birthdate(),6,2),substr($bGuest->get_patient_birthdate(),0,2));
			    $bDate2 = mktime(0,0,0,substr($aBooking->get_date_out(),3,2),substr($aBooking->get_date_out(),6,2),substr($aBooking->get_date_out(),0,2));
			    $bAge = ($bDate2 - $bDate1)/31536000; // years = 365*60*60*24 seconds (approximately)
			}
			else $bAge = "unknown";
			if (!$this->agecounts[$bAge]) {
			    $this->agecounts[$bAge] = 1;
			    $this->ageguestcounts[$bAge] = $bGuests;
			}
			else {
				$this->agecounts[$bAge] += 1;
				$this->ageguestcounts[$bAge] += $bGuests;
			}
		}
	}
	// compute hospital counts
	function compute_hospitalcounts($allBookings) {
		$this->hospitalcounts = array();
		$this->hospitalcounts["other"] = 0;
		$this->hospitalguestcounts = array();
		$this->hospitalguestcounts["other"] = 0;
		foreach ($allBookings as $aBooking){
			$bHospital = $aBooking->get_hospital();
			$bHospital .= "/".$aBooking->get_department();
			$bGuests = sizeof($aBooking->get_occupants());
			if (!$bHospital || $bHospital=="/") {
			    $this->hospitalcounts["unknown"] += 1;
			    $this->hospitalguestcounts["unknown"] += $bGuests;
			}
			else {
				$this->hospitalcounts[$bHospital] += 1;
				$this->hospitalguestcounts[$bHospital] += $bGuests;
			}
		}
	}
	function get_date() {
		return $this->date;
	}
	function get_enddate() {
		return $this->enddate;
	}
	// return count of occupancy days for a given room_no
	function get_booking_counts() {
		return $this->bookingcounts;
	}
    function get_room_counts() {
		return $this->roomcounts;
	}
    function get_guest_counts() {
		return $this->guestcounts;
	}
	function get_address_counts() {
		return $this->addresscounts;
	}
	function get_age_counts() {
		return $this->agecounts;
	}
	function get_hospital_counts() {
		return $this->hospitalcounts;
	}
    function get_address_guest_counts() {
		return $this->addressguestcounts;
	}
	function get_age_guest_counts() {
		return $this->ageguestcounts;
	}
	function get_hospital_guest_counts() {
		return $this->hospitalguestcounts;
	}
	function get_closed_counts_by_room() {
		return $this->closedcounts_room;
	}
	function get_closed_counts_by_zip() {
		return $this->closedcounts_zip;
	}
	function get_closed_counts_by_age() {
		return $this->closedcounts_age;
	}
	function get_closed_counts_by_hospital() {
		return $this->closedcounts_hospital;
	}
}

?>