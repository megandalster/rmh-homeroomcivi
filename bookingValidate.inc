<?php
/**
 * validate_form validates the form data, checking all legality. 
 * it returns an array of the errors with the form (string warnings)
 * this array is null (aka false) if there are no errors
 */
function validate_form($id,$referralid){
    if ($_POST['patient_name0'] == null) $errors[] = "Please enter a name for the patient";
    if ($_POST['hospital']== null) $errors[]= "Please enter a hospital name";
    if ($_POST['dept']== null) $errors[]= "Please enter a department";
    if ($_POST['patient_birth_year'] == null || strlen($_POST['patient_birth_year']) != 4 
	    || $_POST['patient_birth_month'] == null || $_POST['patient_birth_day'] == null) $errors[] = 'Please enter a valid date of birth for the patient';
	if($_POST['referred_by'] == null) $errors[] = "Please name the person who submitted this booking";
    if (strlen($_POST['room_no'])>4 && substr($_POST['room_no'],4)!=$referralid)  // check if somebody else is not in the room selected
	    $errors[] = 'Please select an unoccupied Room';   
	if($_POST['visitOrWC']=='visit'){
	    if($_POST['date_in_day']== "" || $_POST['date_in_month']== "" || $_POST['date_in_year']== ""){
	        $errors[] = 'Please enter a valid date of arrival. If this date is unknown, please select "Will Call" instead.';
	    }
	}
	    
	if($_POST['day']=='yes'){
	    if($_POST['day_use_day']== "" || $_POST['day_use_month']== "" || $_POST['day_use_year']== "" || strlen($_POST['day_use_year']) != 4){
	        $errors[] = 'Please enter a valid day use date. If this booking is not day use, please uncheck day use';
	    }   
	}
	if ($id=='new') {
	   if($_POST['date_submitted_day']== "" || $_POST['date_submitted_month']== "" || $_POST['date_submitted_year']== "")
			$errors[] = 'Please enter a Date Submitted';
  	   if(trim($_POST['first_name_1'])==null) $errors[] = 'Please enter a first name for the primary guest ';
	   if($_POST['last_name_1']==null || $_POST['first_name']=='volunteer') $errors[] = 'Please enter a last name for the primary guest ';   
  //     if($_POST['patient_gender_1'] == "") $errors[] = 'Please enter a patient gender ';
       
	   $phone1 = $_POST['phone1_area_1'].$_POST['phone1_middle_1'].$_POST['phone1_end_1'];
	   $phone2 = $_POST['phone2_area_1'].$_POST['phone2_middle_1'].$_POST['phone2_end_1'];
       if(!valid_phone($phone1)) $errors[] = 'Please enter a valid primary phone number for the primary guest (10 digits: ### ### ####)';
	   if($phone2!=null && !valid_phone($phone2)) $errors[] = 'Please enter a valid secondary phone number (Optional, 10 digits: ### ### ####)';
	   if(!valid_email($_POST['email_1']) && $_POST['email_1']!=null) $errors[] = "Please enter a valid email";
	}          
	return $errors;
}


/**
* valid_phone validates a phone on the following parameters:
* 		it assumes the characters '-' ' ' '+' '(' and ')' are valid, but ignores them
*		every other digit must be a number
*		it should be between 7 and 11 digits
* returns boolean if phone is valid
*/
function valid_phone($phone){
		if($phone==null) return false;
		$phone = str_replace(' ','',str_replace('+','',str_replace('(','',str_replace(')','',str_replace('-','',$phone)))));
		$test = str_replace('0','',str_replace('1','',str_replace('2','',str_replace('3','',str_replace('4','',str_replace('5','',str_replace('6','',str_replace('7','',str_replace('8','',str_replace('9','',$phone))))))))));
		if($test != null) return false;
		if ( (strlen($phone)) != 10) return false;
		return true;
}

//Function from <http://www.phpit.net/code/valid-email/>
function valid_email($email) {
		// First, we check that there's one @ symbol, and that the lengths are right
		if (!preg_match("~^[^@]{1,64}@[^@]{1,255}$~", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!preg_match("~^(([A-Za-z0-9!#$%&#038;'*+/=?^_`{|}\~-][A-Za-z0-9!#$%&#038;'*+/=?^_`{|}\~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$~", $local_array[$i])) {
				return false;
			}
		}
		if (!preg_match("~^\[?[0-9\.]+\]?$~", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!preg_match("~^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$~", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
}

function show_errors($e){
		//this function should display all of our errors.
		echo('<div class="warning">');
		echo('<ul>');
		foreach($e as $error){
			echo("<li><strong><font color=\"red\">".$error."</font></strong></li>\n");
		}
		echo("</ul></div></p>");
		echo "Hit the Back button to fix these errors.";
}
?>