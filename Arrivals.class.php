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
    private $offset;
    private $rowcount;

    public function __construct($package, $resource, $RESTparameters) {
        parent::__construct($package, $resource, $RESTparameters);
    }
    public static function getParameters(){
        return array("stationid" => "Station ID that can be found in the Stations resource",
                     "year" => "Year",
                     "month" => "Month",
                     "day" => "Day"
                     ,"hour" => "Hour"
                     ,"minute" => "Minute"
                     ,"offset" => "Offset"
                     ,"rowcount" => "Rowcount");
    }

    public static function getRequiredParameters(){
        return array("stationid","year","month","day","hour","minute");
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
        } else if ($key == "offset") {
            $this->offset = $val;
        } else if ($key == "rowcount") {
            $this->rowcount = $val;
        }
    }

    public function read(){
        $stopTimesDao = new MIVBSTIBStopTimesDao();
	
        return $stopTimesDao->getArrivals($this->stationid, $this->year, $this->month, $this->day, $this->hour, $this->minute, $this->offset, $this->rowcount);
    }

    public static function getDoc(){
        return "This resource contains the Arrivals for a certain Station for a certain date and time from MIVB/STIB.";
    }
}

?>