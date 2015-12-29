<?PHP
/*
 * Copyright 2008 by Oliver Radwan, Maxwell Palmer, Nolan McNair,
 * Taylor Talmage, and Allen Tucker.  This program is part of RMH Homebase.
 * RMH Homebase is free software.  It comes with absolutely no warranty.
 * You can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
*/
/*
 *	personForm.inc
 *  shows a form for a person to be added or edited in the database
 *	@author Oliver Radwan and Allen Tucker
 *	@version 9/1/2008
 */


if($_SESSION['access_level']<1) {  
        echo("<p id=\"error\">You do not have sufficient permissions to add a person to the database.</p>");
	    ('footer.inc');
		echo('</div></div></body></html>');
		die();
	}

if ($id=='new') {
	  echo('<strong>New Person Input Form</strong><br />');
	  echo('Here you can add a new person to the database.</p>');
}
else {
	   echo('<strong>Person Edit Form</strong><br />');
	   echo('Here you can edit (or delete) a person in the database.</p><p>');
}

?>
<form method="POST"><fieldset>
<legend> (<span style="font-size:x-small;color:FF0000">*</span> denotes required fields) </legend>
	<input type="hidden" name="old_id" value=<?PHP echo("\"".$id."\"");?>>
	<input type="hidden" name="old_pass" value=<?PHP echo("\"".$person->get_password()."\"");?>>
	<input type="hidden" name="_form_submit" value="1">
	<p>
	<table border="0px">
		<?php 
  echo "<tr><td><span style='font-size:x-small;color:FF0000'>* </span>First Name:";
    if ($id=='new')
        echo ':</td><td> 
        	<input type="text" name="first_name" tabindex=1 value="'.$person->get_first_name().'"></td></tr>';
    else echo '</td><td>'.$person->get_first_name().'</td></tr>';
 ?>
		<tr><td><span style='font-size:x-small;color:FF0000'>* </span>Last Name:</td><td> <input type="text" name="last_name" tabindex=2 value="<?PHP echo($person->get_last_name())?>"></td></tr>
		
		<tr><td>Gender:</td>
		<td><select name="gender" tabindex=3> 
		<?php
		echo '<option value = " "> </option>'; 
		if($person->get_gender() == "Male") echo '<option value = "Male" selected>Male</option>';
		else 									echo '<option value = "Male">Male</option>';
		 
		if($person->get_gender() == "Female") echo '<option value = "Female" selected>Female</option>';
		else 									echo '<option value = "Female">Female</option>';
		?>
		</select></td></tr>
		
		<tr><td><span style='font-size:x-small;color:FF0000'> </span>Employer:</td><td> <input type="text" name="employer" tabindex=3 value="<?PHP echo($person->get_employer())?>"></td></tr>
		<tr><td><span style='font-size:x-small;color:FF0000'>* </span>Address:</td><td> <input type="text" name="address" tabindex=4 value="<?PHP echo($person->get_address())?>"></td></tr>
		<tr><td><span style='font-size:x-small;color:FF0000'>* </span>City:</td><td> <input type="text" name="city" tabindex=5 value="<?PHP echo($person->get_city())?>"></td></tr>
		<tr><td>State, Zip:</td>
		<td><select name="state" tabindex=6>
		<?PHP

			$states = array("AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA",
					        "KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM",
					        "NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VT","VA","WA",
					        "WV","WI","WY", "other");
			foreach ($states as $st) {
				echo "<option value='" . $st . "' ";
                if($person->get_state() == $st ) echo("SELECTED");
                else if ($id == "new" && $st =="ME") echo("SELECTED");
                echo ">" . $st . "</option>";
			}
			
		?>
		</select>,
		<input type="text" name="zip" size="5" tabindex=6 value=<?PHP echo $person->get_zip()?>>
		</td></tr>
		<?php if ($id!="new" && $person->get_state()=="ME") {
		    echo "<tr><td>Maine County:</td><td>";
			echo $person->get_county().'</td></tr>';
		}
		?>
		<?php 
  echo "<tr><td><span style='font-size:x-small;color:FF0000'>* </span>Primary Phone:";
   // if ($id=='new')
     //   echo '</td><td> 
       // 	<input type="text" name="phone1" MAXLENGTH=12 tabindex=7 value="'.$person->get_phone1().'"></td></tr>';
    //else echo ":</td><td>(".substr($person->get_phone1(),0,3).")".substr($person->get_phone1(),3,3)."-".substr($person->get_phone1(),6).'</td></tr>';
 ?> 	
         
        <?php
        echo '</td><td>';
        $phone1 = $person->get_phone1();
        $phone2 = $person->get_phone2();
        
        
		echo '<span style="font-size:x-small;color:FF0000"> </span> (<input type = "text" size = "3" maxLength = "3" name="phone1_area_1" value="'.substr($phone1,0,3).'" onkeyup="moveOnMax(this,\'input2\')">)';
		echo '<input id="input2" type = "text" size = "3" maxLength = "3" name="phone1_middle_1" value="'.substr($phone1,3,3).'" onkeyup="moveOnMax(this,\'input3\')">-';
		echo '<input id="input3" type = "text" size = "4" maxLength = "4" name="phone1_end_1" value="'.substr($phone1,6,4).'"></td></tr>';
		
 echo "<tr><td>";
 echo 'Alternate Phone:</td><td> (<input type = "text" size = "3" maxLength = "3" name="phone2_area_1" value="'.substr($phone2,0,3).'" onkeyup="moveOnMax(this,\'input4\')">)';
 echo '<input id="input4" type = "text" size = "3" maxLength = "3" name="phone2_middle_1" value="'.substr($phone2,3,3).'" onkeyup="moveOnMax(this,\'input5\')">-';
 echo '<input id ="input5" type = "text" size = "4" maxLength = "4" name="phone2_end_1" value='.substr($phone2,6,4).'>';
 echo '</td></tr>'
