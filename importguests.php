<?php
include_once(database/dbPersons.php);
include_once(domain/Person);
    $filename = "file:///Users/allentucker/Desktop/guests1_50.csv";
	$handle = fopen($filename, "r");
	if ($handle==false) echo "failed to open";
	$keys = fgetcsv($handle,0,',','"');
	var_dump($keys);
	$values = fgetcsv($handle,0,',','"');
	echo "<br>";
	var_dump($values);
	$g = array_combine($keys,$values);
	$pgb = build_patients($g['patient1'], $g['patient2'], $g['patient3'],$g['gender1'], $g['gender2'], $g['gender3'],
							$g['birthday1'], $g['birthday2'], $g['birthday3']);
	$p = new Person($g['last_name'], $g['first_name'], "", "", $g['address'], $g['city'], $g['state'], $g['zip'], $g['phone1'], 
					$g['phone2'], $g['email'], array('guest'), array(), $pgb[0], $pgb[1], $pgb[2], '');
	$p->set_mgr_notes($g['mgr_notes']);
	echo "<br>";
	var_dump($p);
	fclose($handle);
	
function build_patients($p1, $p2, $p3, $g1, $g2, $g3, $b1, $b2, $b3) {
	$p = array();
	$b = ""; $g = "";
	if ($p1!="") {
		$p[]=$p1; $b = $date_fix(b1);$g = $g1;
		if ($p2!="")
			$p[]=$p2;
		if  ($p3!="")
			$p[]=$p3;
	}
	else if ($p2!="")
	{
		$p[]=$p2; $b = date_fix($b2);$g = $g2;
		if ($p3!="")
			$p[]=$p3;
	}
	else if ($p3!="") {
		$p[]=$p3; $b = date_fix($b3);$g = $g3;
	}
	return array($p,$b,$g);
}
function date_fix($d) {
	$yy_mm_dd = "-";
	$i = strpos($d,"/");
	if ($i==1 || $i==2){
		$yy_mm_dd = substr($d,0,$i)."-";
		if ($i==1)
			$yy_mm_dd = "0".substr($d,0,1)."-";
		$d = substr($d,$i+1);
		$j = strpos($d,"/");
		if ($j==1 || $j==2) {
		    if ($j==1)
				$yy_mm_dd .= "0".substr($d,0,1);
			else $yy_mm_dd .= substr($d,0,2);
			$d = substr($d,$j+1);
			if (strlen($d)!=4)
				return "";
			else return substr($d,2,2).$yy_mm_dd;
		}
		else return "";
	}
	else return "";
}
?>
