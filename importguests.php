<?php
include_once(dirname(__FILE__).'/database/dbPersons.php');
include_once(dirname(__FILE__).'/domain/Person.php');
    $filename = "file:///Users/allen/Desktop/00-14guestsfinal.csv";
	$handle = fopen($filename, "r");
	if ($handle==false) echo "failed to open";
	$keys = fgetcsv($handle,0,',','"');
	$values = fgetcsv($handle,0,',','"');
	$count=0;
	while ($values) {
	    $g = array_combine($keys,$values);
	   // echo "<br><br>";var_dump($g);
	    $pgb = build_patients($g['patient1'], $g['patient2'], $g['patient3'],
	                        $g['gender1'], $g['gender2'], $g['gender3'],
							$g['birthday1'], $g['birthday2'], $g['birthday3']);
		// echo "<br><br>";var_dump($pgb);
	    $p = new Person($g['last_name'], $g['first_name'], "", "", $g['address'], $g['city'], $g['state'], $g['zip'], $g['phone1'], 
					$g['phone2'], $g['email'], "guest", "", implode(',',$pgb[0]), $pgb[2], $pgb[1],'', '');
	    $p->set_mgr_notes($g['mgr_notes']);
	    if (!insert_dbPersons($p))
	        echo "<br><br>did not import :".$g['last_name']." ". $g['first_name'];
	    else $count++;
	    $values = fgetcsv($handle,0,',','"');
	}
	fclose($handle);
	echo "<br><br>".$count." guests imported";
	
function build_patients($p1, $p2, $p3, $g1, $g2, $g3, $b1, $b2, $b3) {
	$p = array();
	$b = ""; $g = "";
	if ($p1>" ") {
	    $p[]=$p1; $b = date_fix($b1);$g = $g1;
		if ($p2>" ")
			$p[]=$p2;
		if ($p3>" ")
			$p[]=$p3;
			
	}
	else if ($p2>" ")
	{
		$p[]=$p2; $b = date_fix($b2);$g = $g2;
		if ($p3>" ")
			$p[]=$p3;
	}
	else if ($p3>" ") {
		$p[]=$p3; $b = date_fix($b3);$g = $g3;
	}
	return array($p,$g,$b);
}
function date_fix($d) {
	$yy_mm_dd = "";
	$i = strpos($d,"/");
	if ($i==1 || $i==2){
		$yy_mm_dd = substr($d,0,$i)."-";
		if ($i==1)
			$yy_mm_dd = "0".$yy_mm_dd;
		$d = substr($d,$i+1);
		$j = strpos($d,"/");
		if ($j==1 || $j==2) {
		    if ($j==1)
				$yy_mm_dd .= "0".substr($d,0,1);
			else $yy_mm_dd .= substr($d,0,2);
			$d = substr($d,$j+1);
			if (strlen($d)!=4)
				return "";
			else return substr($d,2,2)."-".$yy_mm_dd;
		}
		else return "";
	}
	else return "";
}
?>
