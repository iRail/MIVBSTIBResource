<?php

/**
 * This is a class which will return the information with the latest departures from a certain station
 * 
 * @package packages/StopTimesDao
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Maarten Cautreels <maarten@flatturtle.com>
 */
class MIVBSTIBStopTimesDao {

    /*
     *	Timezone set to Europe/Brussels
     */
    var $timezone = "Europe/Brussels";

	/**
	  * Query to get all departures of a given station on a given date and starting from a given time
	  * @param String stationIdentifier
	  * @param int $year The Year (Required)
	  * @param int $month The Month (Required)
	  * @param int $day The Day (Required)
	  * @param int $hour The Hour (Required)
	  * @param int $minute The Minute (Required)
	  * @param int $monday Monday 
	  * @param int $tuesday Tuesday 
	  * @param int $wednesday Wednesday 
	  * @param int $thursday Thursday 
	  * @param int $friday Friday 
	  * @param int $saturday Saturday 
	  * @param int $sunday Sunday 
	  */
	private $GET_DEPARTURES_BY_NAME_QUERY = "SELECT DISTINCT route.route_short_name, trip.trip_headsign, route.route_color, route.route_text_color, trip.direction_id, times.departure_time_t
										FROM mivbstibgtfs_stop_times times
										JOIN mivbstibgtfs_trips trip
											ON trip.trip_id = times.trip_id
										JOIN mivbstibgtfs_routes route
											ON route.route_id = trip.route_id
										JOIN mivbstibgtfs_calendar calendar
											ON calendar.service_id = trip.service_id
										INNER JOIN mivbstibgtfs_stops stop
											ON stop.stop_name = :stationIdentifier
										WHERE times.stop_id = stop.stop_id
										AND times.departure_time_t >= TIME(STR_TO_DATE(CONCAT(:hour, ':', :minute), '%k:%i'))
										AND calendar.start_date <= STR_TO_DATE(CONCAT(:year, '-', :month, '-', :day), '%Y-%m-%d')
										  AND calendar.end_date >= STR_TO_DATE(CONCAT(:year, '-', :month, '-', :day), '%Y-%m-%d')
										  AND 
										  (
											calendar.monday = :monday
											OR calendar.tuesday = :tuesday
											OR calendar.wednesday = :wednesday
											OR calendar.thursday = :thursday
											OR calendar.friday = :friday
											OR calendar.saturday = :saturday
											OR calendar.sunday = :sunday
										  )
										AND (SELECT count(time.stop_id) 
												FROM  mivbstibgtfs_stop_times time
												WHERE time.trip_id = trip.trip_id
												AND time.arrival_time_t > times.departure_time_t) > 0
										ORDER BY times.departure_time_t
										LIMIT :offset, :rowcount;";
										
	/**
	  * Query to get all departures of a given station on a given date and starting from a given time
	  * @param int stationIdentifier
	  * @param int $year The Year (Required)
	  * @param int $month The Month (Required)
	  * @param int $day The Day (Required)
	  * @param int $hour The Hour (Required)
	  * @param int $minute The Minute (Required)
	  * @param int $monday Monday 
	  * @param int $tuesday Tuesday 
	  * @param int $wednesday Wednesday 
	  * @param int $thursday Thursday 
	  * @param int $friday Friday 
	  * @param int $saturday Saturday 
	  * @param int $sunday Sunday 
	  */
	private $GET_DEPARTURES_BY_ID_QUERY = "SELECT DISTINCT route.route_short_name, trip.trip_headsign, route.route_color, route.route_text_color, trip.direction_id, times.departure_time_t
										FROM mivbstibgtfs_stop_times times
										JOIN mivbstibgtfs_trips trip
											ON trip.trip_id = times.trip_id
										JOIN mivbstibgtfs_routes route
											ON route.route_id = trip.route_id
										JOIN mivbstibgtfs_calendar calendar
											ON calendar.service_id = trip.service_id
										WHERE times.stop_id = :stationIdentifier
										AND times.departure_time_t >= TIME(STR_TO_DATE(CONCAT(:hour, ':', :minute), '%k:%i'))
										AND calendar.start_date <= STR_TO_DATE(CONCAT(:year, '-', :month, '-', :day), '%Y-%m-%d')
										  AND calendar.end_date >= STR_TO_DATE(CONCAT(:year, '-', :month, '-', :day), '%Y-%m-%d')
										  AND 
										  (
											calendar.monday = :monday
											OR calendar.tuesday = :tuesday
											OR calendar.wednesday = :wednesday
											OR calendar.thursday = :thursday
											OR calendar.friday = :friday
											OR calendar.saturday = :saturday
											OR calendar.sunday = :sunday
										  )
										AND (SELECT count(time.stop_id) 
												FROM  mivbstibgtfs_stop_times time
												WHERE time.trip_id = trip.trip_id
												AND time.arrival_time_t > times.departure_time_t) > 0
										ORDER BY times.departure_time_t
										LIMIT :offset, :rowcount;";
		
