<?php
include_once(dirname(__FILE__).'/../domain/Week.php');
include_once(dirname(__FILE__).'/../domain/RoomLog.php');
class testWeek extends UnitTestCase {
    function testWeekModule() {
        $today = date('y-m-d');
        $timestamp = mktime(0,0,0,substr($today,3,2), substr($today,6,2), substr($today,0,2)); 
      	$dayofweek = date("N", $timestamp);
      	$mondaymm = date("m",mktime(0,0,0,substr($today,3,2),substr($today,6,2)-$dayofweek+1,substr($today,0,2)));
        $mondaydd = date("d",mktime(0,0,0,substr($today,3,2),substr($today,6,2)-$dayofweek+1,substr($today,0,2)));
        $mondayyy = date("y",mktime(0,0,0,substr($today,3,2),substr($today,6,2)-$dayofweek+1,substr($today,0,2)));
        $roomlogs = new ArrayObject();
        for ($d=$mondaydd; $d<$mondaydd+7; $d++) {
        	$day_id=date("y-m-d",mktime(0,0,0,$mondaymm,$d,$mondayyy));
        	$roomlogs[] = new RoomLog($day_id);
        }
		$w = new Week($roomlogs);
		$mon = $roomlogs[0];
		$sun = $roomlogs[6];
 		$this->assertTrue($w->get_id() == $mon->get_id());
		$this->assertTrue(sizeof($w->get_roomlogs()) == 7);
 		$this->assertTrue($w->get_name() == 
		                  $mon->get_name()." to ".$sun->get_name());
		$this->assertTrue($w->get_end() == $sun->get_end_time());
		$this->assertTrue(true);
		echo ($mon->get_name()." to ".$sun->get_name()."\n");
		echo ("testWeek complete");
    }
}

?>
