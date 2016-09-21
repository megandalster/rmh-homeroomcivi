<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * Updated 2013 by David Phipps & Allen Tucker
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/*
 * Booking class for RMH Homeroom.  A Booking is a connection between a Room and a Person
 * on a particular date.
 * @author Allen
 * @version Fabruary 7, 2011
 */
include_once(dirname(__FILE__).'/Room.php');
include_once(dirname(__FILE__).'/../database/dbBookings.php');
include_once(dirname(__FILE__).'/../database/dbPersons.php');

class Booking {
    private $id;            // unique identifier of the form $current_date."guest_id"
    private $date_submitted;// date that the boooking information was submitted, in the form "yy-mm-dd"
    private $date_in;       // check-in date, in the form "yy-mm-dd"
	private $guest_id;      // id of the primary guest e.g., "John2077291234"
 	private $status;		// "pending", "active", "closed", or "closed-deceased"
  	private $room_no;	    // id of the room; null if status == "pending" or "wait-list"
	private $patient;       // array of up to 3 patients for whom this booking is made
  	private $occupants;     // array of up to 6 people staying in the room.  
  	                        // Each entry has the form $first_name:$relationship_to_patient:$gender:$present
  	                        // e.g., array("John:father:Male:Present", "Jean:mother:Female:", "Teeny:sibling:Female:Present")
  	private $auto;          // automobile make:model:color:state
	private $linked_room;   // (optional) id of a room where other family members are staying
 	private $date_out;      // check-out date "yy-mm-dd" ; null if unknown
    private $referred_by;   // id of the person (eg, social worker) requesting this booking
	private $hospital;      // name of the hospital where the patient is staying
    private $department;    // (optional) department where the treatment occurs
    private $health_questions; // 11 health_questions for the family ("00000000000" means no problems)
    /* 
     * Do you:
     * 1.  Currently experience flu-like symptoms?
     * 2.  Have active shingles?
     * 3.  Have active TB?
     * 4.  Have active conjunctivitis, impetigo, or strep throat?
     * 5.  Have active scabies, head lice, or body lice?
     * 6. Have whooping cough?
     * Have you:
     * 7.  Been exposed to measles in the last 18 days?
     * 8.  Elected not to be immunized against measles?
     * 9.  Had or been exposed to chicken pox in the last 21 days?
     * 10. Been vaccinated against chicken pox in the last 21 days?
     * Do any of the children:
     * 11. Carry the hepatitis B virus? 
     */
	private $payment;       // the paymant arrangement for this booking, typically $10/night
	private $overnight;     // marks approval for overnight use (yes/no)
	private $day;           // marks approval for day use (yes/no)
	private $day_use_date;		//string with with format "yy-mm-dd"
    private $mgr_notes;		// (optional) notes from the manager/social worker
	private $flag;        // to mark whether this booking has been viewed since submission
	                        
