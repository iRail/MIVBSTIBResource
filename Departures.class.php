<?php
/**
 * This is a class which will return the information with the latest departures from a certain station
 * 
 * @package packages/Departures
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Maarten Cautreels <maarten@flatturtle.com>
 */

include_once('MIVBSTIBStopTimesDao.php');
 
class MIVBSTIBDepartures extends AReader{

	public function __construct($package, $resource, $RESTparameters) {
		parent::__construct($package, $resource, $RESTparameters);
		
		$this->offset = 0;
		$this->rowcount = 1024;
	}
	
    public static function getParameters(){
        return array("stationidentifier" => "Station Name or ID that can be found in the Stations resource",
                     "year" => "Year",
                     "month" => "Month",
                     "day" => "Day"
                     ,"hour" => "Hour"
                     ,"minute" => "Minute"
                     ,"offset" => "Offset"
                     ,"rowcount" => "Rowcount");
    }

    public static function getRequiredParameters(){
        return array("stationidentifier","year","month","day","hour","minute");
    }

    public function setParameter($key,$val){
        if ($key == "stationidentifier"){
            $this->stationidentifier = $val;
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
        } else if ($key == "offset") {
            $this->offset = $val;
        } else if ($key == "rowcount") {
            $this->rowcount = $val;
        }
    }

    public function read(){
        $stopTimesDao = new MIVBSTIBStopTimesDao();
        
		if(is_numeric($this->stationidentifier)) {
			return $stopTimesDao->getDeparturesByID($this->stationidentifier, $this->year, $this->month, $this->day, $this->hour, $this->minute, $this->offset, $this->rowcount);
		} else {
			return $stopTimesDao->getDeparturesByName($this->stationidentifier, $this->year, $this->month, $this->day, $this->hour, $this->minute, $this->offset, $this->rowcount);
		}
    }

    public static function getDoc(){
        return "This resource contains the Departures for a certain Station for a certain date and time from MIVB/STIB.";
    }
}

?>