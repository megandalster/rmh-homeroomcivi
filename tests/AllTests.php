<?php
/*
 * Run all the RMH Homeroom unit tests
 */
// require_once(dirname(__FILE__).'/simpletest/autorun.php');
class AllTests extends GroupTest {
 	  function AllTests() {
        $this->addTestFile(dirname(__FILE__).'/testdbLoaners.php');
        $this->addTestFile(dirname(__FILE__).'/testLoaner.php');
        $this->addTestFile(dirname(__FILE__).'/testdbRooms.php');
        $this->addTestFile(dirname(__FILE__).'/testRoom.php');
        $this->addTestFile(dirname(__FILE__).'/testdbBookings.php');
        $this->addTestFile(dirname(__FILE__).'/testBooking.php');
        $this->addTestFile(dirname(__FILE__).'/testdbRoomLogs.php');
        $this->addTestFile(dirname(__FILE__).'/testRoomLog.php');
        $this->addTestFile(dirname(__FILE__).'/testdbPersons.php');
        $this->addTestFile(dirname(__FILE__).'/testPerson.php');
        $this->addTestFile(dirname(__FILE__).'/testOccupancyData.php');
//        $this->addTestFile(dirname(__FILE__).'/testWeek.php');
       
        echo ("All tests complete");
 	  }
 }
?>
