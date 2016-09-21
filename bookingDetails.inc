<html>
 <br/>

 <div align="left">
  <fieldset><legend>Booking Information:</legend>
  <?php
    $current_id = $booking->get_guest_id();
    $current_guest = retrieve_dbPersons($current_id);
 ?>
  
         <b>Patient: </b> <?php echo($booking->getith_patient(0));?> <br/>
         Birth date:  <?php echo date_string($current_guest->get_patient_birthdate())?> <br/>
         <?php 
         if (sizeof($booking->get_patient())>1) {
             echo "Additional Patients: ". $booking->getith_patient(1);
             if (sizeof($booking->get_patient())>2)
                 echo ", ". $booking->getith_patient(2). "<br>";
             else echo "<br>";
         }
         ?>
         Gender: <?php echo($current_guest->get_gender());?> <br/>
         Hospital:  <?php echo($booking->get_hospital());?> <br/>
         Department: <?php echo($booking->get_department());?> <br/>
     	 Submitted by: <?php echo $booking->get_referred_by();?> <br/>      
 <br/>
 <b>Status: </b> <?php echo($booking->get_status()."<br/>");
    if ($booking->get_status()== "pending"){
         echo('Date submitted: '.date_string($booking->get_date_submitted())."<br>");
         echo('Room No: '.$booking->get_room_no().'<br>');     
    }
    else if($booking->get_status()== "active" || $booking->get_status()== "confirmed"){
         echo('Date In: '.date_string($booking->get_date_in()).'<br>');
         echo('Room No: '.$booking->get_room_no());
         if ($booking->get_linked_room()!="")
         	echo ", ".$booking->get_linked_room();
         echo '<br>';
    } 
    else if($booking->get_status()== "closed" || $booking->get_status()== "closed-deceased")
         echo('Date In: '.date_string($booking->get_date_in()).
         	  ', Room No: '.$booking->get_room_no().
              '<br/> Date Out: '.date_string($booking->get_date_out()).'<br>');
?> 
<br><b>Manager's Notes/Special Requests:  </b><font color="red">
     <?php echo($booking->get_mgr_notes()."</b><br/><br/></font>"); ?>
<b>Primary Guest: </b>
 
  <a href = <?php  echo("personEdit.php?id=".$current_id);?> ><?php echo($current_guest->get_first_name()." ".$current_guest->get_last_name())?> </a>
    <br/>
    Additional Guests: 
    <?php $occupants = $booking->get_occupants();
    for($i=1 ; $i< sizeof($occupants) ; $i++){
        $j = strpos($occupants[$i],":");
        if (!$j) $j=strlen($occupants[$i]);
        echo(substr($occupants[$i],0,$j));
        if ($j+3<strlen($occupants[$i]))
                echo " (".str_replace(":"," ",substr($occupants[$i],$j+1)).")";
        if ($i < sizeof($occupants)-1)
            echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        else echo '<br>';
     }?>
        
     <br>Automobile: <?php echo $booking->get_auto_make()." ".$booking->get_auto_model()." ".
                             $booking->get_auto_color()." ".$booking->get_auto_state() ;
		?>
    <br />
    
     Payment Arrangement:  <?php echo($booking->get_payment_arrangement());?> <br> 

     <?php 
    echo "Approved Uses: ";
     if ($booking->overnight_use() == "yes") echo ('Overnight');
     if ($booking->day_use() == "yes") echo (' Day');
    echo "<br>";
     ?>
     <?php
      if($booking->get_status() == "pending"){
     	if ($booking->get_date_in() != "" && $booking->get_date_in() != "Will Call")
            $d = date_string($booking->get_date_in());
        else $d = "Will Call";
     	echo('<b> Expected Arrival Date: </b> '.$d);
     }
     ?>
   
     
  </fieldset>
<br/><br/>                    
<fieldset><legend>Ronald McDonald House Health Questionnaire Responses:</legend>

Do you currently:
<br>
    1.  Experience flu-like symptoms? <?php if($booking->get_health_question(1) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?> 
<br>2.  Have active shingles? <?php if($booking->get_health_question(2) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?> 
<br>3.  Have active TB? <?php if($booking->get_health_question(3) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?>
<br>4.  Have active conjunctivitis, impetigo, or strep throat? <?php if($booking->get_health_question(4) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?>
<br>5.  Have active scabies, head lice, or body lice? <?php if($booking->get_health_question(5) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?>
<br>6.  Have whooping cough? <?php if($booking->get_health_question(6) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?>
<br><br>Have you:
<br>7.  Been exposed to measles in the last 18 days? <?php if($booking->get_health_question(7) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?> 
<br>8.  Elected not to be immunized against measles? <?php if($booking->get_health_question(8) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?> 
<br>9.  Had or been exposed to chicken pox in the last 21 days? <?php if($booking->get_health_question(9) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?>
<br>10. Been vaccinated against chicken pox in the last 21 days? <?php if($booking->get_health_question(10) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?>
<br><br>Do any of the children:
<br>11. Carry the hepatitis B virus? <?php if($booking->get_health_question(11) != "0") echo('<b>Yes</b>'); else echo('<b>No </b>')?>

</fieldset>

  <br/>
  <br/>
<?php if ($booking->get_status()=="pending") {?>
<form method = "POST" action = <?php echo "viewBookings.php?id=update&bookingid=".$booking->get_id()?>>
<input type="hidden" name = "booking_id" value = "<?php echo($id)?>" />
          <b> Flag as:</b> 
            <select name = "flag"> 
                     <option value = "new" <?php if($booking->get_flag() == "new") echo SELECTED?>> new </option>
                     <option value = "viewed"  <?php if($booking->get_flag() == "viewed") echo SELECTED?>> viewed </option>
                     <option value = "requires attention" <?php if($booking->get_flag() == "requires attention") echo SELECTED?>> requires attention </option>
                     <option value = "past arrival date" <?php if($booking->get_flag() == "past arrival date") echo SELECTED?>> past arrival date </option>
                     </select><br/>
<br/>
          <input type ="submit" value = "Update Flag" name = "submit" />              
      <br/>
</form>
<?php }?>   
</div>
</html>