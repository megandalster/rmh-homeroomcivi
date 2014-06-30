<?PHP
/*
 * Copyright 2011 by Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
 session_start();
 session_cache_expire(30);
 include_once(dirname(__FILE__).'/domain/Booking.php');
 include_once(dirname(__FILE__).'/database/dbBookings.php');
 include_once(dirname(__FILE__).'/domain/Person.php');
 include_once(dirname(__FILE__).'/database/dbPersons.php');
	
?>
<html>
	<head>
		<title>
			View Bookings
		</title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
	</head>
	<body>
		<div id="container">
			<?PHP include('header.php');?>
			<div id="content">
				<!-- your main page data goes here. This is the place to enter content -->
				
		<?PHP
					   
        $id = $_GET["id"];
        $bookingid = $_GET['bookingid'];
        //updates the notes and flag if these have just been edited            
        if ($_POST['submit'] == "Update Flag") {
	           update_notes_and_flag($bookingid);
			echo "Flag updated: hit <a href='viewBookings.php?id=pending'>bookings: view</a> to see all pending bookings.";
        }
        if ($id=="delete") {
    		if (delete_dbBookings($bookingid)) {
    			echo "<p>Booking successfully deleted.</p>";
    		}
     	    else {
    		    echo '<p id="error">Error: booking not deleted</p>'. $id;
    		    die();
    	    }
	    }
        //if viewing a specific booking
        else if ($id != "pending"){
            $booking = retrieve_dbBookings($bookingid);
            if ($booking == false) {
              echo('<p id="error">Error: There is no booking with this id in the database</p>'. $id);
		      die();
            }
            else{
              $guest_id = $booking->get_guest_id();
	          $guest= retrieve_dbPersons($guest_id);
	          $patient_DOB = $guest->get_patient_birthdate();
	          if($booking->get_date_in() != "Will Call")
	              $date_in = $booking->get_date_in();
            }
            echo "<br>Viewing booking for ";
			echo($guest->get_first_name()." ".$guest->get_last_name());
			
			echo(" submitted on ".date_string($booking->get_date_submitted())." ");
			if ($_SESSION['access_level']==3)
  	            echo '<a href = "bookingEdit.php?id=update&referralid='.$booking->get_id().'" > (Edit this booking) </a><br>';
		//	else echo "<br>";
			//echo "(To find other bookings, <a href='searchBookings.php'>search the database</a>!)";
	        //updates the booking if changes have just been submitted
		    if($_POST['submit']== "Submit"){
		        $update_result = update_booking($id);
				if($update_result !='incomplete' && $update_result != false){
					echo ("<b> <center> <font color = green> You have successfully updated this booking </font> </center> </b>");
					$booking = $update_result;			        
			    }
				if($update_result == false)
					echo ("ERROR: Booking could not be updated.");
		    }
		    //show the details of the booking
			include('bookingDetails.inc');
        }
        //otherwise, show just pending bookings              
		else{
		   echo "<p><strong>Viewing all Pending and Reserved Bookings</strong><br />";
		   echo "(To find other bookings, <a href='searchBookings.php'>search the database</a>!)";
		//grab the sort parameter
	       $s = $_GET['sort'];
		   $sort = get_sort_string($s);
					    
		   //r parameter marks that the sort should be reversed	    
		   if($_GET['r'] != 'r'){
		        //determines whether to append an r parameter to a particular sort link
			    if ($s ==  'flag')
                   $r5= "&r=r";
			    elseif ($s ==  'status')
                   $r4 = "&r=r";
                elseif ($s ==  'patient')
                   $r3 = "&r=r";
                elseif ($s ==  'guest')
                   $r2 = "&r=r";
                else 
                   $r1 = "&r=r";
			}
			//retrieve all the pending bookings		    
			$pending_bookings = retrieve_all_pending_dbBookings();
			$num_bookings = sizeof($pending_bookings);
			//handles the case when there are no pending bookings
			if($num_bookings == 0){
				echo ("<p><b> There are currently no pending or reserved bookings. </b>");
			}
            //otherwise
			else{
			   //reverse the sort if r is set
			   if($_GET['r']=='r')
				  $pending_bookings = array_reverse($pending_bookings);						     
				echo("<p>");
				if ($num_bookings != 1)			
					echo("There are currently ".$num_bookings." pending or reserved bookings: ");
				else 
					echo("There is currently ".$num_bookings." pending or reserved booking: ");
					
			
			//headings to the table, clicking a heading sorts by that type of data
			//or reverses the sort if currently sorted by that type
			echo('<p><table border="0"; align="left"><tr>');
			echo('<td> <a href= "viewBookings.php?id=pending'.$r1.'">Date Submitted </a></td>');
			echo('<td> Primary Guest</td>');
			echo('<td> Patient Name </td>');
			echo('<td> Status </td>');
			echo('<td> Flags </td>');
			echo('<td> Actions </td></tr>');										
						
			foreach($pending_bookings as $current_booking){
			  //updates flags of any bookings that are past arrival date  
			  check_arrival_date($current_booking);
			  //default color for viewed booking
		      $color = "White";
		      
		      //changes the color based on flag value
			  $flag = $current_booking->get_flag();
			  if($flag =="new")
				  $color = "Yellow";
			  elseif ($flag == "requires attention")
				  $color = "LightCoral";
			  elseif ($flag == "past arrival date")
				  $color = "LightGrey";
						    
			  echo('<tr border="0"; align="left">');

			  //prints the date submitted
			  echo('<td align="center" bgcolor='.$color.'>'.date_string($current_booking->get_date_submitted()).'</td>'.
				   '<td align="center" bgcolor='.$color.'>');
			  
			  //pulls out array of primary guest id's
			  $guest_id = $current_booking->get_guest_id();
			  
			  //prints the name of each primary guest as a 
			  //link to the corresponding viewPerson page
			  $current_guest = retrieve_dbPersons($guest_id);
                  //handles when no guest with the corresponding ID in databse
                  if($current_guest==false)
                         echo ("Error: This booking contains a guestID for a person not in the database");
                  else  
						 echo ("<a href = viewPerson.php?id=".$guest_id." >".$current_guest->get_first_name()." ".$current_guest->get_last_name(). " </a>");
			  //prints patient name
			  echo('</td>'.
				   '<td align="center" bgcolor='.$color.'>'.$current_booking->getith_patient(0).'</td>');
			  
			  //prints status and flag
              echo('<td align="center" bgcolor='.$color.'>'.$current_booking->get_status().'</td>');
		      echo('<td align="center" bgcolor='.$color.'>'.$current_booking->get_flag().'</td>');
						    
			  //prints links for viewing and editing		
			  //confirms on click if user wants to delete the guest    
			  
		  $guestName = $current_guest->get_first_name()." ".$current_guest->get_last_name();
			  echo('<td> <a href="viewBookings.php?id=update&bookingid='.$current_booking->get_id().'">view</a>
						 <a href="viewBookings.php?id=delete&bookingid='.$current_booking->get_id().'" onclick= " if (!confirm(\'Are you sure you want to delete '.$guestName.'?\')) return false;">delete</a></td>');	
			  
			  echo("</tr>");
			  echo("\n");
		    }
			}
			
		    //completes the table
			echo("</table></p>");
			
	  }//end showing all pending bookings
					  
	?>
					
				<!-- footer for homeroom-->
				
			</div><?PHP include('footer.inc');?>
		</div>
	</body>
</html>

<?php 
/**
* update_booking retrieves and sanitizes $_POST data, and uses it to update
* the booking with $b_id, then updates the databse
* @param   string    $b_id the id of the booking to update
* @return the updated booking or false if unsuccessful
*/
function update_booking($b_id){
  
    include('bookingValidate.inc');
	$e = validate_form();

	if(sizeof($e)==0){
    
      $b = retrieve_dbBookings($b_id);
      $referred_by = trim(str_replace("'","\'", htmlentities($_POST['referred_by'])));
      $b->set_referred_by($referred_by);
      $hospital = trim(str_replace("'","\'", htmlentities($_POST['hospital'])));
      $b->set_hospital($hospital);
      $department = trim(str_replace("'","\'", htmlentities($_POST['dept']))); 
      $b->set_department($department);
      $b->set_health_questions($_POST['health_questions']);
    
      //sets date_in to Will Call if date in is not yet known
      if($_POST['visitOrWC'] == "Will Call"){
           $b->set_date_in("Will Call");
      }
      else{
         $date_in = $_POST['date_in_year'].'-'.
                    $_POST['date_in_month'].'-'.
                    $_POST['date_in_day'];
         $b->set_date_in($date_in);
       }
     
       //updates day and overnight use
       if ($_POST['overnight']=="yes")
         $b->set_overnight_use("yes");
       else
         $b->set_overnight_use("no");
       if ($_POST['day']=="yes")
         $b->set_day_use("yes");
       else
         $b->set_day_use("no");
    
      //sets payment arrangement
      $payment = trim(str_replace("'","\'",htmlentities($_POST['payment'])));    
      $b->set_payment_arrangement($payment);
    
      //updates notes
      $notes = trim(str_replace("'","\'",htmlentities($_POST['notes'])));
      $b->set_mgr_notes($notes);
    
      $new_status = $_POST['status'];
      $b->set_status($new_status);
    
    
      if(update_dbBookings($b))
          return $b;
          
      else
      return false;
	}
	else{
	    echo('The booking could not be updated for the following reasons: <br/>');
	    show_errors($e);
	    echo('Click <a href = viewBookings.php?id=update&bookingid='.$b_id.'>Edit This Booking </a> to try editing again');
	    return 'incomplete';
	}
    
    
}
/**
 * updates the notes and flag of the previously viewed booking
 * using the $_POST method
 */
