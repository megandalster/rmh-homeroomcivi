<form method="POST">
<?php     
if ($_SESSION['access_level']==0)
    echo "<b> To request a room at the Ronald McDonald House, please fill out the following form</b> ";
else if ($id=='update') {
    echo "<b> Editing an existing booking " ;
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
<span style='font-size:x-small;color:FF0000'>* </span>Patient: <input type="text" name="patient_name0" value="<?php echo($tempBooking->getith_patient(0))?>"/>

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
    
    <?php
        if($patient_DOB){
            $tempYear = (substr($patient_DOB,0,2));
            if($tempYear < 40)
           		echo("<input type=\"text\" size = \"6\" maxLength = \"4\" name=\"patient_birth_year\" value=\"20$tempYear\"/><br />");
            else
            	echo("<input type=\"text\" size = \"6\" maxLength = \"4\" name=\"patient_birth_year\" value=\"19$tempYear\"/><br />");
            
        }
        else echo("<input type=\"text\" size = \"6\" maxLength = \"4\" name=\"patient_birth_year\" value=\"".date('Y')."\"/><br />");
    ?>
 Gender:  
<?php  
		echo '<select name = "patient_gender"> <option value = " "> </option>';
		 
		 if($patient_gender == "Male") echo '<option value = "Male" selected>Male</option>';
		 else 									echo '<option value = "Male">Male</option>';
		 
		 if($patient_gender == "Female") echo '<option value = "Female" selected>Female</option>';
		 else 									echo '<option value = "Female">Female</option>';
		 echo '</select>';
?>
<br />

 Additional patients:    <input type="text" name="patient_name1" value="<?php echo($tempBooking->getith_patient(1))?>"/>
<input type="text" name="patient_name2" value= "<?php echo($tempBooking->getith_patient(2))?>"/>
    
					
<br><span style='font-size:x-small;color:FF0000'>* </span>Hospital <select name="hospital">
			<?php 
$hospitals = array("", "Maine Medical Center", "Mercy Hospital", "Specialist", "Spring Harbor Hospital", "Other");	
			foreach($hospitals as $hospital){
				echo ("<option value='");
				echo ($hospital. "' ");
				if ($tempBooking->get_hospital() == $hospital || $id == "new" && $hospital == "" || $last_hospital == $hospital)
				    echo "SELECTED";
				echo (">".$hospital."</option>");
			}
			?>
</select>
<br><span style='font-size:x-small;color:FF0000'>* </span>Department of Treatment  
<select name="dept">
			<?php 
$departments = array("BBCH", "CARDIOLOGY", "MCCP", "NEEDS", "NICU/CCN", "PRENATAL", "SCU", "SPRING HARBOR", "Other", "Outpatient");			
			foreach($departments as $department){
				echo ("<option value='");
				echo ($department. "' ");
				if ($tempBooking->get_department() == $department  || $last_department == $department)
				    echo "SELECTED";
				echo (">".$department."</option>");
			}
			if (!in_array($tempBooking->get_department(),$departments)){
			    echo ("<option value='");
				echo ($tempBooking->get_department(). "' SELECTED");
				echo (">".$tempBooking->get_department()."</option>");
			}   
			?>
</select><br>

<span style='font-size:x-small;color:FF0000'>* </span>Submitted by (Social Worker name/phone): <input type="text" size="35" name="referred_by" value="
<?php 
echo($tempBooking->get_referred_by());
?>"/>
<br><span style='font-size:x-small;color:FF0000'>* </span>Date Submitted:    
    <?php 
    if ($id=="new") {
      echo '<select name = "date_submitted_month">';
      $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	  echo("<option selected='yes'> </option>");
      for ($i = 1 ; $i <= 9 ; $i ++){
 		echo ("<option value='0".$i."'>".$months[$i-1]."</option>");
      }
      for ($i = 10 ; $i <= 12 ; $i ++){
 		echo ("<option value=".$i.">".$months[$i-1]."</option>");
      } 
      echo '</select><select name = "date_submitted_day">';
      echo("<option selected='yes'>  </option>");
      for ($i = 1; $i <= 9 ; $i ++){
  		echo ("<option value='0".$i."'>".$i."</option>");
      }
      for ($i = 10; $i <= 31 ; $i ++){
  		echo ("<option>".$i."</option>");
      }
      echo '</select><input type="text" size = "6" maxLength = "4" name="date_submitted_year" value="20'.substr($tempBooking->get_date_submitted(),0,2).'"/><br />';
    }
    else {
      echo date_string($tempBooking->get_date_submitted());
      echo "<br><br><b>Status: </b>";
      if (substr($tempBooking->get_status(),0,6)!="closed") {
         echo $tempBooking->get_status();
         if ($tempBooking->get_status()!="pending") {
          echo '<br>Room No: '.$tempBooking->get_room_no();
          $rooms = retrieve_all_rooms(date('y-m-d'));
          if ($tempBooking->get_linked_room()!="") {
          	echo ", ".$tempBooking->get_linked_room();
          	echo ' (Remove the second room for this family? ';
          	echo '<select name="linked_room">';
          	echo "<option value=''></option>";
          	echo "<option value='remove'>yes</option>";
          	echo "</select>)";
          }
          else {
            echo ' (Add a second room for this family? ';
            echo '<select name="linked_room">';
            echo "<option value=''></option>";
            foreach ($rooms as $room) {
            	$room_no = substr($room,0,3);
            	$booking_id = substr($room,4);
            	if ($room_no!=$tempBooking->get_room_no() && $room_no!=$tempBooking->get_linked_room() && $booking_id=="")
            		echo "<option value=".$room.">".$room_no."</option>";
            }
            echo "</select>)";
          }
          echo '<br>  (Move family to a different room? ';
          echo '<select name="room_no">'; 
          echo "<option value=''></option>";
          foreach ($rooms as $room) {
           $room_no = substr($room,0,3);
           $booking_id = substr($room,4);
           if ($room_no!=$tempBooking->get_room_no() && $room_no!=$tempBooking->get_linked_room() && $booking_id=="")
              echo "<option value=".$room.">".$room_no."</option>";
          }
          echo "</select>)"; 
         } 
      }
      else {
      	echo '<select name="status">';
      	$status_options = array("closed","closed-deceased","active");
      	echo("<option></option>");
      	foreach ($status_options as $status_option)
        	if ($status_option==$tempBooking->get_status())
          		echo "<option value=".$status_option." selected='yes'>".$status_option."</option>";
        	else echo "<option value=".$status_option.">".$status_option."</option>";
      	echo "</select>";
      	echo "&nbsp;&nbsp;Check-out Date: <select name = 'date_out_month'>";
      	$months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	  	echo("<option> </option>");
      	for ($i = 1 ; $i <= 9 ; $i ++){
          if('0'.$i == substr($tempBooking->get_date_out(),3,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$months[$i-1]."</option>");
          else
             echo ("<option value='0".$i."'>".$months[$i-1]."</option>");
      	}
      	for ($i = 10 ; $i <= 12 ; $i ++){
          if($i == substr($tempBooking->get_date_out(),3,2))
             echo ("<option value=".$i." selected = 'yes' >".$months[$i-1]."</option>");
          else
             echo ("<option value=".$i.">".$months[$i-1]."</option>");
      	}
      	echo '</select><select name = "date_out_day">';
 
      	echo("<option>  </option>");
      	for ($i = 1; $i <= 9 ; $i ++){
          if($i == substr($tempBooking->get_date_out(),6,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$i."</option>");
          else
             echo ("<option value='0".$i."'>".$i."</option>");
      	}
      	for ($i = 10; $i <= 31 ; $i ++){
          if($i == substr($tempBooking->get_date_out(),6,2))
             echo ("<option selected = 'yes'>".$i."</option>");
          else
             echo ("<option>".$i."</option>");
      	}
      	echo '</select>
    		<input type="text" size = "6" maxLength = "4" name="date_out_year" 
    		value="20'.substr($tempBooking->get_date_out(),0,2).'"/>';
      }
    } 
?>

<br /><br />
<b>Manager's Notes/Special Requests:</b><br />
	<font color ="red">
  	<textarea rows = "3" cols = "80" name = "notes" ><?php echo $tempBooking->get_mgr_notes()?></textarea>
    </font>
<br/>
<br />
<fieldset>
  <legend><b>Primary Guest:</b></legend>
<?php    if ($id!="new") {
    echo '<a  target="_blank" href="personEdit.php?id='.$guest->get_id().'">'.$guest->get_first_name().' '.$guest->get_last_name().'</a><br>';
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
    echo 'Relation to Patient: <input type="text" size="20" name="patient_relation" value='.$guest->get_patient_relation().'><br />' ;
    echo 'Gender: '.$guest->get_gender().'<br />';
    echo 'Address: '.$guest->get_address().', '. $guest->get_city().', '.$guest->get_state().' '.$guest->get_zip(). '<br />';
    echo 'E-mail: '.$guest->get_email().'<br />';
}
    else {
        echo '<span style="font-size:x-small;color:FF0000">* </span>First Name: <input type="text" size="30" name="first_name_1" value='.$guest->get_first_name().'><br />' ;
        echo '<span style="font-size:x-small;color:FF0000">* </span>Last Name: <input type="text" size="30" name="last_name_1" value='.$guest->get_last_name().'><br />' ;
        $phone1 = $guest->get_phone1();
        $phone2 = $guest->get_phone2();
		echo '<span style="font-size:x-small;color:FF0000">* </span>Phone: (<input type = "text" size = "3" maxLength = "3" name="phone1_area_1" value="'.substr($phone1,0,3).'" onkeyup="moveOnMax(this,\'input2\')">)';
		echo '<input id="input2" type = "text" size = "3" maxLength = "3" name="phone1_middle_1" value="'.substr($phone1,3,3).'" onkeyup="moveOnMax(this,\'input3\')">-';
		echo '<input id="input3" type = "text" size = "4" maxLength = "4" name="phone1_end_1" value="'.substr($phone1,6,4).'">';
 		echo '&nbsp;&nbsp;Alternate Phone: (<input type = "text" size = "3" maxLength = "3" name="phone2_area_1" value="'.substr($phone2,0,3).'">)';
 		echo '<input type = "text" size = "3" maxLength = "3" name="phone2_middle_1" value='.substr($phone2,3,3).'>-';
 		echo '<input type = "text" size = "4" maxLength = "4" name="phone2_end_1" value='.substr($phone2,6,4).'><br />';
        echo 'Relation to Patient: <input type="text" size="20" name="patient_relation" value='.$guest->get_patient_relation().'><br />' ;
        echo 'Gender: <select name = "gender_1"> <option value = " "> </option>';	 
		 if($guest_gender == "Male") echo '<option value = "Male" selected>Male</option>';
		 else 						echo '<option value = "Male">Male</option>'; 
		 if($guest_gender == "Female") echo '<option value = "Female" selected>Female</option>';
		 else 						echo '<option value = "Female">Female</option>';
		 echo '</select><br />';
//		echo '<span style="font-size:x-small;color:FF0000">* </span>Relationship to Patient: <input type="text" size = "30" name = "patient_gender_1" value='.$guest->get_patient_gender().'><br />' ;
        echo 'Home Address: <input type="text" size="40" name = "address_1" value='.$guest->get_address().'><br />' ;
        echo 'City (or Country if outside US): <input type = "text" size = "20" name = "city_1" value='.$guest->get_city().'><br />' ;
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
    }
 ?> 
</fieldset>

<br>
<?php
  if ($id=="new") echo "<b>Additional </b>";
  echo  "<b>Occupants </b>(Name / Relation to Patient / Gender):<br><br />";
  if(empty($last_occupants)) $occupants = $tempBooking->get_occupants();
  else $occupants = $last_occupants;
  for ($counter = 1; $counter <= 6; $counter++){
  	  //Before getting information from the occupants array we need to sanitize it
  	  //So we find if it has the appropiate number of colons (:) and act accordingly (add them if needed, move on if not)
  	  $numberOfColons = substr_count($occupants[$counter-1], ":");
  	  if($numberOfColons < 3 )
  	  {
  	  	for($i = $numberOfColons; $i < 3; $i++)
  	  	{
  	  		$occupants[$counter-1] .= ":";
  	  	}
  	  }
      $startingColonPosition = strpos($occupants[$counter-1],":");
      $secondColonPosition = strpos($occupants[$counter-1],":", $startingColonPosition+1);
      $thirdColonPosition = strpos($occupants[$counter-1],":", $secondColonPosition+1);
      
      $lengthOfRelation = $secondColonPosition - $startingColonPosition - 1;
      $lengthOfGender = $thirdColonPosition - $secondColonPosition - 1;
       
      $occupant_name = substr($occupants[$counter-1],0,$startingColonPosition);
      $occupant_relation = substr($occupants[$counter-1],$startingColonPosition+1,$lengthOfRelation);
      $occupant_gender = substr($occupants[$counter-1], $secondColonPosition+1, $lengthOfGender);
      $occupant_present = substr($occupants[$counter-1], $thirdColonPosition+1);
      
     echo
		'<input type="text" size="25" name = "additional_guest_'.($counter).'" value = "'.$occupant_name.'"/>' . ' / '.
		'<input type="text" size="15" name = "additional_guest_'.($counter).'_relation" value = "'.$occupant_relation.'"/>' . ' / ' .
		'<select name= "additional_guest_'.($counter).'_gender"> 
     	<option value = ""> </option>';
     	if($occupant_gender == 'Male')	echo '<option value = "Male" selected>Male</option>';
     	else 							echo '<option value = "Male">Male</option>';
     	if($occupant_gender == 'Female')	echo '<option value = "Female" selected>Female</option>';
     	else 								echo '<option value = "Female">Female</option>';
     	echo '</select>';
     //	echo "occupant_present = ".$occupant_present;
     	if($occupant_present == ""||$occupant_present == "Absent") 
     		echo '<input type="checkbox" name="additional_guest_'.($counter).'_present" value="Present">Present<br>';
     	else 
     		echo '<input type="checkbox" name="additional_guest_'.($counter).'_present" value="Present" checked>Present<br>';
		echo '<br />';
  }
?>
<br />

<b>Automobile </b>: <br>  Make <input type="text" size="15" name="auto_make" value= "
<?php 
if($tempBooking->get_auto_make() != "") echo($tempBooking->get_auto_make()); 
else if($last_auto_make != "") echo($last_auto_make); 
?>
"/>
	Model <input type="text" size="10" name="auto_model" value= "
<?php 
if($tempBooking->get_auto_model() != "") echo($tempBooking->get_auto_model());
else if($last_auto_model != "") echo($last_auto_model); 
?>
"/>
	Color <input type="text" size="10" name="auto_color" value= "
<?php 
if($tempBooking->get_auto_color() != "") echo($tempBooking->get_auto_color());
else if($last_auto_color != "") echo($last_auto_color);
?>
"/>
	State <select name = "auto_state">
  <?php
  $states = array(" ", "AL","AK","AR","AZ","CA","CO","CT","DC","DE","FL","GA","HI","IA","ID","IL","IN",
				"KS","KY","LA","MA","MD","ME","MI","MN","MO", "MS","MT","NC","ND","NE","NH","NJ","NM",
				"NV","NY","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VA","VT","WA","WI",
				"WV","WY");
			foreach ($states as $st) {
			    if ($st=="ME" && ("" == $tempBooking->get_auto_state() || $last_auto_state == ""))
			       echo "<option selected='yes'>" . $st ."</option>"; 
			    else if ($st == $tempBooking->get_auto_state() || $last_auto_state == $st)
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
       <input type="text" name="payment_description" size="80" value = "<?php if($tempBooking->get_payment_arrangement()!= "10 per night") echo($tempBooking->get_payment_arrangement())?>"/> <br />
<br />

<br><b>Confirmation: </b> <br />
 <input type="radio" name="visitOrWC" value="visit" <?php if($tempBooking->get_date_in() != "Will Call") echo('checked')?>/> 
 Check-in Date
   
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
    
    <input type="text" size = "6" maxLength = "4" name="date_in_year" value= "<?php 
    if ($tempBooking->get_date_in() != "Will Call") 
    if(substr($date_in,0,2) < 40)
    echo ("20".substr($date_in,0,2));
    else
    echo ("19".substr($date_in,0,2));
    ?>"/> <br />
    
    
 <input type="radio" name="visitOrWC" value="Will Call" <?php if($tempBooking->get_date_in()== "Will Call") echo('checked')?>/> Will Call 

<br />
<br />
<b>Approved Use(s): </b> <br />

 <input type="checkbox" name="overnight" value="yes" <?php if($tempBooking->overnight_use() == "yes") echo('checked')?>/> Overnight<br />
 <input type="checkbox" onClick="add()" name="day" value="yes" <?php if($tempBooking->day_use() == "yes") echo('checked' )?>/> Day <br />
 
 <div name = "day_use_input" style = "visibility:hidden" >For Date: </div> 
 <select name = "day_use_month" style="visibility:hidden">
    <?php 
    $day_use_date = $tempBooking->get_day_use_date();
    if ($day_use_date=="")
    	$day_use_date = date("y-m-d");
      $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	  echo("<option> </option>");
      for ($i = 1 ; $i <= 9 ; $i ++){
      	if($i == substr($day_use_date,3,2))
             echo ("<option value='0".$i."' selected = 'yes'>".$months[$i-1]."</option>");
        else 
             echo ("<option value='0".$i."'>".$months[$i-1]."</option>");
      }
      for ($i = 10 ; $i <= 12 ; $i ++){
      if($i == substr($day_use_date,3,2))
            echo ("<option value=".$i." selected = 'yes'>".$months[$i-1]."</option>");
      else 
          	echo ("<option value=".$i.">".$months[$i-1]."</option>");
      }
    ?>
    </select>
     
    <select name = "day_use_day" style="visibility:hidden">
    <?php 
      echo("<option>  </option>");
      for ($i = 1; $i <= 9 ; $i ++){
      	if($i == substr($day_use_date,6,2))
             echo ("<option value='0".$i."' selected = 'yes' >".$i."</option>");
        else
             echo ("<option value='0".$i."'>".$i."</option>");
      }
      for ($i = 10; $i <= 31 ; $i ++){
      	if($i == substr($day_use_date,6,2))
         	echo ("<option value=".$i." selected = 'yes'>".$i."</option>");
        else 
             echo ("<option value=".$i.">".$i."</option>");
      }
      $tempYear = (substr($day_use_date,0,2));
      echo("<input type=\"text\" size = \"6\" maxLength = \"4\" name=\"day_use_year\" value= \"20$tempYear\"/ style=\"visibility:hidden\"><br />");
    ?>
    </select>

<script>
function add(){

	var currentDate = new Date();
	title = document.getElementsByName("day_use_input");
	checkbox = document.getElementsByName("day");
	
	if(checkbox[0].checked)
		title[0].style.visibility = "visible";
	else
		title[0].style.visibility = "hidden";	

		
	month = document.getElementsByName("day_use_month");
	if(checkbox[0].checked){
		month[0].style.visibility = "visible";
		if(month[0].value == "")
		month[0].value = currentDate.getMonth() +1;
	}
	else
		month[0].style.visibility = "hidden";
	
	
	day = document.getElementsByName("day_use_day");
	if(checkbox[0].checked){
		day[0].style.visibility = "visible";

		if(currentDate.getDate()<10)
		{
			if(day[0].value == "")
			day[0].value = "0"+currentDate.getDate();
		}
		else
		{
			if(day[0].value == "")
			day[0].value = currentDate.getDate();
		}
	}
	else 
		day[0].style.visibility = "hidden";
	
	
	year = document.getElementsByName("day_use_year");
	if(checkbox[0].checked){
		year[0].style.visibility = "visible";
		if(year[0].value == "")
		year[0].value = currentDate.getFullYear();
	}
	else
		year[0].style.visibility = "hidden";
	
}
</script>
	
    <script>
    add(); 
    </script>
 
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