	/**
	  * Query to get all departures of a given station on a given date and starting from a given time
	  * @param int stationIdentifier
	  * @param int $year The Year (Required)
	  * @param int $month The Month (Required)
	  * @param int $day The Day (Required)
	  * @param int $hour The Hour (Required)
	  * @param int $minute The Minute (Required)
	  * @param int $monday Monday 
	  * @param int $tuesday Tuesday 
	  * @param int $wednesday Wednesday 
	  * @param int $thursday Thursday 
	  * @param int $friday Friday 
	  * @param int $saturday Saturday 
	  * @param int $sunday Sunday 
	  */
	private $GET_ARRIVALS_BY_NAME_QUERY = "SELECT DISTINCT route.route_short_name, trip.trip_headsign, route.route_color, route.route_text_color, trip.direction_id, times.arrival_time_t
										FROM mivbstibgtfs_stop_times times
										JOIN mivbstibgtfs_trips trip
											ON trip.trip_id = times.trip_id
										JOIN mivbstibgtfs_routes route
											ON route.route_id = trip.route_id
										JOIN mivbstibgtfs_calendar calendar
											ON calendar.service_id = trip.service_id
										INNER JOIN mivbstibgtfs_stops stop
											ON stop.stop_name = :stationIdentifier
										WHERE times.stop_id = stop.stop_id
										AND times.arrival_time_t >= TIME(STR_TO_DATE(CONCAT(:hour, ':', :minute), '%k:%i'))
										AND calendar.start_date <= STR_TO_DATE(CONCAT(:year, '-', :month, '-', :day), '%Y-%m-%d')
										  AND calendar.end_date >= STR_TO_DATE(CONCAT(:year, '-', :month, '-', :day), '%Y-%m-%d')
										  AND 
										  (
											calendar.monday = :monday
											OR calendar.tuesday = :tuesday
											OR calendar.wednesday = :wednesday
											OR calendar.thursday = :thursday
											OR calendar.friday = :friday
											OR calendar.saturday = :saturday
											OR calendar.sunday = :sunday
										  )
										AND (SELECT count(time.stop_id) 
												FROM  mivbstibgtfs_stop_times time
												WHERE time.trip_id = trip.trip_id
												AND time.arrival_time_t > times.departure_time_t) > 0
										ORDER BY times.arrival_time_t
										LIMIT :offset, :rowcount;";
										
