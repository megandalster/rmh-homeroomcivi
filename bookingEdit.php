<?php
session_start();
session_cache_expire(30);
include_once(dirname(__FILE__).'/domain/Person.php');
include_once(dirname(__FILE__).'/database/dbPersons.php');
include_once(dirname(__FILE__).'/domain/Booking.php');
include_once(dirname(__FILE__).'/database/dbBookings.php');
include_once(dirname(__FILE__).'/database/dbLog.php');

?>
<html>
<head>
<title>Booking Edit</title>

<!--  Choose a style sheet -->
<link rel="stylesheet" href="styles.css" type="text/css" />
<link rel="stylesheet" href="calendar.css" type="text/css" />
</head>
<!-- Body portion starts here -->
<body>
	<div id="container">
		<!--  the header usually goes here -->
		<?php include_once("header.php");?>
		<div id="content">
		<br/>

<?php

    // Get the info of the user who is making the referral
	$user = retrieve_dbPersons($_SESSION['_id']);
	$user_name = $user->get_first_name()." ".$user->get_last_name();
	$user_phone = $user->get_phone1();

	$id = $_GET['id'];
	if (!$_GET['referralid']) 
	    $referralid = date("y-m-d").$id; // new booking from an old one
	else $referralid = $_GET['referralid'];
	// prepare to update a new or existing referral that has not yet been edited
	// set up the proper form for the user to fill out
	if($_POST['submit'] != 'Submit') {
	  if ($id == "new") { // create a new booking from scratch
	        $status = "pending";
	        $date_in = "Will Call";
            $room_no = "";
            $flag = "new";
            $guest = new Person("","","","","","","","","","","","","","","","", "", "");
            $tempBooking = new Booking(date("y-m-d"),"Will Call","","pending","","","","","","","","","","00000000000", "", "", "", "", "","new"); 
	  }
	  else if ($id=="update") {
	        $tempBooking = retrieve_dbBookings($referralid);
            $status=$tempBooking->get_status();
            $date_in = $tempBooking->get_date_in();
            $room_no = $tempBooking->get_room_no();
            $flag = $tempBooking->get_flag();
            $guestid = $tempBooking->get_guest_id();
            $guest = retrieve_dbPersons($guestid);
            $patient_DOB = $guest->get_patient_birthdate();
            $patient_gender = $guest->get_patient_gender(); 
	  }
	  else { // id is a guest id... create a new booking for that guest
	       $status = "pending";
           $date_in = "Will Call";
           $room_no = "";
           $flag = "new";
           $guestid = $id;
	       $guest = retrieve_dbPersons($id);
	   //    $last_booking = retrieve_persons_closed_dbBookings($id);
           if (!$guest){
                echo("The guest with id '".$id."' does not exist in the database. Please fill out a blank form below:");
                $guest = new Person("","","","","","","","","","","","","","","","", "","");
                $patient_DOB = ""; 
                $patient_gender = ""; 
                $patient_relation = "";          
           }
           else 
           {
           		$patient_name = $guest->get_patient_name();
           		$patient_DOB = $guest->get_patient_birthdate();
           		$patient_gender = $guest->get_patient_gender();
           		$patient_relation = $guest->get_patient_relation();
                $lastBooking = retrieve_persons_closed_dbBookings($id);
                if($lastBooking) {
					    $last_hospital = $lastBooking->get_hospital();
                        $last_department = $lastBooking->get_department();
                        $last_auto =   $lastBooking->get_auto();
                        $last_occupants = $lastBooking->get_occupants();
                }
                else {
					    $last_hospital = "";
                        $last_department = "";
                        $last_auto =  "";
                        $last_occupants = array($guest->get_first_name()." ".$guest->get_last_name().":".
					                    $guest->get_patient_relation().":".$guest->get_gender().":");
                }
                $tempBooking = new Booking(date("y-m-d"), "Will Call", $guest->get_id(), $status, "", $guest->get_patient_name(), 
           		    $last_occupants, $last_auto,  
                    "","","",$last_hospital,$last_department,"00000000000", "", "", "", "", "","new");  
           }                          
	  }
	  include('bookingForm.inc'); 
	}
	// now process the form that has been submitted
	if ($_POST['submit'] == 'Submit') { 
        // check for errors    
        include('bookingValidate.inc');
        $errors = validate_form($id,$referralid);
        if($errors){
            show_errors($errors);                                          
        }
        // okay, good to go
        else{
            $primaryGuest = process_form($id,$referralid);
            $booking = build_POST_booking($id,$primaryGuest,$referralid);
            echo("This booking has been "); if ($id=="new") echo "submitted."; else echo "updated.";
            echo '<a href = "bookingEdit.php?id=update&referralid='.$booking->get_id().'" > (Edit this booking) </a><br>';
			// Create the log message
			$message = "<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$user_name."</a>".
	 		" has created or edited a booking for <a href='viewPerson.php?id=".$primaryGuest->get_id()."'>".
	 		$primaryGuest->get_first_name()." ".$primaryGuest->get_last_name()."</a>";
	 		add_log_entry($message); 
	 		include('bookingDetails.inc');
        }
	}
