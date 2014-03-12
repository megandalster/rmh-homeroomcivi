<?php
include_once(dirname(__FILE__).'/domain/Booking.php');
include_once(dirname(__FILE__).'/database/dbBookings.php');
include_once(dirname(__FILE__).'/domain/Person.php');
include_once(dirname(__FILE__).'/database/dbPersons.php');
//echo dirname(__FILE__);

$filename = "file:///Users/allen/Desktop/00-14bookings3001-3478.csv";
	$handle = fopen($filename, "r");
	if ($handle==false) echo "failed to open";
	$keys = fgetcsv($handle,0,',','"');
	$g = fgetcsv($handle,0,',','"');
	$count=0; $pcount=0;
	while ($g) {
	//    $g = array_combine($keys,$values);
	//    echo "<br><br>";var_dump($g);
	    $p = retrieve_dbPersons($g[0]);
	//    echo "<br><br>";var_dump($p);
	    if ($p) {
	      $pcount++;
	      echo "<br>".$pcount;
	      for ($i=6; $i<=64; $i+=2) 
	        if ($g[$i]>="00-01-01" && $g[$i]<="14-03-09" && $g[$i+60]>=$g[$i]) {
	            $b = makenew_booking($p, $g[1],$g[2],$g[3],$g[5],
	                            $g[$i],$g[$i+60],room_fix($g[$i+1]));
	            if (!insert_dbBookings($b)) {echo "<br><br>booking not added: b = ";var_dump($b);}
	            else {$p->add_prior_booking($b->get_id()); $count++;}
	        }
	      update_dbPersons($p); 
	    }
	    $g = fgetcsv($handle,0,',','"');
	}
	fclose($handle);
	echo "<br><br>".$count. " bookings imported";
	
function makenew_booking($p, $additional_guest, $hospital, $department, $mgr_notes, $date_in, $date_out, $room_no) {
    $occupants = array($p->get_first_name()." ".$p->get_last_name());
    if ($additional_guest>" ")
        $occupants[]= $additional_guest;
//    echo "<br><br>";var_dump($p, $date_in, $p->get_id(), $room_no, $p->get_patient_name(), $occupants, $date_out, 
//            $hospital, $department,$mgr_notes);
	$b = new Booking($date_in, $date_in, $p->get_id(), "closed", $room_no, $p->get_patient_name(),
            $occupants, "", "", $date_out, "", 
            $hospital, $department, "00000000000", "", "", "", "", $mgr_notes, "");
	return $b;
}
function room_fix($r) {
	$valid_rooms = array("125","126","151","152","214","215",
						"218","223","224","231","232","233",
						"243","244","245","250","251","252",
						"253","254","255");
	if (strlen($r)>=6)
	    $room_no = substr($r,3,3);
	else $room_no = "";
	return $room_no;
}
?>