?>
		
		<tr><td>Email:</td><td><input type="text" name="email" tabindex=9 value="<?PHP echo($person->get_email())?>"></td></tr>
       
	</table>
<?PHP
	if($_SESSION['access_level'] > 1) {
    $st = implode(',',$person->get_type());
    $newpersontypes = array('guest'=>'Primary Guest');
    $types = array('guest'=>'Primary Guest', 'volunteer'=>'Volunteer', 'manager'=>'Manager' );
    if ($_SESSION['access_level']==0) {
	     echo('<p>Please check "guest" if you are requesting a room at the House:');
	     $ts = $newpersontypes;
    }
	else {
		 echo("<p><span style='font-size:x-small;color:FF0000'>* </span>Role(s) for this person:");
		 $ts = $types;
	}
	echo('<br>');
    
	//$i = 0;
    foreach ($ts as $key=>$value) {
    	echo ('<input type="checkbox" name="type[]" value=' . $key);
    	if (strpos($st,$key)!==false) echo(' CHECKED');
    	echo ('>' . $value . '<br />');
    //	$i++;
    }
	}

    if ($id == 'new' || strpos(implode(',',$person->get_type()),'guest') !== false) {
    	//I commented this on 11/07/13, why would we want to echo that?
		//if($_SESSION['access_level']<2)
	  	  //  echo(' disabled="true"');
	  	
	  	if ($id !== 'new') {
	  	    echo('<p> Prior Bookings (date checked out): <br />');
	  	    foreach ($person->get_prior_bookings() as $prior_booking_id) {
	  	         $b = retrieve_dbBookings($prior_booking_id);
	  	         if (!$b)
	  	    	     $date = substr($prior_booking_id,0,8);
	  	    	 else $date = $b->get_date_out();
	  	    	 if (strlen($date)==8)
	  	         	echo '<a href="viewBookings.php?id=closed&bookingid='.$prior_booking_id.'" target="_blank">'.date_string($date).'</a> <br>';
	  	    }
	  	    echo "<br><a href=bookingEdit.php?id=".$id." target='_blank'>Create new booking</a>";
	  	}
	  	echo('<p><b>Manager\'s Notes:<br />');
	  	echo('<textarea name="mgr_notes" rows="3" cols="60" style="color:FF0000;font-weight:bold"');
	  	echo('>'. $person->get_mgr_notes() . '</textarea></b>');
	  	
    }

		echo('<input type="hidden" name="password" value="'.$person->get_password().'">');
		echo('<input type="hidden" name="prior_bookings" value="'.implode(',',$person->get_prior_bookings()).'">');
	    echo('<input type="hidden" name="_submit_check" value="1"><p>');
		echo('Hit <input type="submit" value="Submit" name="Submit Edits"> to submit these edits.<br /><br />');
		if ($id != 'new' && $_SESSION['access_level']==3) {
			echo '<input type="checkbox" name="deleteMe" value="DELETE"> Check this box and then hit ' .
				'<input type="submit" value="Delete" name="Delete Entry"> to delete this entry. <br />';
			if (!in_array('guest',$person->get_type()))
			    echo '<input type="checkbox" name="reset_pass" value="RESET"> Check this box and then hit ' .
					'<input type="submit" value="Reset Password" name="Reset Password"> to reset this user\'s password.</p>';
		}
		?>
</fieldset></form>
<script>
function moveOnMax(field,nextFieldID){
  if(field.value.length >= field.maxLength){
    document.getElementById(nextFieldID).focus();
  }
}
</script>