?>
		</div><?php include_once("footer.inc");?>
	</div>
</body>
</html>
<?php
// sanitize the primary guest and patient data and reconcile with dbPersons
function process_form($id,$referralid)	{

   	if ($id=="update") {
   	    $tempBooking = retrieve_dbBookings($referralid);
   	    $guestid = $tempBooking->get_guest_id();
        $guest = retrieve_dbPersons($guestid);
   	    $first_name = $guest->get_first_name();
		$phone1 = $guest->get_phone1();
		$patient_gender = $guest->get_patient_gender();
		$guest_gender = $guest->get_gender();
		$patient_relation = trim(str_replace('\\\'','',htmlentities($_POST['patient_relation'])));
   	}
   	else if ($id=="new"){ // creating a new booking from scratch -- edit everything
        $first_name = trim(str_replace("'","", htmlentities(str_replace('&','and',$_POST['first_name_1']))));
		$last_name = trim(str_replace("'","", htmlentities($_POST['last_name_1'])));
		$patient_gender = trim(str_replace('\\\'','',htmlentities($_POST['patient_gender_1'])));
		$guest_gender = trim(str_replace('\\\'','',htmlentities($_POST['gender_1'])));		
        $patient_relation = trim(str_replace('\\\'','',htmlentities($_POST['patient_relation'])));		
        $address = trim(str_replace("'","", htmlentities($_POST['address_1'])));
		$city = trim(str_replace("'","", htmlentities($_POST['city_1'])));
		$state = $_POST['state_1'];
		$zip = trim(htmlentities($_POST['zip_1']));
		$phone1 = $_POST['phone1_area_1'].$_POST['phone1_middle_1'].$_POST['phone1_end_1'];
		$phone2 = $_POST['phone2_area_1'].$_POST['phone2_middle_1'].$_POST['phone2_end_1'];
		$email = trim(str_replace("'","\'", htmlentities($_POST['email_1'])));
    }
    else { //creating a new booking from an old one -- pull old guest information
    	$tempBooking = retrieve_dbBookings($referralid);
   	    $guest = retrieve_dbPersons($id);
   	    $guest_gender = $guest->get_gender();
   	    $patient_relation = trim(str_replace('\\\'','',htmlentities($_POST['patient_relation'])));		
        $first_name = $guest->get_first_name();
		$phone1 = $guest->get_phone1();
		$patient_gender = $guest->get_patient_gender();
    }
    $patient_name = array(trim(str_replace("'","", htmlentities($_POST['patient_name0']))));
    if ($_POST['patient_name1']!="") 
            $patient_name[] = trim(str_replace("'","", htmlentities($_POST['patient_name1'])));
    if ($_POST['patient_name2']!="") 
            $patient_name[] = trim(str_replace("'","", htmlentities($_POST['patient_name2'])));
    $patient_birthdate = substr($_POST['patient_birth_year'], 2,2).'-'. 
                             $_POST['patient_birth_month'].'-'.
                             $_POST['patient_birth_day'];
                             
    $patient_gender = $_POST['patient_gender'];
    $currentEntry = retrieve_dbPersons($first_name.$phone1);
    if(!$currentEntry) {
            $currentEntry = new Person($last_name, $first_name, $guest_gender, "", $address, $city,$state, $zip, $phone1, $phone2, 
                                   $email, "guest", "", implode(',',$patient_name),$patient_birthdate,$patient_gender,$patient_relation,"");
    }
    else {
            $currentEntry->set_patient_name($patient_name);
            $currentEntry->set_patient_birthdate($patient_birthdate);
            $currentEntry->set_patient_gender($patient_gender);
            $currentEntry->set_patient_relation($patient_relation);
            $currentEntry->set_gender($guest_gender);
            $currentEntry->add_type("guest");
    }
    insert_dbPersons($currentEntry);
    return $currentEntry;
    
}
// build a booking from the posted data and save it
function build_POST_booking($id,$primaryGuest,$referralid) {
    if ($id=="new")
	    $date_submitted = substr($_POST['date_submitted_year'],2,2).'-'.
                 $_POST['date_submitted_month'].'-'.
                 $_POST['date_submitted_day']; 
    else 
        $date_submitted = substr($referralid,0,8);
    
    if($_POST['visitOrWC'] == "Will Call" ){
       $date_in = "Will Call";
    }
    else if ($_POST['date_in_year'] && $_POST['date_in_month'] && $_POST['date_in_day']) {
       $date_in = substr($_POST['date_in_year'], 2,2).'-'.
                 $_POST['date_in_month'].'-'.
                 $_POST['date_in_day'];
    }

    if($_POST['day']=="yes" && $_POST['day_use_year'] && $_POST['day_use_month'] && $_POST['day_use_day']){
    	$day_use_date = substr($_POST['day_use_year'],2,2).'-'.
                 $_POST['day_use_month'].'-'.
                 $_POST['day_use_day'];               
    }
     
    $referred_by = trim(str_replace("'","\'", htmlentities($_POST['referred_by'])));
    if($_POST['payment'] != "other")
        $payment = "10 per night";
    else
        $payment = trim(str_replace("'","\'",htmlentities($_POST['payment_description'])));
    $notes = trim(str_replace("'","\'",htmlentities($_POST['notes'])));
    $hospital = trim(str_replace("'","\'", htmlentities($_POST['hospital'])));
    $department = trim(str_replace("'","\'", htmlentities($_POST['dept'])));
    $healthvalues = array("flu","shingles","tb","strep","lice","whoopingcough",
        "measles","nomeaslesshot","chickenpox","chickenpoxshot","hepatitisb");$health_questions = "";
    $auto = trim(str_replace("'","\'",htmlentities($_POST['auto_make']))).":".
            trim(str_replace("'","\'",htmlentities($_POST['auto_model']))).":".
            trim(str_replace("'","\'",htmlentities($_POST['auto_color']))).":".
            trim(str_replace("'","\'",htmlentities($_POST['auto_state'])));
    if ($auto==":::ME")
        $auto = "";
    for ($i=1; $i<=11; $i++)
    	if ($_POST['health'] && in_array($healthvalues[$i-1],$_POST['health']))
    		$health_questions .= "1";
        else $health_questions .= "0";    
    
    if ($referralid && retrieve_dbBookings($referralid)) {
    	$pendingBooking = retrieve_dbBookings($referralid);
    //	$pendingBooking->set_date_submitted($date_submitted);
        $pendingBooking->set_date_in($date_in);
        $pendingBooking->set_patient($primaryGuest->get_patient_name());
        $pendingBooking->set_auto($auto);
        $pendingBooking->set_payment_arrangement($payment);
        $pendingBooking->set_overnight_use($_POST['overnight']);
        $pendingBooking->set_day_use($_POST['day']);
        if ($_POST['status']!="")
            $pendingBooking->set_status($_POST['status']);
        $new_room = substr($_POST['room_no'],0,3);
        if ($new_room!="" && $new_room!=$pendingBooking->get_room_no()) {
            $pendingBooking->change_room($pendingBooking->get_room_no(),
                                substr($_POST['room_no'],0,3),$date_in);
        }
        if ($_POST['linked_room']=="remove")
        	$pendingBooking->remove_linked_room($date_in);
        else
        	$pendingBooking->add_linked_room(substr($_POST['linked_room'],0,3),$date_in);
        if ($_POST['status']=="active")
            $pendingBooking->set_date_out("");
        else // closed or closed-deceased
            $pendingBooking->set_date_out(substr($_POST['date_out_year'],2,2).'-'.$_POST['date_out_month'].'-'.$_POST['date_out_day']);
        $pendingBooking->set_day_use_date($day_use_date);
        $pendingBooking->set_mgr_notes($notes);
        $pendingBooking->set_referred_by($referred_by);
        $pendingBooking->set_hospital($hospital);
        $pendingBooking->set_department($department);
        $pendingBooking->set_health_questions($health_questions);
        $pendingBooking->remove_occupants();
    }
    else {
    	$pendingBooking = new Booking($date_submitted, $date_in, $primaryGuest->get_id(), "pending", "", $primaryGuest->get_patient_name(), 
                                  array(), $auto, "", "", $referred_by, $hospital, $department, 
                                  $health_questions, $payment, $_POST['overnight'], $_POST['day'], $day_use_date, $notes, "new");                      
    }
    if ($id=='new')  
        $pendingBooking-> add_occupant($primaryGuest->get_first_name()." ".$primaryGuest->get_last_name(),$primaryGuest->get_patient_relation(),$primaryGuest->get_gender(),"Present");
    for($count = 1 ; $count <= 6 ; $count++){
        if($_POST['additional_guest_'.$count] != "")
           	$pendingBooking->add_occupant($_POST['additional_guest_'.$count], $_POST['additional_guest_'.$count.'_relation'],
            								$_POST['additional_guest_'.$count.'_gender'], $_POST['additional_guest_'.$count.'_present']);
    }
    
    insert_dbBookings($pendingBooking);

    return $pendingBooking;
}

?>