<?PHP
	echo('<form method="post"><p><strong>Search for People</strong>');
	echo('<table><tr><td>First Name:</td><td><input type="text" name="s_first_name"></td></tr>');
	echo('<tr><td>Last Name:</td><td><input type="text" name="s_last_name"></td></tr>');
	echo('<tr><td>E-mail:</td><td><input type="text" name="s_email"></td></tr>');
	echo('<tr><td>Patient Name:</td><td><input type="text" name="s_patient_name"></td></tr>');
    echo('<tr><td>Manager Notes:</td><td><input type="text" name="s_mgr_notes"></td></tr>');
    echo('<tr><td>Type:</td><td><select name="s_type">' .
			'<option value=""></option>' .
			'<option value="guest">guest</option>' .
			'<option value="volunteer">volunteer</option>' .
			'<option value="socialworker">socialworker</option>' .
			'<option value="manager">manager</option>' .
			'</select></td></tr>');
	echo('<tr><td colspan="2" align="left"><input type="hidden" name="s_submitted" value="1"><input type="submit" name="Search" value="Search"></td></tr>');
	echo('</table></form></p>');
?>