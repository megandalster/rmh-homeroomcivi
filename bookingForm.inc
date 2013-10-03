<form method="POST">
<?php     
if ($_SESSION['access_level']==0)
    echo "<b> To request a room at the Ronald McDonald House, please fill out the following form</b> ";
else if ($id=='update') {
    echo "<b> Editing a pending or active booking " ;
	echo ("</b> ");
}
else {
	echo "<b> Creating a new booking " ;
	echo ("</b> ");
}
echo '(<span style="font-size:x-small;color:FF0000">*</span> denotes required fields):<br><br>';
?>
<div align="left">
<fieldset>
<legend>Booking Information:</legend>
<span style='font-size:x-small;color:FF0000'>* </span>Patient: <input type="text" size="25" name="patient_name0" value= "<?php echo($tempBooking->getith_patient(0));?>"/>

<span style='font-size:x-small;color:FF0000'>* </span>Birth date:  
    <select name = "patient_birth_month">
    <?php 
      $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	  echo("<option> </option>");
      for ($i = 1 ; $i <= 9 ; $i ++){
          if('0'.$i == substr($patient_DOB,3,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$months[$i-1]."</option>");
          else
             echo ("<option value='0".$i."'>".$months[$i-1]."</option>");
      }
      for ($i = 10 ; $i <= 12 ; $i ++){
          if($i == substr($patient_DOB,3,2))
             echo ("<option value=".$i." selected = 'yes' >".$months[$i-1]."</option>");
          else
             echo ("<option value=".$i.">".$months[$i-1]."</option>");
      }
    ?>
    </select>
     
    <select name = "patient_birth_day">
    <?php 
      echo("<option>  </option>");
      for ($i = 1; $i <= 9 ; $i ++){
          if($i == substr($patient_DOB,6,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$i."</option>");
          else
             echo ("<option value='0".$i."'>".$i."</option>");
      }
      for ($i = 10; $i <= 31 ; $i ++){
          if($i == substr($patient_DOB,6,2))
             echo ("<option selected = 'yes'>".$i."</option>");
          else
             echo ("<option>".$i."</option>");
      }
    ?>
    </select>
    
    <input type="text" size = "1" maxLength = "2" name="patient_birth_year" value= "<?php echo(substr($patient_DOB,0,2))?>"/><br />
Additional patients:    <input type="text" name="patient_name1" value= "<?php echo($tempBooking->getith_patient(1))?>"/>
<input type="text" name="patient_name2" value= "<?php echo($tempBooking->getith_patient(2))?>"/>
    
					
<br><span style='font-size:x-small;color:FF0000'>* </span>Hospital <select name="hospital">
			<?php 
$hospitals = array("", "Maine Medical Center", "Mercy Hospital", "Specialist", "Spring Harbor Hospital", "Other");	
			foreach($hospitals as $hospital){
				echo ("<option value='");
				echo ($hospital. "' ");
				if ($tempBooking->get_hospital() == $hospital || $id == "new" && $hospital == "")
				    echo "SELECTED";
				echo (">".$hospital."</option>");
			}
			?>
</select>
<br><span style='font-size:x-small;color:FF0000'>* </span>Department of Treatment  
<select name="dept">
			<?php 
$departments = array("", "BBCH", "CARDIOLOGY", "MCCP", "NEEDS", "NICU/CCN", "PRENATAL", "SCU", "SPRING HARBOR", "Other", "Outpatient");			
			foreach($departments as $department){
				echo ("<option value='");
				echo ($department. "' ");
				if ($tempBooking->get_department() == $department)
				    echo "SELECTED";
				echo (">".$department."</option>");
			}
			?>
</select><br>

<span style='font-size:x-small;color:FF0000'>* </span>Submitted by: <input type="text" size="35" name="referred_by" value="
<?php 
if ($tempBooking->get_referred_by()!="")
	echo($tempBooking->get_referred_by());
else 
	echo($user_name." (".substr($user_phone,0,3)."-".substr($user_phone,3,3)."-".substr($user_phone,6).")");
?>"/>
<br><span style='font-size:x-small;color:FF0000'>* </span>Date Submitted:  
    <select name = "date_submitted_month">
    <?php 
      $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	  echo("<option> </option>");
      for ($i = 1 ; $i <= 9 ; $i ++){
          if('0'.$i == substr($tempBooking->get_date_submitted(),3,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$months[$i-1]."</option>");
          else
             echo ("<option value='0".$i."'>".$months[$i-1]."</option>");
      }
      for ($i = 10 ; $i <= 12 ; $i ++){
          if($i == substr($tempBooking->get_date_submitted(),3,2))
             echo ("<option value=".$i." selected = 'yes' >".$months[$i-1]."</option>");
          else
             echo ("<option value=".$i.">".$months[$i-1]."</option>");
      }
    ?>
    </select>    
    <select name = "date_submitted_day">
    <?php 
      echo("<option>  </option>");
      for ($i = 1; $i <= 9 ; $i ++){
          if($i == substr($tempBooking->get_date_submitted(),6,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$i."</option>");
          else
             echo ("<option value='0".$i."'>".$i."</option>");
      }
      for ($i = 10; $i <= 31 ; $i ++){
          if($i == substr($tempBooking->get_date_submitted(),6,2))
             echo ("<option selected = 'yes'>".$i."</option>");
          else
             echo ("<option>".$i."</option>");
      }
    ?>
    </select>
    <input type="text" size = "3" maxLength = "4" name="date_submitted_year" value= "<?php echo("20".substr($tempBooking->get_date_submitted(),0,2))?>"/><br />

<?php
echo "<br><b>Status: </b>". $tempBooking->get_status(); 
echo "<br />";
?>
<br/>

<fieldset>
  <legend><b>Primary Guest:</b></legend>
<?php    if ($id!="new") {
    echo '<a  target="_blank" href="personEdit.php?id='.$guest->get_id().'">'.$guest->get_first_name().' '.$guest->get_last_name().'</a><br>';
    echo 'Relationship to Patient: '.$guest->get_patient_relation().'<br />';
    echo 'Address: '.$guest->get_address().', '. $guest->get_city().', '.$guest->get_state().' '.$guest->get_zip(). '<br />';
    echo 'E-mail: '.$guest->get_email().'<br />';
    $phone1 = $guest->get_phone1();
    $phone2 = $guest->get_phone2(); 
    echo "Phone: (";
        echo substr($phone1,0,3).') '.
             substr($phone1,3,3).'-'. 
             substr($phone1,6,4);
        echo '<br />';
    if ($phone2!="") {
      echo "Alternate Phone: (";
        echo substr($phone2,0,3).') '.
             substr($phone2,3,3).'-'. 
             substr($phone2,6,4);
        echo '<br />';
    }
}
    else {
        echo '<span style="font-size:x-small;color:FF0000">* </span>First Name: <input type="text" size="30" name="first_name_1" value='.$guest->get_first_name().'><br />' ;
        echo '<span style="font-size:x-small;color:FF0000">* </span>Last Name: <input type="text" size="30" name="last_name_1" value='.$guest->get_last_name().'><br />' ;
        echo '<span style="font-size:x-small;color:FF0000">* </span>Relationship to Patient: <input type="text" size = "30" name = "patient_relation_1" value='.$guest->get_patient_relation().'><br />' ;
        echo 'Home Address: <input type="text" size="40" name = "address_1" value='.$guest->get_address().'><br />' ;
        echo 'City: <input type = "text" size = "15" name = "city_1" value='.$guest->get_city().'><br />' ;
        echo 'State: <select name = "state_1">';
    $states = array(" ", "AL","AK","AR","AZ","CA","CO","CT","DC","DE","FL","GA","HI","IA","ID","IL","IN",
				"KS","KY","LA","MA","MD","ME","MI","MN","MO", "MS","MT","NC","ND","NE","NH","NJ","NM",
				"NV","NY","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VA","VT","WA","WI",
				"WV","WY");
        foreach ($states as $st) {
            if ($st == $guest->get_state() || $st=="ME")
                echo "<option selected='yes'>" . $st ."</option>";
            else
                echo "<option>" . $st ."</option>";
        }
        echo '</select><br />' ;
        echo 'Zip: <input type = "text" size = "5" maxLength = "5" name="zip_1" value='.$guest->get_zip().'><br />' ;
		echo 'E-mail: <input type="text" size="30" name="email_1" value='.$guest->get_email().'><br />' ;
        $phone1 = $guest->get_phone1();
        $phone2 = $guest->get_phone2();
		echo '<span style="font-size:x-small;color:FF0000">* </span>Phone: (<input type = "text" size = "2" maxLength = "3" name="phone1_area_1" value="'.substr($phone1,0,3).'" onkeyup="moveOnMax(this,\'input2\')">)';
		echo '<input id="input2" type = "text" size = "2" maxLength = "3" name="phone1_middle_1" value='.substr($phone1,3,3).' onkeyup="moveOnMax(this,\'input3\')">-';
		echo '<input id="input3" type = "text" size = "3" maxLength = "4" name="phone1_end_1" value='.substr($phone1,6,4).'><br>';
// echo 'Alternate Phone: (<input type = "text" size = "2" maxLength = "3" name="phone2_area_1" value="'.substr($phone2,0,3).'">)';
// echo '<input type = "text" size = "2" maxLength = "3" name="phone2_middle_1" value='.substr($phone2,3,3).'>-';
// echo '<input type = "text" size = "3" maxLength = "4" name="phone2_end_1" value='.substr($phone2,6,4).'>';
        
    }
 ?> 
</fieldset>

<br>
 <b>Additional Guests </b>(Name / Relation to Patient / Gender):<br>
<br />
<?php
  $occupants = $tempBooking->get_occupants();
  for ($counter = 1; $counter <= 6; $counter++){
      $startingColonPosition = strpos($occupants[$counter-1],":");
      $secondColonPosition = strpos($occupants[$counter-1],":", $startingColonPosition+1);
      
      $lengthOfRelation = $secondColonPosition - $startingColonPosition - 1;
      $lengthOfGender = strlen($occupants[$counter-1]) - $secondColonPosition - 1;
       
      $occupant_name = substr($occupants[$counter-1],0,$startingColonPosition);
      $occupant_relation = substr($occupants[$counter-1],$startingColonPosition+1,$lengthOfRelation);
      $occupant_gender = substr($occupants[$counter-1], $secondColonPosition+1, $lengthOfGender);
      
      if($occupant_gender == 'Male')	$male_previouslySelected = 'yes';
      else 								$male_previouslySelected = 'no';
      
      if($occupant_gender == 'Female')	$female_previouslySelected = 'yes';
      else 								$female_previouslySelected = 'no';
      
     echo
		'<input type="text" size="25" name = "additional_guest_'.($counter).'" value = "'.$occupant_name.'"/>' . ' / '.
		'<input type="text" size="15" name = "additional_guest_'.($counter).'_relation" value = "'.$occupant_relation.'"/>' . ' / ' .
		'<select name= "additional_guest_'.($counter).'_gender"> 
     	<option value = " "> </option>';
     	if($occupant_gender == 'Male')	echo '<option value = "Male" selected>Male</option>';
     	else 							echo '<option value = "Male">Male</option>';
     	if($occupant_gender == 'Female')	echo '<option value = "Female" selected>Female</option>';
     	else 								echo '<option value = "Female">Female</option>';
     	echo '</select>';
		if ($counter % 2 == 0) echo '<br />';
		else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  }
?>
<br />

<b>Automobile </b>: <br>  Make <input type="text" size="15" name="auto_make" value= "<?php echo($tempBooking->get_auto_make())?>"/>
	Model <input type="text" size="10" name="auto_model" value= "<?php echo($tempBooking->get_auto_model())?>"/>
	Color <input type="text" size="10" name="auto_color" value= "<?php echo($tempBooking->get_auto_color())?>"/>
	State <select name = "auto_state">
  <?php
  $states = array(" ", "AL","AK","AR","AZ","CA","CO","CT","DC","DE","FL","GA","HI","IA","ID","IL","IN",
				"KS","KY","LA","MA","MD","ME","MI","MN","MO", "MS","MT","NC","ND","NE","NH","NJ","NM",
				"NV","NY","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VA","VT","WA","WI",
				"WV","WY");
			foreach ($states as $st) {
			    if ($st == $tempBooking->get_auto_state())
			       echo "<option selected='yes'>" . $st ."</option>";
			    else
				   echo "<option>" . $st ."</option>";
			}
		?>
    </select><br />
<br />
<b>Payment Arrangement:</b><br />
 <input type="radio" name="payment" value="10 per night" <?php if($tempBooking->get_payment_arrangement() == "10 per night") echo('checked')?>/> $10 per night<br />
 <input type="radio" name="payment" value="other" <?php if($tempBooking->get_payment_arrangement()!= "10 per night" && $tempBooking->get_payment_arrangement()!= ""  ) echo('checked')?>/> Other:  
       <input type="text" name="payment_description" size="20" value = "<?php if($tempBooking->get_payment_arrangement()!= "10 per night") echo($tempBooking->get_payment_arrangement())?>"/> <br />
<br />

<?php 

if ($tempBooking->get_status()!="active") {
    ?>
<br><b>Confirmation: </b> <br />
 <input type="radio" name="visitOrWC" value="visit" <?php if($tempBooking->get_date_in() != "Will Call") echo('checked')?>/> 
 Expected Arrival Date
   
    <select name = "date_in_month">
    <?php
    
    echo("<option> </option>");
      for ($i = 1 ; $i <= 9 ; $i ++){
          if('0'.$i == substr($date_in,3,2) && $tempBooking->get_date_in() != "Will Call")
             echo ("<option value='0".$i."' selected = 'yes'>".$months[$i-1]."</option>");
          else
             echo ("<option value='0".$i."'>".$months[$i-1]."</option>");
      }
      for ($i = 10 ; $i <= 12 ; $i ++){
          if($i == substr($date_in,3,2) && $tempBooking->get_date_in() != "Will Call")
             echo ("<option value=".$i." selected = 'yes'>".$months[$i-1]."</option>");
          else
             echo ("<option value=".$i.">".$months[$i-1]."</option>");
      }
    ?>
    </select>
    /
    
    <select name = "date_in_day">
     <?php 
      echo("<option>  </option>");
      for ($i = 1; $i <= 9 ; $i ++){
          if($i == substr($date_in,6,2) && $tempBooking->get_date_in() != "Will Call")
             echo ("<option value='0".$i."' selected = 'yes'>".$i."</option>");
          else
             echo ("<option value='0".$i."'>".$i."</option>");
      }
      for ($i = 10; $i <= 31 ; $i ++){
          if($i == substr($date_in,6,2) && $tempBooking->get_date_in() != "Will Call")
             echo ("<option selected = 'yes'>".$i."</option>");
          else
             echo ("<option>".$i."</option>");
      }
      ?>
      </select>
      /
    
    <input type="text" size = "1" maxLength = "2" name="date_in_year" value= "<?php if ($tempBooking->get_date_in() != "Will Call") echo (substr($date_in,0,2))?>"/> <br />
    
    
 <input type="radio" name="visitOrWC" value="Will Call" <?php if($tempBooking->get_date_in()== "Will Call") echo('checked')?>/> Will Call 
 <?php } // active booking
else echo " <b>Check-in date:</b> ".date_string($tempBooking->get_date_in());
 ?>
<br />
<br />
<b>Approved Use(s): </b> <br />
 <input type="checkbox" name="overnight" value="yes" <?php if($tempBooking->overnight_use() == "yes") echo('checked')?>/> Overnight<br />
 <input type="checkbox" name="day" value="yes" <?php if($tempBooking->day_use() == "yes") echo('checked')?>/> Day <br />
<br />
<b>Additional Notes/Special Requests:</b><br />
	<font color ="blue">
  	<textarea rows = "3" cols = "80" name = "notes" ><?php echo $tempBooking->get_mgr_notes()?></textarea>
    </font>
 <br />
 
</fieldset>

<br>
<fieldset><legend>Ronald McDonald House Health Questionnaire:</legend>
<b>Please answer the following questions for all guests who will be staying at the House.</b> (Check any items that apply.)
<br>
<br>Do you currently:
<br><input type="checkbox" name="health[]" value="flu" 
    <?php if($tempBooking->get_health_question(1) != "0") echo(' checked')?>>1.  Have flu-like symptoms?
<br><input type="checkbox" name="health[]" value="shingles" 
    <?php if($tempBooking->get_health_question(2) != "0") echo(' checked')?>>2.  Have active shingles? 
<br><input type="checkbox" name="health[]" value="tb" 
    <?php if($tempBooking->get_health_question(3) != "0") echo(' checked')?>>3.  Have active TB?
<br><input type="checkbox" name="health[]" value="strep"
    <?php if($tempBooking->get_health_question(4) != "0") echo(' checked')?>>4.  Have active conjunctivitis, impetigo, or strep throat?
<br><input type="checkbox" name="health[]" value="lice"
    <?php if($tempBooking->get_health_question(5) != "0") echo(' checked')?>>5.  Have active scabies, head lice, or body lice?
<br><input type="checkbox" name="health[]" value="whoopingcough"
    <?php if($tempBooking->get_health_question(6) != "0") echo(' checked')?>>6. Have whooping cough?
<br><br>Have you:
<br><input type="checkbox" name="health[]" value="measles" 
    <?php if($tempBooking->get_health_question(7) != "0") echo(' checked')?>>7.  Been exposed to measles in the last 18 days?
<br><input type="checkbox" name="health[]" value="nomeaslesshot" 
    <?php if($tempBooking->get_health_question(8) != "0") echo(' checked')?>>8.  Elected not to be immunized against measles?
<br><input type="checkbox" name="health[]" value="chickenpox"
    <?php if($tempBooking->get_health_question(9) != "0") echo(' checked')?>>9.  Had or been exposed to chicken pox in the last 21 days?
<br><input type="checkbox" name="health[]" value="chickenpoxshot"
    <?php if($tempBooking->get_health_question(10) != "0") echo(' checked')?>>10. Been vaccinated against chicken pox in the last 21 days?
<br><br>Do any of the children:
<br><input type="checkbox" name="health[]" value="hepatitisb"
    <?php if($tempBooking->get_health_question(11) != "0") echo(' checked')?>>11. Carry the hepatitis B virus? 
</fieldset>
</div>
<div align ="left">
<br>Hit Submit to complete this form: 
<input type="submit" value = "Submit" name = "submit"/>
<?php 
if ($id == "new") {
    echo '<br>Hit Clear Form to start over: ';
    echo '<input type="submit" value = "Clear Form" name = "submit"/>';
}
?>
</div>
</form>
<script>
function moveOnMax(field,nextFieldID){
  if(field.value.length >= field.maxLength){
    document.getElementById(nextFieldID).focus();
  }
}
</script>
