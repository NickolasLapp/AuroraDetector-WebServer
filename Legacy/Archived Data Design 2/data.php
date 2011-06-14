<?php
	// This file accepts the unix timestamps (in milliseconds) of when the data is requested in the user's local time and then returns the data in csv format.
	$ID = $_REQUEST['id'];
	$from_date = ($_REQUEST['from'])/1000;
	$to_date = ($_REQUEST['to'])/1000;
	$tz = str_replace('_', ' ', $_REQUEST['tz']);
	
	// Set the timezone to be UTC
	date_default_timezone_set('UTC');
	
	// This section determines the user's timezone's offset from UTC
	// Find which UTC dates the from and to dates are part of and get the timestamps for the start and end of those dates.
	$search_from_date_UTC = gmmktime(0,0,0, gmdate("n", $from_date), gmdate("j", $from_date), gmdate("Y", $from_date));
	$search_to_date_UTC = gmmktime(0,0,0, gmdate("n", $to_date), gmdate("j", $to_date), gmdate("Y", $to_date));
	
	// Calculate the offset from UTC for the local timezone
	$offset = $search_from_date_UTC-$from_date;
	
	// If the calculated offset is less than -12 hours, then the timezone has a positive offset
	if ($offset < -43200) {
		$offset = $offset + (24*3600);
	}
	
	// If the calculated timezone is exactly -12 hours, then used the passed in timezone to check if it is +/- 12 hours
	if ($offset == -43200) {
		if (preg_match('/fiji/i', $tz) > 0) { $offset = 43200; }
		if (preg_match('/kamchatka/i', $tz) > 0) {
			if (preg_match('/daylight/i', $tz) > 0) { $offset = 46800; }
			else { $offset = 43200; }
		}
		if (preg_match('/new\szealand/i', $tz) > 0) {
			if (preg_match('/daylight/i', $tz) > 0) { $offset = 46800; }
			else { $offset = 43200; }
		}
	}
	
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=\"".$ID.".csv\"");
	
	// Find all of the files that contain the desired data
	$path = 'data/'.$ID;
	$dir_handle = @opendir($path) or die("Error: Cannot open the data folder for detector id: ".$ID."<br>");
	while ($file = readdir($dir_handle)) {
		if (preg_match('/^(\d{4}-\d{2}-\d{2}.txt)/', $file) ) {
			if (substr($file,0,4)!=0) {
				if ( ( gmmktime(0, 0, 0, substr($file,5,2), substr($file,8,2), substr($file,0,4)) >= $search_from_date_UTC)
					 && ( gmmktime(0, 0, 0, substr($file,5,2), substr($file,8,2), substr($file,0,4)) <= $search_to_date_UTC) ) {
					$files_list[] = $file;					
				}
			}	
		}	
	}
	
	if (count($files_list) == 0) {
		// If no files exist for this detector in the time range specified, insert a dummy data point to prevent problems with AMcharts.
		echo "UTC Timestamp,Local Timestamp,PMT Digital Number,PD Digital Number\r\n";	
		$data_array[date('Y-m-d H:i', $from_date)] = array(date('Y-m-d H:i', $from_date), date('Y-m-d H:i', ($from_date+$offset)), 0, 0);
	} else {
		// Else add all of the data in the time range
		foreach ($files_list as $file_name) {
			$handle = fopen($path.'/'.$file_name,"r");
			while($data = fgetcsv($handle, 1024)){
				if ((strtotime($data[0]) !== false ) && ($data[0] != 'UTC')) {
					$current_time = strtotime($data[0]);
					if (($current_time >= $from_date) && ($current_time <= $to_date)) {
						if ($data_array[date('Y-m-d H:i', $current_time)] == NULL) {
							$data_array[date('Y-m-d H:i', $current_time)] = array(date('Y-m-d H:i', $current_time), date('Y-m-d H:i', ($current_time+$offset)), $data[1], $data[2]);
							$data_points = 1;
						} else {
							$data_points++;
							$average_PMT = round($data_array[date('Y-m-d H:i', $current_time)][2]*(($data_points-1)/$data_points) + $data[1]*(1/$data_points));
							$average_PD = round($data_array[date('Y-m-d H:i', $current_time)][3]*(($data_points-1)/$data_points) + $data[2]*(1/$data_points));
							$data_array[date('Y-m-d H:i', $current_time)] = array(date('Y-m-d H:i', $current_time), date('Y-m-d H:i', ($current_time+$offset)), $average_PMT, $average_PD);
						}
					}
				}
			}
		}
	}
	
	// If there are more than a week's (approximately) data points for the detector in the specified time range, average the data in 10 minute intervals.
	if (count($data_array) > 10000) {
		foreach ($data_array as $data) {
			$current_time = (floor(strtotime($data[0])/600) * 600);
			if ($new_data_array[date('Y-m-d H:i', $current_time)] == NULL) {
				$new_data_array[date('Y-m-d H:i', $current_time)] = array(date('Y-m-d H:i', $current_time), date('Y-m-d H:i', ($current_time+$offset)), $data[2], $data[3]);
				$data_points = 1;
			} else {
				$data_points++;
				$average_PMT = round($new_data_array[date('Y-m-d H:i', $current_time)][2]*(($data_points-1)/$data_points) + $data[2]*(1/$data_points));
				$average_PD = round($new_data_array[date('Y-m-d H:i', $current_time)][3]*(($data_points-1)/$data_points) + $data[3]*(1/$data_points));
				$new_data_array[date('Y-m-d H:i', $current_time)] = array(date('Y-m-d H:i', $current_time), date('Y-m-d H:i', ($current_time+$offset)), $average_PMT, $average_PD);
			}
		}
		$data_array = $new_data_array;
	}
	
	// Echo out each data point with proper separator
	foreach ($data_array as $row) {
		for ($ii=0; $ii<count($row); $ii++) {
			if ($ii == (count($row)-1)) {
				echo $row[$ii]."\r\n";
			} else {
				echo $row[$ii].',';
			}
		}
	}
?>