	/**
	  * Query to get all departures of a given station on a given date and starting from a given time
	  * @param int stationIdentifier
	  * @param int $year The Year (Required)
	  * @param int $month The Month (Required)
	  * @param int $day The Day (Required)
	  * @param int $hour The Hour (Required)
	  * @param int $minute The Minute (Required)
	  * @param int $monday Monday 
	  * @param int $tuesday Tuesday 
	  * @param int $wednesday Wednesday 
	  * @param int $thursday Thursday 
	  * @param int $friday Friday 
	  * @param int $saturday Saturday 
	  * @param int $sunday Sunday 
	  */
	private $GET_ARRIVALS_BY_ID_QUERY = "SELECT DISTINCT route.route_short_name, trip.trip_headsign, route.route_color, route.route_text_color, trip.direction_id, times.arrival_time_t
										FROM mivbstibgtfs_stop_times times
										JOIN mivbstibgtfs_trips trip
											ON trip.trip_id = times.trip_id
										JOIN mivbstibgtfs_routes route
											ON route.route_id = trip.route_id
										JOIN mivbstibgtfs_calendar calendar
											ON calendar.service_id = trip.service_id
										WHERE times.stop_id = :stationIdentifier
										AND times.arrival_time_t >= TIME(STR_TO_DATE(CONCAT(:hour, ':', :minute), '%k:%i'))
										AND calendar.start_date <= STR_TO_DATE(CONCAT(:year, '-', :month, '-', :day), '%Y-%m-%d')
										  AND calendar.end_date >= STR_TO_DATE(CONCAT(:year, '-', :month, '-', :day), '%Y-%m-%d')
										  AND 
										  (
											calendar.monday = :monday
											OR calendar.tuesday = :tuesday
											OR calendar.wednesday = :wednesday
											OR calendar.thursday = :thursday
											OR calendar.friday = :friday
											OR calendar.saturday = :saturday
											OR calendar.sunday = :sunday
										  )
										AND (SELECT count(time.stop_id) 
												FROM  mivbstibgtfs_stop_times time
												WHERE time.trip_id = trip.trip_id
												AND time.arrival_time_t > times.departure_time_t) > 0
										ORDER BY times.arrival_time_t
										LIMIT :offset, :rowcount;";
																
    /**
     *
     * @param int $stationName The Name of a station (Required)
     * @param int $year The Year (Required)
     * @param int $month The Month (Required)
     * @param int $day The Day (Required)
     * @param int $hour The Hour (Required)
     * @param int $minute The Minute (Required)
     * @return array A List of Departures for a given station, date and starting from a given time
     */
    public function getDeparturesByName($stationName, $year, $month, $day, $hour, $minute, $offset, $rowcount) {	
        date_default_timezone_set($this->timezone);
		
        $arguments = $this->processArguments($stationName, $year, $month, $day, $hour, $minute, $offset, $rowcount);
							
        $query = $this->GET_DEPARTURES_BY_NAME_QUERY;
		
        $result = R::getAll($query, $arguments);
		
        return $this->parseStopTimes($result, $year, $month, $day);
    }
	
	/**
     *
     * @param String $stationId The ID of a station (Required)
     * @param int $year The Year (Required)
     * @param int $month The Month (Required)
     * @param int $day The Day (Required)
     * @param int $hour The Hour (Required)
     * @param int $minute The Minute (Required)
     * @return array A List of Departures for a given station, date and starting from a given time
     */
    public function getDeparturesByID($stationId, $year, $month, $day, $hour, $minute, $offset, $rowcount) {	
        date_default_timezone_set($this->timezone);
		
        $arguments = $this->processArguments($stationId, $year, $month, $day, $hour, $minute, $offset, $rowcount);
							
        $query = $this->GET_DEPARTURES_BY_ID_QUERY;
		
        $result = R::getAll($query, $arguments);
		
        return $this->parseStopTimes($result, $year, $month, $day);
    }
	
    /**
     *
     * @param int $stationId The Unique identifier (Name) of a station (Required)
     * @param int $year The Year (Required)
     * @param int $month The Month (Required)
     * @param int $day The Day (Required)
     * @param int $hour The Hour (Required)
     * @param int $minute The Minute (Required)
     * @return array A List of Arrivals for a given station, date and starting from a given time
     */
    public function getArrivalsByName($stationName, $year, $month, $day, $hour, $minute, $offset, $rowcount) {	
        date_default_timezone_set($this->timezone);
		
        $arguments = $this->processArguments($stationName, $year, $month, $day, $hour, $minute, $offset, $rowcount);
		
        $query = $this->GET_ARRIVALS_BY_NAME_QUERY;
		
        $result = R::getAll($query, $arguments);
		
		return $this->parseStopTimes($result, $year, $month, $day);
    }
	
