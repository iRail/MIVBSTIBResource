<?php
/**
 * This is a class which will return the information with the latest arrivals from a certain station
 * 
 * @package packages/Arrivals
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Maarten Cautreels <maarten@flatturtle.com>
 */

include_once('MIVBSTIBStopTimesDao.php');
 
class MIVBSTIBArrivals extends AReader{

	public function __construct($package, $resource, $RESTparameters) {
		parent::__construct($package, $resource, $RESTparameters);
	}

    public static function getParameters(){
		return array();
    }

    public static function getRequiredParameters(){
		return array("Station ID" => "stationid",
		"Year" => "year",
		"Month" => "month",
		"Day" => "day"
		,"Hour" => "hour"
		,"Minute" => "minute");
    }

    public function setParameter($key,$val){
        if ($key == "stationid"){
			$this->stationid = $val;
		} else if ($key == "year"){
			$this->year = $val;
		} else if ($key == "month"){
			$this->month = $val;
		} else if ($key == "day") {
			$this->day = $val;
		} else if ($key == "hour") {
			$this->hour = $val;
		} else if ($key == "minute") {
			$this->minute = $val;
		}
    }

    public function read(){
		$stopTimesDao = new MIVBSTIBStopTimesDao();
	
		return $stopTimesDao->getArrivals($this->stationid, $this->year, $this->month, $this->day, $this->hour, $this->minute);
    }

    public static function getDoc(){
		return "This resource contains the Arrivals for a certain Station for a certain date and time from MIVB/STIB.";
    }
}

?>