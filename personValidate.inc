<?PHP
/**
 * validate_form validates the form data, checking all legality.
 * it returns an array of the errors with the form (string warnings)
 * this array is null (aka false) if there are no errors
 */
function validate_form($id){
  if ($id=='new')
	if($_POST['first_name']==null || $_POST['first_name']=='new') $errors[] = 'Please enter a first name';
	if($_POST['last_name']==null || $_POST['first_name']=='volunteer') $errors[] = 'Please enter a last name';
	if($_POST['address']==null) $errors[] = 'Please enter an address';
	if($_POST['city']==null) $errors[] = 'Please enter a city';
	if($_POST['zip']!=null && ($_POST['zip'] != strval(intval($_POST['zip'])) || (strlen($_POST['zip'])!=5))) $errors[] = 'Please enter a valid zip code';
	if ($_SESSION['access_level']==0 && ($_POST['type']==null || !in_array('guest',$_POST['type'])))
		$_POST['type'][] = 'guest';
	if($_POST['type']==null && $_SESSION['access_level']>=1) $errors[] = 'Select a role';
	if ($_POST['type']!=null && $_SESSION['access_level']<=1 && in_array('manager',$_POST['type']))
		$errors[] = "Sorry, you can't promote yourself to manager.";
 
    $phone1 = $_POST['phone1_area_1'].$_POST['phone1_middle_1'].$_POST['phone1_end_1'];
    $phone2 = $_POST['phone2_area_2'].$_POST['phone2_middle_2'].$_POST['phone2_end_2'];
    
	    if(!valid_phone($phone1)) $errors[] = 'Enter a valid primary phone number (10 digits: ### ### ####)';
	if($phone2!=null && !valid_phone($phone2)) $errors[] = 'Enter a valid secondary phone number (10 digits: ### ### ####)';
	if(!valid_email($_POST['email']) && $_POST['email']!=null) $errors[] = "Please enter a valid email";
	
//	if ($_POST['patient_name0']==null) $errors[] = 'Enter a patient name';
//	$birthday = $_POST['DateOfBirth_Month'].'-'.$_POST['DateOfBirth_Day'].'-'.$_POST['DateOfBirth_Year'];
//	if (strlen($birthday)>2 && strlen($birthday)<8) $errors[] = 'Please select a valid patient birthday';
	
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