function update_notes_and_flag($bookingid){
    $b = retrieve_dbBookings($bookingid);
//	$notes = trim(str_replace("'","\'",htmlentities($_POST['notes'])));
//    $b->set_mgr_notes($notes);
    if ($b) { 
        $b->set_flag($_POST['flag']);
        update_dbBookings($b);
    }
}
/**
 * 
 * compare function for date submitted sort
 * @param Booking $booking1
 * @param Booking $booking2
 */
function date_submitted_sort($booking1, $booking2){

     $dt1 = $booking1->get_date_submitted();
     $dt2 = $booking2->get_date_submitted();
     if ($dt1 > $dt2){
          return -1;
     }
     elseif ($dt1 < $dt2){
          return 1;
     }
     else {
          return 0;
     }
}
/**
 * 
 * compare function for date in sort
 * @param Booking $booking1
 * @param Booking $booking2
 */
function date_in_sort($booking1, $booking2){
     $dt1 = $booking1->get_date_in();
     $dt2 = $booking2->get_date_in();
     if ($dt1 > $dt2)
          return -1;
     
     elseif ($dt1 < $dt2)
          return 1;
     
     else 
          return 0;
          
}
/**
 * 
 * compare function for flag sort
 * @param Booking $booking1
 * @param Booking $booking2
 */
