<?PHP
	session_start();
	session_cache_expire(30);
?>
<?php
/*
 * Created on Apr 2, 2008
 * @author Oliver Radwan <oradwan@bowdoin.edu>
 */
?>
<html>
	<head>
		<title>
			Search for People
		</title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<link rel="stylesheet" href="lib/jquery-ui.css" />
	</head>
	<body>
		<div id="container">
			<?PHP include('header.php');?>
			<div id="content">
				<?PHP
					if($_POST['s_submitted']){
						$fns = trim(str_replace('\'','&#39;',htmlentities($_POST['s_first_name'])));
						$lns = trim(str_replace('\'','&#39;',htmlentities($_POST['s_last_name'])));
					//	$ems = trim(str_replace('\'','&#39;',htmlentities($_POST['s_email'])));
                        $pns = trim(str_replace('\'','&#39;',htmlentities($_POST['s_patient_name'])));
                        $mns = trim(str_replace('\'','&#39;',htmlentities($_POST['s_mgr_notes'])));
						$query = "SELECT * FROM dbPersons WHERE first_name LIKE '%".$fns."%' " .
								"AND last_name LIKE '%".$lns."%' " .
						//		"AND email LIKE '%".$ems."%' " .
								"AND patient_name LIKE '%".$pns."%' " .
						        "AND mgr_notes LIKE '%".$mns."%' " ;		
						if ($_POST['s_type']!=="") $query .= "AND type LIKE '%".$_POST['s_type']."%' ";
						$query .= "ORDER BY last_name,first_name";
						include_once('database/dbinfo.php');
						$con=connect();
						$result = mysqli_query($con,$query);
						if(!$result)echo mysqli_error($con);
						mysqli_close($con);
						echo('<p>You may search for people with past bookings using any of the following options.<br /><span style="font-size:x-small">A search for "an" would return D<strong>an</strong>, J<strong>an</strong>e, <strong>An</strong>n, and Sus<strong>an</strong></span>.</p>');
						include('searchPeople.inc.php');
						echo('<p><strong>Search Results: '.mysqli_num_rows($result).' found...</strong>');
						echo '<div id="target" style="overflow: scroll; width: variable; height: 400px;">';
						echo('<hr size="1" width="30%" align="left">');
					    echo('<p><table class="searchResults">');
							if(mysqli_num_rows($result)) {
								echo('<tr><td class="searchResults"><strong>Name</strong></td><td class="searchResults"><strong>Patient Name</strong></td></tr>');
							    while($thisRow = mysqli_fetch_array($result, MYSQLI_ASSOC)){
								  echo('<tr><td class="searchResults">'.$thisRow['last_name'].", ".
									$thisRow['first_name'].'</td><td class="searchResults">'.$thisRow['patient_name'].'</td><td class="searchResults"><a href=viewPerson.php?id='.
									$thisRow['id'].">view</a></td><td class=searchResults><a href=personEdit.php?id=".
									$thisRow['id'].">edit</a></td><td class=searchResults><a href=bookingEdit.php?id=".
									$thisRow['id'].">create new booking</a></td></tr>");
							    }
							}
						echo("</table></p>");
						echo('<hr size="1" width="30%" align="left">');
						echo "</div>";
					}
					else {
						echo('<p>You may search for people with past bookings using any of the following options.<br /><span style="font-size:x-small">A search for "an" would return D<strong>an</strong>, J<strong>an</strong>e, <strong>An</strong>n, and Sus<strong>an</strong></span>.</p>');
						include('searchPeople.inc.php');
					}
					
				?>
				<!-- below is the footer that we're using currently-->
				
			</div><?PHP include('footer.inc');?>
		</div>
	</body>
</html>