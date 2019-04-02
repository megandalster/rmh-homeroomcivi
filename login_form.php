<?php
/*
 * Created on Mar 28, 2008
 * @author Oliver Radwan <oradwan@bowdoin.edu>
 */
?>
<div>
	<div id="content">
			<?PHP
				include_once(dirname(__FILE__).'/database/dbPersons.php');
     			include_once(dirname(__FILE__).'/domain/Person.php');
     			if(($_SERVER['PHP_SELF'])=="/logout.php"){
     				//prevents infinite loop of logging in to the page which logs you out...
     				echo "<script type=\"text/javascript\">window.location = \"index.php\";</script>";
     			}
				if(!array_key_exists('_submit_check', $_POST)){
					echo('<div align="left"><p>Access to <i>Homeroom</i> requires a Username and a Password. '  );
					echo('<ul><li>You must be a Ronald McDonald House <i>staff member</i> to access this system.');
					echo('<li> If you do not remember your Password, please contact the House Manager.</ul>');
					echo('<p><table><form method="post"><input type="hidden" name="_submit_check" value="true"><tr><td>Username:</td>
                                <td><input type="text" name="user" tabindex="1"></td></tr><tr><td>Password:</td>
                                <td><input type="password" name="pass" tabindex="2"></td></tr>
                            <tr><td colspan="2" align="center"><input type="submit" name="Login" value="Login"></td></tr></table>');
				}
				else{
						$db_pass = md5($_POST['pass']);
						$db_id = $_POST['user'];
						$person = retrieve_dbPersons($db_id);
						
					//	echo $person->get_id() . " = retrieved person_id<br>";
						if($person){ //avoids null results
						    if($person->get_password()==$db_pass && in_array('manager', $person->get_type())) { 
						        //if the passwords match and the person is a manager, login
								$_SESSION['access_level'] = 3;
								$_SESSION['f_name']=$person->get_first_name();
								$_SESSION['l_name']=$person->get_last_name();
								$_SESSION['_id']=$_POST['user'];
								$_SESSION['logged_in']=1;
								echo "<script type=\"text/javascript\">window.location = \"index.php\";</script>";
							}
							else {
								echo('<div align="left"><p class="error">Error: invalid username/password.');
								echo('<br />if you cannot remember your password, ask the House Manager to reset it for you.</p>');
								echo('<p><table><form method="post"><input type="hidden" name="_submit_check" value="true"><tr>
                                    <td>Username:</td><td><input type="text" name="user" tabindex="1"></td></tr>
                                    <tr><td>Password:</td><td><input type="password" name="pass" tabindex="2"></td></tr>
                                    <tr><td colspan="2" align="center"><input type="submit" name="Login" value="Login"></td></tr></table>');
							}
						}
						else{
							//At this point, they failed to authenticate
							echo('<div align="left"><p class="error">Error: invalid username/password.');
								echo('<br />if you cannot remember your password, ask the House Manager to reset it for you.</p>');
								echo('<p><table><form method="post"><input type="hidden" name="_submit_check" value="true"><tr><td>Username:</td>
                                    <td><input type="text" name="user" tabindex="1"></td></tr><tr><td>Password:</td>
                                    <td><input type="password" name="pass" tabindex="2"></td></tr>
                                <tr><td colspan="2" align="center"><input type="submit" name="Login" value="Login"></td></tr></table>');
							}
				}
			?>
				
			</div><?PHP include('footer.inc');?>
		</div>
	</body>
</html>