	/**
     *
     * @param int $stationId The Unique identifier of a station (Required)
     * @param int $year The Year (Required)
     * @param int $month The Month (Required)
     * @param int $day The Day (Required)
     * @param int $hour The Hour (Required)
     * @param int $minute The Minute (Required)
     * @return array A List of Arrivals for a given station, date and starting from a given time
     */
    public function getArrivals($stationId, $year, $month, $day, $hour, $minute, $offset, $rowcount) {	
        date_default_timezone_set($this->timezone);
		
        $arguments = $this->processArguments($stationId, $year, $month, $day, $hour, $minute, $offset, $rowcount);
		
        $query = $this->GET_ARRIVALS_BY_ID_QUERY;
		
        $result = R::getAll($query, $arguments);
		return $this->parseStopTimes($result, $year, $month, $day);
    }
	
    /**
     *
     * @param int $stationId The Unique identifier of a station (Required)
     * @param int $year The Year (Required)
     * @param int $month The Month (Required)
     * @param int $day The Day (Required)
     * @param int $hour The Hour (Required)
     * @param int $minute The Minute (Required)
     * @return array List of arguments that will be used in the SQL Query
     */
    private function processArguments($stationName, $year, $month, $day, $hour, $minute, $offset=0, $rowcount=1024) {
        $dayOfTheWeek = date("l", mktime(0, 0, 0, $month, $day, $year));
		
        // Initialize on 0
        $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = 0;
	
        switch (strtolower($dayOfTheWeek)) {
            case "monday":
                $monday = 1;
                break;
            case "tuesday":
                $tuesday = 1;
                break;
            case "wednesday":
                $wednesday = 1;
                break;
            case "thursday":
                $thursday = 1;
                break;
            case "friday":
                $friday = 1;
                break;
            case "saturday":
                $saturday = 1;
                break;
            case "sunday":
                $sunday = 1;
                break;
        }
	
        $arguments = array(":stationIdentifier" => urldecode($stationName), 
                           ":year" => urldecode($year), 
                           ":month" => urldecode($month), 
                           ":day" => urldecode($day), 
                           ":year" => urldecode($year), 
                           ":month" => urldecode($month), 
                           ":day" => urldecode($day), 
                           ":hour" => urldecode($hour),
                           ":minute" => urldecode($minute),
                           ":monday" => $monday, 
                           ":tuesday" => $tuesday, 
                           ":wednesday" => $wednesday, 
                           ":thursday" => $thursday, 
                           ":friday" => $friday, 
                           ":saturday" => $saturday, 
                           ":sunday" => $sunday,
                           ":offset" => intval(urldecode($offset)), 
                           ":rowcount" => intval(urldecode($rowcount)));
							
        return $arguments;
    }
	
	private function parseStopTimes($result, $year, $month, $day) {
		$stoptimes = array();
        foreach($result as &$row){
            $stoptime = array();
			
            $stoptime["short_name"] = $row["route_short_name"];
            $stoptime["long_name"] = $row["trip_headsign"];
            $stoptime["color"] = $row["route_color"];
            $stoptime["text_color"]  = $row["route_text_color"];
            $stoptime["direction"] = $row["direction_id"];

			if(isset($row["departure_time_t"])) {
				$timeString =  $row["departure_time_t"];
			} else {
				$timeString =  $row["arrival_time_t"];
			}
			
            $split = explode(':', $timeString);
            $hour = $split[0];
            $minute = $split[1];
			
            $date = mktime($hour, $minute, 0, $month, $day, $year);
            $stoptime["iso8601"] = date("c", $date);
            $stoptime["time"] = date("U", $date);
			
            $stoptimes[] = $stoptime;
        }

        return $stoptimes;
	}
}