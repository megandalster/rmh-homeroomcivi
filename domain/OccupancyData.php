<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * Updated 2013 by David Phipps & Allen Tucker
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
	private $room_no;        // a specific room for which distinct family names is being gathered
	private $familycounts;    // array of room=>families pairs for each room, over all dates in the range 
	private $family_tags;    // array of tags for each room, of the form $id+$first_name." ".$last_hame for each family's primary guest.
	private $bookingcounts;   // array of room=>bookings pairs for each room, over all dates in the range
	private $guestcounts;  // array of room=>totalguests pairs for each room, over all dates in the range
	private $addresscounts;// array of zip=>count pairs for each zip code, over all dates in the range
	private $addressguestcounts; // array of zip=>totalguests pairs for each zip code, over all dates in the range
	private $agecounts;    // array of age=>count pairs for each patient birth year, over all dates in the range
	private $ageguestcounts; // array of age=>totalguests for each patient birth year, over all dates in the range
	private $hospitalcounts; // array of hospital-department=>count pairs for each hospital-department, over all dates in the range
	private $hospitalguestcounts; //array of hospital-department=>totalguests for each hospital-department, over all dates in the range
	private $bookingcounts_d; //array of room=>closed count pairs
	private $addresscounts_d; //array of zip=>closed count pairs
	private $agecounts_d; //array of age=>closed count pairs
	private $hospitalcounts_d; //array of hospital=>closed count pairs
	/*
	 * Construct occupancy data for a particular date range
	 * 
	 */
	function __construct($date, $enddate, $roomNo) {
		$this->date = $date;
        $this->enddate = $enddate;
        $this->room_no = $roomNo;
        $allBookings = retrieve_all_closed_dbBookings($this->date, $this->enddate, $this->room_no);
        $this->compute_roomcounts($allBookings,$roomNo);
        $this->compute_addresscounts($allBookings);
        ksort($this->addresscounts);
		$this->compute_agecounts($allBookings);
		ksort($this->agecounts);
		$this->compute_hospitalcounts($allBookings);
        ksort($this->hospitalcounts);
		return true;
	}
	
	function occupants_present($occupants) {
		$op = 0;
		foreach ($occupants as $occupant) {
			if (strpos($occupant,"Present")>0) // comment this out if we want to count everyone, whether or not "Present"
				$op++;
		}
		return $op;
	}
    // compute room and guest counts
	function compute_roomcounts($allBookings, $roomNo) {
		$this->roomcounts = array();
		$this->bookingcounts = array();
		$this->guestcounts = array();
		$allRooms = retrieveall_rooms();
		foreach ($allRooms as $room) {
		    $aRoom = $room->get_room_no();
		    $this->familycounts[$aRoom] = 0;
		    $this->family_tags[$aRoom] = array();
		    $this->bookingcounts[$aRoom] = 0;
		    $this->roomcounts[$aRoom] = 0;
		    $this->guestcounts[$aRoom] = 0;
		    $this->bookingcounts_d[$aRoom] = 0;
		}
			
		foreach ($allBookings as $aBooking){
		    if ($aBooking->get_date_in() < $this->date) 
				$bStart = mktime(0,0,0,substr($this->date,3,2),substr($this->date,6,2),substr($this->date,0,2));
			else 
				$bStart = mktime(0,0,0,substr($aBooking->get_date_in(),3,2),substr($aBooking->get_date_in(),6,2),substr($aBooking->get_date_in(),0,2));
			$bEnd = mktime(0,0,0,substr($aBooking->get_date_out(),3,2),substr($aBooking->get_date_out(),6,2),substr($aBooking->get_date_out(),0,2));
			$days = round(($bEnd-$bStart) / 86400);
			$bRoom = $aBooking->get_room_no();
			$bGuests = $this->occupants_present($aBooking->get_occupants());
			if ($bRoom=="" || strlen($bRoom)!=3)
			    $bRoom = "UNK";
			$this->bookingcounts[$bRoom] += 1;
			$this->roomcounts[$bRoom] += $days;
			$this->guestcounts[$bRoom] += $bGuests;
			if($aBooking->get_status() == "closed-deceased") 
				$this->bookingcounts_d[$bRoom] += 1;
		//	var_dump(array_keys($this->family_tags[$bRoom]));
			if (!strpos(implode("'",$this->family_tags[$bRoom]),$aBooking->get_guest_id())){
			    $this->familycounts[$bRoom] += 1;    
			}
			$next_family = $aBooking->get_date_in()."+".
			               $aBooking->get_date_out()."+".
			               $this->pull_details($aBooking->get_guest_id())."+".
			               $days."+".
			               $bGuests;
			$this->family_tags[$bRoom][] = $next_family;	
		}
		foreach ($allRooms as $room) {
		    $aRoom = $room->get_room_no();
		    if($this->bookingcounts_d[substr($aRoom,0,3)] > 0) {
				$this->bookingcounts[substr($aRoom,0,3)] = 
					"{$this->bookingcounts[substr($aRoom,0,3)]} ({$this->bookingcounts_d[substr($aRoom,0,3)]})";
			}
		}	
	}
	// pull the first and last name of the primary guest for a booking
    function pull_details ($guest_id) {
	    $a_guest = retrieve_dbPersons($guest_id);
	    if (!$a_guest)
	        return $guest_id."+".$guest_id;
	    else return $guest_id."+".$a_guest->get_first_name()." ".$a_guest->get_last_name();
	}
	
    // compute address counts
	function compute_addresscounts($allBookings) {
		$this->addresscounts = array();
		$this->addressguestcounts = array();
		$this->addresscounts_d = array();
		$this->addresscounts["UNK"]=0;
		$this->addressguestcounts["UNK"]=0;
		$this->addresscounts_d["UNK"] = 0;
		$addresses = array();
		foreach ($allBookings as $aBooking){
			$g = $aBooking->get_guest_id();
			$bGuest = retrieve_dbPersons($g);
			$bGuests = $this->occupants_present($aBooking->get_occupants());
			// bZip means Maine county, state, or other country
			if ($bGuest) {
			    if ($bGuest->get_county()!="")
			        $bZip = "ME/".$bGuest->get_county();
			    else if ($bGuest->get_state()!="")
			        $bZip = $bGuest->get_state(); 
			}
			else $bZip = "UNK";   
			if (!in_array($bZip, $addresses))
				array_push($addresses, $bZip);
			if (!$this->addresscounts[$bZip]) {
			    $this->addresscounts[$bZip] = 1;
			    $this->addresscounts_d[$bZip] = 0;
			    $this->addressguestcounts[$bZip] = $bGuests;   
			}
			else {
				$this->addresscounts[$bZip] += 1;
				$this->addressguestcounts[$bZip] += $bGuests;	
			}
			if($aBooking->get_status() == "closed-deceased") 
					$this->addresscounts_d[$bZip] += 1;
		}
		foreach ($addresses as $bZip) {
			if($this->addresscounts_d[$bZip] > 0) {
				$this->addresscounts[$bZip] = 
					"{$this->addresscounts[$bZip]} ({$this->addresscounts_d[$bZip]})";
			}
		}
		if($this->addresscounts_d["UNK"] > 0) {
			$this->addresscounts["UNK"] = 
				"{$this->addresscounts["UNK"]} ({$this->addresscounts_d["UNK"]})";
		}
	}
	// compute age counts
	function compute_agecounts($allBookings) {
		$this->agecounts = array();
		$this->agecounts["UNK"]=0;
		$this->agecounts_d = array();
		$this->agecounts_d["UNK"]=0;
		$this->ageguestcounts = array();
		$this->ageguestcounts["UNK"]=0;
		$ages = array();
		foreach ($allBookings as $aBooking){
			$g = $aBooking->get_guest_id();
			$bGuest = retrieve_dbPersons($g);
			$bGuests = $this->occupants_present($aBooking->get_occupants());
			if ($bGuest && $bGuest->get_patient_birthdate()!="") {
				$bDate1 = mktime(0,0,0,substr($bGuest->get_patient_birthdate(),3,2),substr($bGuest->get_patient_birthdate(),6,2),substr($bGuest->get_patient_birthdate(),0,2));
			    $bDate2 = mktime(0,0,0,substr($aBooking->get_date_out(),3,2),substr($aBooking->get_date_out(),6,2),substr($aBooking->get_date_out(),0,2));
			    $bAge = ($bDate2 - $bDate1)/31536000; // years = 365*60*60*24 seconds (approximately)
			}
			else $bAge = "UNK";
			if (!in_array($bAge, $ages))
				array_push($ages, $bAge); //$this->ages[] = $bAge;
			if (!$this->agecounts[$bAge]) {
			    $this->agecounts[$bAge] = 1;
			    $this->agecounts_d[$bAge] = 0;
			    $this->ageguestcounts[$bAge] = $bGuests;
			}
			else {
				$this->agecounts[$bAge] += 1;
				$this->ageguestcounts[$bAge] += $bGuests;
			}
			if($aBooking->get_status() == "closed-deceased") 
				$this->agecounts_d[$bAge] += 1;
			
		}
		foreach ($ages as $bAge) {
			if($this->agecounts_d[$bAge] > 0) {
				$this->agecounts[$bAge] =
					"{$this->agecounts[$bAge]} ({$this->agecounts_d[$bAge]})";
			}
		}
		if($this->agecounts_d["UNK"] > 0) {
			$this->agecounts["UNK"] = 
				"{$this->agecounts["UNK"]} ({$this->agecounts_d["UNK"]})";
		}
	}
	// compute hospital counts
	function compute_hospitalcounts($allBookings) {
		$this->hospitalcounts = array();
		$this->hospitalcounts["other"] = 0;
		$this->hospitalcounts_d = array();
		$this->hospitalcounts_d["other"]=0;
		$this->hospitalguestcounts = array();
		$this->hospitalguestcounts["other"] = 0;
		$hospitals = array();
		foreach ($allBookings as $aBooking){
			$bHospital = $aBooking->get_hospital();
			if ($bHospital=="")
				$bHospital="UNK";
			else $bHospital .= "/".$aBooking->get_department();
			$bGuests = $this->occupants_present($aBooking->get_occupants());
			if (!in_array($bHospital, $hospitals))
				array_push($hospitals, $bHospital);
			if (!$this->hospitalcounts[$bHospital]) {
				$this->hospitalcounts[$bHospital] = 1;
			    $this->hospitalcounts_d[$bHospital] = 0;
			    $this->hospitalguestcounts[$bHospital] = $bGuests; 
			}
			else {
				$this->hospitalcounts[$bHospital] += 1;
				$this->hospitalguestcounts[$bHospital] += $bGuests;
			}
			if($aBooking->get_status() == "closed-deceased") 
				$this->hospitalcounts_d[$bHospital] += 1;
		}
		foreach ($hospitals as $bHospital) {
			if($this->hospitalcounts_d[$bHospital] > 0) {
				$this->hospitalcounts[$bHospital] = 
					"{$this->hospitalcounts[$bHospital]} ({$this->hospitalcounts_d[$bHospital]})";
			}
		}
		if($this->hospitalcounts_d["UNK"] > 0) {
			$this->hospitalcounts["UNK"] =
				"{$this->hospitalcounts["UNK"]} ({$this->hospitalcounts_d["UNK"]})";
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
    function get_family_counts() {
		return $this->familycounts;
	}
	function get_family_tags ($room) {
	    return $this->family_tags[$room];
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
}

?>