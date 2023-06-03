<?PHP
	echo('<form method="post"><p><strong>Search for People</strong>');
	echo('<table><tr><td>Primary Guest\'s First Name:</td><td><input type="text" name="s_first_name" value="'.$_POST['s_first_name'].'"></td></tr>');
	echo('<tr><td>Primary Guest\'s Last Name:</td><td><input type="text" name="s_last_name" value="'.$_POST['s_last_name'].'"></td></tr>');
	echo('<tr><td>Primary Guest\'s Phone:</td><td><input type="text" name="s_phone" value="'.$_POST['s_phone'].'"></td></tr>');
	echo('<tr><td>Patient\'s Name:</td><td><input type="text" name="s_patient_name" value="'.$_POST['s_patient_name'].'"></td></tr>');
    echo('<tr><td>Manager Notes:</td><td><input type="text" name="s_mgr_notes" value="'.$_POST['s_mgr_notes'].'"></td></tr>');
    echo('<tr><td>Type:</td><td><select name="s_type">' .
			'<option value=""></option>' .
			'<option value="guest" '.($_POST["s_type"]=="guest"?"selected":"").'>Guest</option>' .
			'<option value="volunteer" '.($_POST["s_type"]=="volunteer"?"selected":"").'>Volunteer</option>' .
			'<option value="socialworker" '.($_POST["s_type"]=="socialworker"?"selected":"").'>Social Worker</option>' .
			'<option value="manager" '.($_POST["s_type"]=="manager"?"selected":"").'>Manager</option>' .
			'</select></td></tr>');
	echo('<tr><td colspan="2" align="left"><input type="hidden" name="s_submitted" value="1"><input type="submit" name="Search" value="Search"></td></tr>');
	echo('</table></form></p>');
?>