function flag_sort($booking1, $booking2){
    $f1 = $booking1->get_flag();
    $f2 = $booking2->get_flag();
    if ($f1 < $f2)
          return -1;
     elseif ($f1 > $f2)
          return 1;
     else 
          return 0;
}
/**
 * 
 * compare function for patient sort
 * @param Booking $booking1
 * @param Booking $booking2
 */
function patient_sort($booking1, $booking2){
    $p1 = $booking1->getith_patient(0);
    $p2 = $booking2->getith_patient(0);
    if ($p1 < $p2)
          return -1;
    elseif ($p1 > $p2)
          return 1;
     else 
          return 0;
}
/**
 * 
 * checks the arrival date of the current booking and updates the 
 * flag in the database to "past arrival date" if this is the case
 * @param Booking $b
 */
function check_arrival_date($b){
    //corrects an incorrectly set "past arrival date" flag
    if($b->get_flag() == "past arrival date"){
       if ($b->get_date_in() > date("y-m-d") || $b->get_date_in() == "Will Call"){
          $b->set_flag('viewed');
          update_dbBookings($b);
       }
    }
    //checks whether the date of arrival has passed
    elseif($b->get_date_in() != "Will Call"){ 
      if ($b->get_date_in() < date("y-m-d")){
          $b->set_flag("past arrival date");
          update_dbBookings($b);
      }
   }
}
/**
 * 
 * given sort parameter passed through the $_GET method,
 * returns the corresponding sort method name
 * 
 * @param string $s the $_GET sort parameter
 * @return string the method name corresponding to the sort parameter
 */
function get_sort_string($s){
    if ($s ==  'datein')
      return "date_in_sort";
    elseif ($s ==  'guest')
      return "guest_sort";
    elseif ($s ==  'patient')
      return "patient_sort";
    elseif ($s ==  'flag')
      return "flag_sort";
    else 
      return "date_submitted_sort";
    
}


?>