    /*
     * construct a new Booking
     */
    function __construct ($date_submitted, $date_in, $guest_id, $status, $room_no, $patient, 
            $occupants, $auto, $linked_room, $date_out, $referred_by, 
            $hospital, $department, $health_questions, $payment, $overnight, $day, $day_use_date, $mgr_notes, $flag) {
    	$this->id = $date_submitted . $guest_id;
    	$this->date_submitted = $date_submitted;
    	$this->date_in = $date_in;
    	$this->guest_id = $guest_id;
    	$this->status = $status;
    	$this->room_no = $room_no;
    	$this->patient = $patient;
    	$this->occupants = $occupants;
    	$this->auto = $auto;
    	$this->linked_room = $linked_room;
    	$this->date_out = $date_out; 
    	$this->referred_by = $referred_by;
    	$this->hospital = $hospital;
    	$this->department = $department;
    	$this->health_questions = $health_questions;
    	$this->payment = $payment;
    	$this->overnight = $overnight;
    	$this->day = $day;
    	$this->day_use_date = $day_use_date;
    	$this->mgr_notes = $mgr_notes;
    	$this->flag = $flag;
    }
    /* 
     * getters
     */
    function get_id() {
        return $this->id;
    }
    function get_date_submitted() {
        return $this->date_submitted;
    }
    function get_date_in() {
        return $this->date_in;
    }
    function get_guest_id() {
        return $this->guest_id;
    }
    function get_status () {
    	return $this->status;
    }
    function get_room_no() {
        return $this->room_no;
    }
    function get_patient() {   // returns an array
        return $this->patient;
    }
    function getith_patient($i) {   // returns a string
        if ($i>sizeof($this->patient)-1)
            return "";
        else return $this->patient[$i];
    }
    function get_occupants() {
        return $this->occupants;
    }
    function get_auto() {
        return $this->auto;
    }
    function get_auto_make () {
        if ($this->auto == "")
		    return "";
		else {
		    return substr($this->auto,0,strpos($this->auto,":"));
		}
	}
    function get_auto_model() {
        if ($this->auto == "")
		    return "";
		else {
		    $model = substr($this->auto,strpos($this->auto,":")+1);
		    return substr($model,0,strpos($model,":"));
		}
	}
	function get_auto_color () {
	    if ($this->auto == "")
		    return "";
		else {
		    $model = substr($this->auto,strpos($this->auto,":")+1);
		    $color = substr($model,strpos($model,":")+1);
		    return substr($color,0,strpos($color,":"));
		}
	}
	function get_auto_state () {
	    if ($this->auto == "")
		    return "";
		else {
		    $model = substr($this->auto,strpos($this->auto,":")+1);
		    $color = substr($model,strpos($model,":")+1);
		    $state = substr($color,strpos($color,":")+1);
		    return $state;
		}
	}
	function get_linked_room() {
        return $this->linked_room;
    }
    function get_date_out() {
        return $this->date_out;
    }
    function get_referred_by() {
        return $this->referred_by;
    }
    function get_hospital() {
        return $this->hospital;
    }
    function get_department() {
        return $this->department;
    }
    function get_payment_arrangement() {
        return $this->payment;
    }
    function get_health_questions() {
        return $this->health_questions;
    }
    function get_health_question($i) { // $i indexes questions 1-11
    	return substr($this->health_questions,$i-1,1);
    }
    function get_mgr_notes() {
        return $this->mgr_notes;
    }
    function get_flag(){
        return $this->flag;
    }
    function overnight_use(){
        return $this->overnight;
    }
    function day_use(){
        return $this->day;
    }
	function get_day_use_date(){
        return $this->day_use_date;
    }
    
    /*
     *  assign a room to a booking after client has confirmed -- book it immediately if the date is past
     */
    function reserve_room ($room_no, $date) {
    	$r = retrieve_dbRooms($room_no,$date,"");
        if ($r) {
            if ($date<date('y-m-d')) {
                $r->book_me($this->id);
                $this->status = "active";
            }
            else { 
                $r->reserve_me($this->id);
                $this->status = "reserved";
            }
            $this->date_in = $date;	
            $this->room_no = $room_no;
            update_dbBookings($this);
            return $this;
        }
        else return false;
    }
    
