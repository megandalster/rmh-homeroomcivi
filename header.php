<!--
/*
 * Copyright 2011-2013 by Felix Emiliano, Luis Rojas, David Phipps, 
 * Ruben Martinez, Alex Lucyk, Jesus Navarro, and Allen Tucker.
 * This program is part of RMH Homeroom, which is free software.
 * It comes with absolutely no warranty.  You can redistribute and/or
 * modify it under the terms of the GNU Public License as published
 * by the Free Software Foundation (see <http://www.gnu.org/licenses/).
*/
-->

<style type="text/css">
h1 {padding-left: 0px; padding-right:165px;}
</style>
<div id="header">
<!--<br><br><img src="images/rmhHeader.gif" align="center"><br>
<h1><br><br>RMH Homebase <br></h1>-->

</div>

<div align="center" id="navigationLinks">

<?PHP
	//Log-in security
	//If they aren't logged in, display our log-in form.
	if(!isset($_SESSION['logged_in'])){
		include('login_form.php');
		die();
	}
	else if($_SESSION['logged_in']){

		/**
		 * Set our permission array for guests, volunteers, social workers, and managers.
		 * If a page is not specified in the permission array, anyone logged into the system
		 * can view it. If someone logged into the system attempts to access a page above their
		 * permission level, they will be sent back to the home page.
		 */
		//pages clients can view
		//pages volunteers can view
		//additional pages social workers can view
		//additional pages managers can view
		$permission_array['index.php']=3;
	    $permission_array['about.php']=3;
	    $permission_array['searchPeople.php']=3;
		$permission_array['roomLog.php']=3;
		$permission_array['room.php']=3;
		$permission_array['bookingEdit.php']=3;
		$permission_array['personEdit.php']=3;
		$permission_array['log.php']=3;
		$permission_array['data.php']=3;

		//Check if they're at a valid page for their access level.
		$current_page = strtolower(substr($_SERVER['PHP_SELF'], strpos($_SERVER['PHP_SELF'],"/")+1));
		$current_page = substr($current_page, strpos($current_page,"/")+1);
		
		if($permission_array[$current_page]>$_SESSION['access_level']){
		    //in this case, the user doesn't have permission to view this page.
		    //we redirect them to the index page.
		    echo "<script type=\"text/javascript\">window.location = \"index.php\";</script>";
		    //note: if javascript is disabled for a user's browser, it would still show the page.
		    //so we die().
		    die();
		}

		//This line gives us the path to the html pages in question, useful if the server isn't installed @ root.
		$path = strrev(substr(strrev($_SERVER['SCRIPT_NAME']),strpos(strrev($_SERVER['SCRIPT_NAME']),'/')));

		//they're logged in and session variables are set.
		echo('<br><a href="'.$path.'index.php">home</a> | ');
		echo('<a href="'.$path.'about.php">about</a>');
		if ($_SESSION['access_level']==0) // clients
		    echo('<a href="bookingEdit.php?id=new'.'"> | room request</a>');
		
		if($_SESSION['access_level']>=1) // volunteers, social workers and managers 
		{	
		    echo(' | <strong>bookings:</strong> <a href="'.$path.'viewBookings.php?id=pending">pending,</a> <a href="'.$path.'searchBookings.php">search</a>' . 
			                                    '<a href="bookingEdit.php?id=new'.'">, new</a>');
		    echo('<br> <strong>guests:</strong> <a href="'.$path.'view.php">view,</a> <a href="'.$path.'searchPeople.php">search</a>');
		    if ($_SESSION['access_level']>=1)
	    	    echo('<a href="personEdit.php?id='.'new'.'">, add</a> ');
		}
	    if($_SESSION['access_level']>1) { // managers 
	        echo ('| <a href="'.$path.'log.php">log</a> | <a href="'.$path.'data.php?date='.date('y-01-01').'&enddate='.date('y-m-d').'">data</a>');
	    }
		if($_SESSION['access_level']>=1) { // volunteers, social workers, and managers
		    echo('<br><a href="roomLog.php?date=today">Room Logs</a>');
			echo(' | <a href="'.$path.'help.php?helpPage='.$current_page.'" target="_BLANK">help</a>');
		}
		echo(		' | <a href="'.$path.'logout.php">logout</a> <br>');
	}
?>
</div>
<!-- End Header -->