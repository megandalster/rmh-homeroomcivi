<?php
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/

/*
 * Person class for RMH Homeroom
 * @author Alex Lucyk
 * @version May 1, 2011
 */
include_once(dirname(__FILE__).'/../database/dbZipCodes.php');
class Person {
    private $id;               // id (unique key) = first_name . phone1
    private $last_name;        // last name - string
    private $first_name;       // first name - string
    private $gender;			//gender of person - string
    private $employer;			//employer of the person - string
    private $address;              // address - string
    private $city;                 // city - string
    private $state;                // state - string
    private $zip;                  // zip code - integer
    private $phone1;               // primary phone
    private $phone2;               // alternate phone
    private $email;             // email address
    
    private $patient_name;      // array of up to 3 patients for this person
    private $patient_birthdate; // format: 11-03-12 for the first patient only
    private $patient_gender;    // "Male" "Female" or "Unknown" for the first patient only.
    private $patient_relation;  // relationship of person to the patient, eg "mother"
    private $prior_bookings;    // array of booking ids; e.g., '11-02-08John2077291234'
    private $mgr_notes;         // manager's notes
    private $county;           // county in Maine; otherwise blank
    private $type;             // array of 'manager', 'socialworker', 'guest', 'volunteer'
    private $password;         // password for database access: default = $id

        /**
         * constructor for a Person
         */
    function __construct($last_name, $first_name, $gender, $employer, $address, $city, $state, $zip, $phone1, $phone2, $email,
                         $type, $prior_bookings, $patient_name, $patient_birthdate, $patient_gender, $patient_relation, $password){                
        $this->id = $first_name . $phone1; 
        $this->last_name = $last_name;
        $this->first_name = $first_name;
        $this->gender = $gender;
        $this->employer = $employer;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
        $this->phone1 = $phone1;
        $this->phone2 = $phone2;
        $this->email = $email;
      
        $this->patient_name = explode(',',$patient_name);
        $this->patient_birthdate = $patient_birthdate;
        $this->patient_gender = $patient_gender;
        $this->patient_relation = $patient_relation;
        
        $this->prior_bookings = explode(',',$prior_bookings);
        $this->mgr_notes = "";
        $this->county = $this->compute_county();
        $this->type = explode(',',$type);
        
        if ($password=="")
            $this->password = md5($this->id);
        else $this->password = $password;       
    }
    //getter functions
    function get_id() {
        return $this->id;
    }
    function get_first_name() {
        return $this->first_name;
    }
    function get_last_name() {
        return $this->last_name;
    }
    function get_gender() {
    	return $this->gender;
    }
	function get_employer() {
    	return $this->employer;
    }
    function get_address() {
        return $this->address;
    }
    function get_city() {
        return $this->city;
    }
    function get_state() {
        return $this->state;
    }
    function get_zip() {
        return $this->zip;
    }
    function get_phone1() {
        return $this->phone1;
    }
    function get_phone2() {
        return $this->phone2;
    }
    function get_email(){
        return $this->email;
    }
    function get_patient_name(){  // returns an array
        return $this->patient_name;
    }
    function getith_patient_name($i){  // returns a string
        return $this->patient_name[$i];
    }
    function get_patient_birthdate(){
        return $this->patient_birthdate;
    }
    function get_patient_gender(){
        return $this->patient_gender;
    }
    function get_patient_relation(){
        return $this->patient_relation;
    }
    function get_type(){
        return $this->type;
    }
    //returns true if the person has type $t
    function check_type($t){
        if (in_array($t, $this->type))
            return true;
        else
            return false;
    }
    function get_prior_bookings(){
        return $this->prior_bookings;
    }
    
    function get_mgr_notes(){
        return $this->mgr_notes;
    }
    function get_county (){
        return $this->county;
    }
    function get_password () {
        return $this->password;
    }
    //setter functions
    function set_last_name($ln) {
        $this->last_name = $ln;
    }
    function set_gender($g) {
    	$this->gender = $g;
    }
	function set_employer($e) {
    	$this->employer = $e;
    }
    function set_address($ad) {
        $this->address = $ad;
    }
    function set_city($c) {
        $this->city = c;
    }
    function set_state($s) {
       $this->state = $s;
    }
    function set_zip($z) {
        $this->zip = $z;
    }
    function set_phone2($p2) {
        $this->phone2 = $p2;
    }
    function set_email($em){
        $this->email = em;
    }
    function add_type ($t) {
        if (!in_array($t, $this->type))
            $this->type[] = $t;
    }
    function set_patient_name($pn) {
        $this->patient_name = $pn;
    }
    function setith_patient_name($i,$pn){
        $this->patient_name[$i] = $pn;
    }
    function set_patient_birthdate ($pbd){
        $this->patient_birthdate = $pbd;
    }
    function set_patient_gender ($pr){
        $this->patient_gender = $pr;
    }
    function set_patient_relation ($pr){
        $this->patient_relation = $pr;
    }
    
    function set_password ($new_password) {
        $this->password = $new_password;
    }
    function set_mgr_notes($mnote){
        $this->mgr_notes = $mnote;
    }
    function set_county ($county){
        $this->county = $county;
    }
    //adds a booking id to the array of prior booking ids
    function add_prior_booking($id){
        $this->prior_bookings[] = $id;
    }
    function compute_county () {
        if ($this->state=="ME") {
            $countydata = false;
            if ($this->get_zip()!="")
	            $countydata = retrieve_dbZipCodes($this->get_zip(),"");    
	        else if (!$countydata) 
	            $countydata = retrieve_dbZipCodes("",$this->get_city());
	        if ($countydata) {
	            if ($this->zip == "")
	            	$this->zip = $countydata[0];
	            return $countydata[3];
	        }
        }
        return "";
    }
    
}
?>