    function book_room ($room_no, $date) {
    	$r = retrieve_dbRooms($room_no,$date,"");
        if ($r) {
            $r->book_me($this->id);  
            $this->date_in = $date;	
            $this->room_no = $room_no;
            $this->status = "active";
            update_dbBookings($this);
            return $this;
        }
        else return false;    	  
    }
    function change_room ($old_room, $new_room, $date) {
        $r = retrieve_dbRooms($old_room,$date,"");
    	if ($r) 
    	   $r->unbook_me($this->id); 
        $r = retrieve_dbRooms($new_room,$date,"");
        if ($r) {
            $r->book_me($this->id);  
            $this->room_no = $r->get_room_no();
            $this->status = "active";
            update_dbBookings($this);
            return $this;
        }
        else return false;
    }
    function add_linked_room($room_no, $date) {
    	$r = retrieve_dbRooms($room_no,$date,"");
        if ($r) {
            $r->book_me($this->id);  
            $this->linked_room = $r->get_room_no();
            update_dbBookings($this);
            return $this;
        }
        else return false;
    }
    function remove_linked_room($date) {
    	$r = retrieve_dbRooms($this->linked_room,$date,"");
        if ($r) {
            $r->unbook_me($this->id);  
            $this->linked_room = "";
            update_dbBookings($this);
            return $this;
        }
        else return false;
    }
    /*
     *  check a client out of the room and update the client's record.
     */
    function check_out ($date, $deceased){
        $r = retrieve_dbRooms($this->room_no,$date,"");
        $r2 = retrieve_dbRooms($this->linked_room,$date,"");
        $p = retrieve_dbPersons(substr($this->id,8));
        if ($r) { 
            $r->unbook_me($this->id);
            if ($r2)
            	$r2->unbook_me($this_id);
            if ($this->status=="active") { // changing back from active to closed
                if($deceased) {
                    $this->status = "closed-deceased";
                } else {
                   $this->status = "closed";
                }
                $this->date_out = $date;  
            }
            else {                        // changing back from reserved to pending
                $this->status = "pending";
                $this->date_in = "";
                $this->room_no = "";
            }
            update_dbBookings($this);
            if ($p && ($this->status=="closed" || $this->status=="closed-deceased")) {
            	$p->add_prior_booking($this->id);
            	update_dbPersons($p);
            }
            return $this;
        }
        else return false;
    }
    function add_occupant($name, $relationship, $gender, $present) {
    	$this->occupants[] = $name.":".$relationship.":".$gender.":".$present;
    	update_dbBookings($this);
    }
    function remove_occupant($name) {
    	for ($i=0; $i<sizeof($this->occupants); $i++)
    	    if (strpos($this->occupants[$i],$name.":")!==false) {
    	        unset($this->occupants[$i]);
    	        return;
    	    }
    }
    function remove_occupants() {
        $this->occupants = array();
    }
    function set_patient($patient) { // array of patient names
        $this->patient = $patient;
    }
    function add_patient($name) {  // string argument
    	$this->patient[] = $name;
    	update_dbBookings($this);
    }
    function add_auto ($make, $model, $color, $state) {
        $this->auto = $make.":".$model.":".$color.":".$state;
    }
    function remove_auto () {
        $this->auto = "";
    }
    function set_status($status) {
        $this->status = $status;
    }
    function set_auto($auto) {
        $this->auto = $auto;
    }
    function set_room_no($room_no) {
        $this->room_no = $room_no;
    }
    function set_date_submitted ($new_date_submitted) {
    	$this->date_submitted = $new_date_submitted;
    }
    function set_date_in ($new_date_in) {
    	$this->date_in = $new_date_in;
    }
    function set_date_out($date_out) {
        $this->date_out = $date_out;
    }
    function set_referred_by($referred_by){
        $this->referred_by = $referred_by;
    }
    function set_hospital($hospital){
        $this->hospital = $hospital;
    }
    function set_department($department) {
        $this->department = $department;
    }
    function set_payment_arrangement($payment) {
        $this->payment = $payment;
    }
    function set_health_questions($health_questions) {
        $this->health_questions = $health_questions;
    }
    function set_health_question($i, $value) { // $i indexes questions 1-11
    	$x = $this->health_questions;
    	$this->health_questions = substr($x,0,$i-2).$value.substr($x,$i);
    }
    function set_mgr_notes($mgr_notes) {
        $this->mgr_notes = $mgr_notes;
    }
    function set_flag($f){
        $this->flag = $f;
    }
    function set_overnight_use($u){
        $this->overnight = $u;
    }
    function set_day_use($u){
        $this->day = $u;    
    }
    function set_day_use_date($date){
    	$this->day_use_date=$date;
    }

}
?>
