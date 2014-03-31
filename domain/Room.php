<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/*
 * Room class for RMH Homeroom.  A Room is a place where a guest can 
 * stay on a particular date.  It provides the details of a Booking in 
 * the Room Log.  
 * @author Jesus
 * @version Feberuary 15, 2011
 */
include_once(dirname(__FILE__).'/../database/dbRooms.php');

class Room {
 	private $room_no;	    // room number in the house, like "125" or "233"
 	private $beds;			// bed configuration: string of 2T, 1Q, Q, etc.
 	private $capacity;      // maximum number of persons
	private $bath;	        // string: "y" or "n" if there's a private bath
	private $status;	    // string: "clean", "dirty", "booked", "reserved", "off-line"
	private $booking;       // the current booking id for this room 
	private $room_notes;	// (optional) room-specific notes, like "use if 4+ guests"

	/*
	 * Room constructor. Initializes a room with no booking and clean status.
	 */
	function __construct ($room_no, $beds, $capacity, $bath, $status, $booking, $room_notes) {
		// Assign each parameter to its class variable
		$this->room_no = $room_no;
		$this->beds = $beds;
		$this->capacity = $capacity;
		$this->bath = $bath;
		$this->status = $status;   
		$this->booking = $booking;     
		$this->room_notes = $room_notes; 
	}
	// *************** Getters ***************
	
	function get_room_no () {
		return $this->room_no;
	}
	function get_beds () {
		return $this->beds;
	}
	function get_capacity () {
		return $this->capacity;
	}
	function get_bath(){
		return $this->bath;
	}
	function get_status () {
		return $this->status;
	}
	function get_booking_id() {
		return $this->booking;
	}
    function get_room_notes () {
		return $this->room_notes;
	}
    function reserve_me ($booking_id){
		$r = retrieve_dbRooms($this->room_no,"","");
        if ($r) {
        	$r->status = "reserved";
        //    $r->booking = $booking_id;
            update_dbRooms($r);   
            return $r;
        }
        else return false;  // can't reserve if not clean
	}
	// use this only if checking in an already-reserved booking
	function book_me ($booking_id){
		$r = retrieve_dbRooms($this->room_no,"","");
        if ($r) {
        	$r->status = "booked";
        //    $r->booking = $booking_id;
            update_dbRooms($r);   
            return $r;
        }
        else return false;  // can't book if not reserved
	}
    function unbook_me ($booking_id){
		$r = retrieve_dbRooms($this->room_no,"","");
        if ($r) {
        	$r->status = "dirty";
        //    $r->booking = null;
            update_dbRooms($r);   
            return $r;
        }
        else return false;  // can't unbook if not booked
	}
	// use this only for changing status to "clean", "dirty", or "off-line" 
	// and its not currently booked (there's nobody in it)
	function set_status ($new_status) {
	    $r = retrieve_dbRooms($this->room_no,"","");
		if ($r->status!="booked" && $new_status!="booked" && $new_status!="reserved") {
		    $this->status = $new_status;
		    update_dbRooms($this);
		    return $this;
		}
		else return false;
	}
	function set_room_notes($notes) {
		$this->room_notes = $notes;
		update_dbRooms($this); 
		return $this;
	}
	function set_beds ($beds) {
		$this->beds = $beds;
		update_dbRooms($this);
		return $this;
	}
	function set_capacity($newCapacity){
		$this->capacity = $newCapacity;
		update_dbRooms($this);
		return $this;
	}
	function set_bath($newBath){
		$this->bath = $newBath;
		update_dbRooms($this);
		return $this;
	}
}
?>