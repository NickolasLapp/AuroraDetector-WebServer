<?php
	// Function for the Aurora Detector Network Website
	
	function load_data_summary($station_ID) {
		$IBC1_level = 0.30;		 // Not sure if these are 100% accurate,
		$IBC2_LEVEL = 2.90; // I just read them of the graphs.

		// Look at the station_ID's data folder
		$data_files = scandir('data/'.$station_ID);
		
		// Read the most recent data file available
		$most_recent_data_file = trim($data_files[count($data_files)-1]);
		
		// Define the arrays of data
		$UTC_timestamp = array();
		$local_date = array();
		$local_time = array();
		$PMT_rad = array();
		$duration = array();
		
		/* Sets up TimeZone information which is used by the date() function */
    	putenv("TZ=");
		
		$row_number = 0;
		$handle = fopen("data/".$station_ID."/".$most_recent_data_file,"r");
		while($data = fgetcsv($handle, 2048)){
        	$row_number++;
			if ($row_number > 2) {
				$trimmed_timestamp = trim($data[0]);
				$UTC_timestamp[] = $trimmed_timestamp;
				
				$temp_time = strtotime($trimmed_timestamp);
				$daylight_savings = date('I', $temp_time);
				if ($daylight_savings == "1") {
					$temp_time -= 6*3600;
					$local_date[] = date('M j, Y', $temp_time);
					$local_time[] = date('g:i:s a', $temp_time);
				} else {
					$temp_time -= 7*3600;	
					$local_date[] = date('M j, Y', $temp_time);
					$local_time[] = date('g:i:s a', $temp_time);
				}
				
				$current_PMT_rad = trim($data[6]);
				$PMT_rad[] = $current_PMT_rad;
				
				if ($current_PMT_rad >= $IBC1_level) {
					if ($UTC_timestamp[count($UTC_timestamp) - 2] == NULL) {
						$duration[] = 0;
					} else {
						$time_difference = strtotime($UTC_timestamp[count($UTC_timestamp)-1]) - strtotime($UTC_timestamp[count($UTC_timestamp)-2]);
						$duration[] = $duration[count($duration)-1] + $time_difference;
					}
				} else {
					$duration[] = 0;
				}
			}
		}
		
		$data = array("date" => $local_date, "time" => $local_time, "PMT_rad" => $PMT_rad, "duration" => $duration);
		return $data;
	}
?>