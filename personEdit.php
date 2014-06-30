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
 *	personEdit.php
 *  oversees the editing of a person to be added, changed, or deleted from the database
 *	@author Oliver Radwan and Allen Tucker
 *	@version 9/1/2008
 */
	session_start();
	session_cache_expire(30);
    include_once('database/dbPersons.php');
    include_once('domain/Person.php');
    include_once('database/dbBookings.php');
    include_once('database/dbLog.php');
	$id = $_GET["id"];
	if ($id=='new') {
	 	     $person = new Person('person','new',null,null,null,null,null,null,null,null,null,null,null,'new',null,null,null,md5('new'));
	}
	else {
		$person = retrieve_dbPersons($id);
		if (!person) {
             echo('<p id="error">Error: there\'s no person with this id in the database</p>'. $id);
		     die();
        }
	}
?>
<html>
	<head>
		<title>
			Editing <?PHP echo($person->get_first_name()." ".$person->get_last_name());?>
		</title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
	</head>
<body>
  <div id="container">
    <?PHP include('header.php');?>
	<div id="content">
<?PHP
	include('personValidate.inc');
	if($_POST['_form_submit']!=1)
	//in this case, the form has not been submitted, so show it
		include('personForm.inc');
	else {
	//in this case, the form has been submitted, so validate it
		$errors = validate_form($id); 	//step one is validation.
									// errors array lists problems on the form submitted
		if ($errors) {
		// display the errors and the form to fix
			show_errors($errors);
		}
		// this was a successful form submission; update the database and exit
		else{
		    $newperson = process_form($id,$person);
			if ($_POST['deleteMe']!="DELETE" && $_POST['reset_pass']!="RESET")
			   echo('Update successful.  Click <a href=personEdit.php?id='.$newperson->get_id().'> edit</a> to review your changes.');
		}
		echo('</div>');
		include('footer.inc');
		echo('</div></body></html>');
		die();
	}
echo('</div>');
include('footer.inc');
echo('</div></body></html>');
/**
* process_form sanitizes data, concatenates needed data, and enters it all into the database
*/
function process_form($id,$person)	{	
    		    
     // Get the info of the user who is making the update
	 $user = retrieve_dbPersons($_SESSION['_id']);
	 $name = $user->get_first_name()." ".$user->get_last_name();
	 if ($id=='new')
	    $first_name = trim(str_replace("'","\'", htmlentities(str_replace('&','and',$_POST['first_name']))));
	 else $first_name = $person->get_first_name();
	
		$last_name = trim(str_replace("'","\'", htmlentities($_POST['last_name'])));
		$gender = $_POST['gender'];
		$employer = trim(str_replace("'","\'", htmlentities($_POST['employer'])));
		$address = trim(str_replace("'","\'", htmlentities($_POST['address'])));
		$city = trim(str_replace("'","\'", htmlentities($_POST['city'])));
		$state = $_POST['state'];
		$zip = trim(htmlentities($_POST['zip']));
	    
	    $phone1 = $_POST['phone1_area_1'].$_POST['phone1_middle_1'].$_POST['phone1_end_1'];
		$phone2 = $_POST['phone2_area_1'].$_POST['phone2_middle_1'].$_POST['phone2_end_1'];
        $clean_phone1 = preg_replace("/[^0-9]/", "", $phone1);
		$clean_phone2 = preg_replace("/[^0-9]/", "", $phone2);
		$email = trim(str_replace("'","\'", htmlentities($_POST['email'])));
		$mgr_notes = trim(str_replace('\\\'','\'',htmlentities($_POST['mgr_notes'])));
        /*$patient_name = array(trim(str_replace("'","\'", htmlentities($_POST['patient_name0']))));
        if ($_POST['patient_name1']) {
            $patient_name[] = trim(str_replace("'","\'", htmlentities($_POST['patient_name1'])));
        }
        if ($_POST['patient_name2']) {
            $patient_name[] = trim(str_replace("'","\'", htmlentities($_POST['patient_name2'])));
        }
	    $patient_birthdate = $_POST['DateOfBirth_Year'].'-'.$_POST['DateOfBirth_Month'].'-'.$_POST['DateOfBirth_Day'];
        $patient_gender = trim(str_replace('\\\'','\'',htmlentities($_POST['patient_gender'])));
      	*/
        $type = implode(',',$_POST['type']);
        $prior_bookings = implode(',',$person->get_prior_bookings());
		$newperson = new Person($last_name, $first_name, $gender, $employer, $address, $city, $state, $zip, $clean_phone1, $clean_phone2, 
                                   $email, $type, $prior_bookings, implode(',',$person->get_patient_name()), 
                                   $person->get_patient_birthdate(),$person->get_patient_gender(),$person->get_patient_relation(),"");
        $newperson->set_mgr_notes($mgr_notes);   
        if(!retrieve_dbPersons($newperson->get_id())){
           insert_dbPersons($newperson);
           return $newperson;
        }
        else if($_POST['deleteMe']!="DELETE" && $_POST['reset_pass']!="RESET"){
         	update_dbPersons($newperson);
         	return $newperson;
        }
        
	//step two: try to make the deletion or password change
		if($_POST['deleteMe']=="DELETE"){
			$result = retrieve_dbPersons($id);
			if (!$result)
				echo('<p>Unable to delete. ' .$first_name.' '.$last_name. ' is not in the database. <br>Please report this error to the House Manager.');
			else {
				//What if they're the last remaining manager account?
				if(strpos($type,'manager')!==false){
				//They're a manager, we need to check that they can be deleted
					$managers = getall_type('manager');
					if (!$managers || count($managers) <= 1)
						echo('<p class="error">You cannot remove the last remaining manager from the database.</p>');
					else {
						$result = delete_dbPersons($id);
						echo("<p>You have successfully removed " .$first_name." ".$last_name. " from the database.</p>");
						if($id==$_SESSION['_id']){
							session_unset();
							session_destroy();
						}
					}
				}
				else {
					$result = delete_dbPersons($id);
					echo("<p>You have successfully removed " .$first_name." ".$last_name. " from the database.</p>");
					if($id==$_SESSION['_id']){
						session_unset();
						session_destroy();
					}
				}
				// Create the log message
				$message = "<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
				" has removed ".$first_name." ".$last_name." from the database";
				add_log_entry($message);
			}
			return $person;
		}
		// try to reset the person's password
		else if($_POST['reset_pass']=="RESET"){
			$id = $_POST['old_id'];
			// $result = delete_dbPersons($id);
			// $pass = $first_name . $phone1;
            $person = new Person($last_name, $first_name, $gender, $employer, $address, $city, $state, $zip, $clean_phone1, $clean_phone2, 
                               $email, $type, implode(',',$person->get_prior_bookings()), implode(',',$person->get_patient_name()), $patient_birthdate,$patient_gender,$patient_relation,"");
            $result = insert_dbPersons($person);
			if (!$result)
            	echo ('<p class="error">Unable to reset ' .$first_name.' '.$last_name. "'s password.. <br>Please report this error to the House Manager.");
			else {
				echo("<p>You have successfully reset " .$first_name." ".$last_name. "'s password.</p>");
				// Create the log message
				$message = "<a href='viewPerson.php?id=".$_SESSION['_id']."'>".$name."</a>".
				" has reset the password for <a href='viewPerson.php?id=".$id."'>".
				$first_name." ".$last_name."</a>";
				add_log_entry($message);
			}
			return $person;
		}
}
?>

