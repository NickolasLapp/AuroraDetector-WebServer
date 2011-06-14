<?php
	// This file accepts the unix timestamps (in milliseconds) of when the data is requested in the user's local time and then returns the data in csv format.
	$ID = $_REQUEST['id'];
	$from_date = ($_REQUEST['from'])/1000;
	$to_date = (($_REQUEST['to'])/1000)+86399;
	$tz = $_REQUEST['tz'];
		
	$local_timezone = new DateTimeZone($tz);
	
	$local_from_date = new DateTime(gmdate('Y-m-d H:i:s', $from_date), $local_timezone);
	$local_from_timestamp = $local_from_date->getTimestamp();
	$search_from_date_UTC = gmmktime(0,0,0, gmdate("n", $local_from_timestamp), gmdate("j", $local_from_timestamp), gmdate("Y", $local_from_timestamp));
	
	$local_to_date = new DateTime(gmdate('Y-m-d H:i:s', $to_date), $local_timezone);
	$local_to_timestamp = $local_to_date->getTimestamp();
	$search_to_date_UTC = gmmktime(0,0,0, gmdate("n", $local_to_timestamp), gmdate("j", $local_to_timestamp), gmdate("Y", $local_to_timestamp));
	
	echo 'Local --> from: '.$local_from_date->format('Y-m-d H:i:s e').' to: '.$local_to_date->format('Y-m-d H:i:s e').'<br><br>';
	
	echo 'Contained in UTC dates --> from: '.gmdate('Y-m-d', $search_from_date_UTC).' to: '.gmdate('Y-m-d', $search_to_date_UTC).'<br>';
	
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
	
	unset($dir_handle, $file);
	
	$UTC = array();
	$PMT5577DN = array();
	$PMT6300DN = array();
	$PMT4278DN = array();
	$PD1DN = array();
	$PD2DN = array();
	//$PMTS = array();
	//$TMP = array();
	//$SETTMP = array();
	//$TEPWR = array();
	//$TEDIR = array();
	//$PMT5577rad = array();
	//$PMT6300rad = array();
	//$PMT4278rad = array();
	//$PD1rad = array();
	//$PD2rad = array();
	//$rad_units = array();

	if (count($files_list) == 0) {
		// If no files exist for this detector in the time range specified, insert a dummy data point to prevent problems with AMcharts.
		echo "UTC,Local,PMT5577DN,PMT6300DN,PMT4278DN,PD1DN,PD2DN\r\n";	
		$data_array[date('Y-m-d H:i', $from_date)] = array(date('Y-m-d H:i', $from_date), date('Y-m-d H:i', ($from_date+$offset)), 0, 0, 0, 0, 0);
	} else {
		// Else add all of the data in the time range
		echo "UTC,Local,PMT5577DN,PMT6300DN,PMT4278DN,PD1DN,PD2DN\r\n";	
		foreach ($files_list as $file_name) {
			$handle = fopen($path.'/'.$file_name,"r");
			while($data = fgetcsv($handle, 1024)){
				$data[count($data)] = 'Local';
				if (strtotime($data[0]) !== false ) {
					if ($data[0] == 'UTC') {
						$UTC_col = 0;
						for ($ii = 1; $ii < count($data); $ii++) {
							//UTC, PMT5577DN, PMT6300DN, PMT4278DN, PD1DN, PD2DN, PMTS, TMP, SETTMP, TEPWR, TEDIR, PMT5577rad, PMT6300rad, PMT4278rad, PD1rad, PD2rad, rad_units,
							if ($data[$ii] == 'PMT5577DN') {
								$PMT5577DN_col = $ii;
							} elseif ($data[$ii] == 'PMT6300DN') {
								$PMT6300DN_col = $ii;
							} elseif ($data[$ii] == 'PMT4278DN') {
								$PMT4278DN_col = $ii;
							} elseif ($data[$ii] == 'PD1DN') {
								$PD1DN_col = $ii;
							} elseif ($data[$ii] == 'PD2DN') {
								$PD2DN_col = $ii;
							} elseif ($data[$ii] == 'PMT5577rad') {
								$PMT5577rad_col = $ii;
							} elseif ($data[$ii] == 'PMT6300rad') {
								$PMT6300rad_col = $ii;
							} elseif ($data[$ii] == 'PMT4278rad') {
								$PMT4278rad_col = $ii;
							} elseif ($data[$ii] == 'PD1rad') {
								$PD1rad_col = $ii;
							} elseif ($data[$ii] == 'PD2rad') {
								$PD2rad_col = $ii;
							}  
						}
					} else {
						$current_time = strtotime($data[0]);
						if (($current_time >= $from_date) && ($current_time <= $to_date)) {
							if ($data_array[date('Y-m-d H:i', $current_time)] == NULL) {
								echo $data[0].'<br>';
								$local_date = (new DateTime(gmdate('Y-m-d H:i:s', $current_time), $local_timezone));
								//$data_array[date('Y-m-d H:i', $current_time)] = array(date('Y-m-d H:i', $current_time), $local_date, $data[1], $data[2]);
								echo $local_date->format('Y-m-d H:i:s');
								$data_points = 1;
							} //else {
								//$data_points++;
								//$average_PMT = round($data_array[date('Y-m-d H:i', $current_time)][2]*(($data_points-1)/$data_points) + $data[1]*(1/$data_points));
								//$average_PD = round($data_array[date('Y-m-d H:i', $current_time)][3]*(($data_points-1)/$data_points) + $data[2]*(1/$data_points));
								//$data_array[date('Y-m-d H:i', $current_time)] = array(date('Y-m-d H:i', $current_time), date('Y-m-d H:i', ($current_time+$offset)), $average_PMT, $average_PD);
							//}
						}
					}
				}
			}
			
			$PMT5577DN_col = NULL;
			$PMT6300DN_col = NULL;
			$PMT4278DN_col = NULL;
			$PD1DN_col = NULL;
			$PD2DN_col = NULL;
			$PMT5577rad_col = NULL;
			$PMT6300rad_col = NULL;
			$PMT4278rad_col = NULL;
			$PD1rad_col = NULL;
			$PD2rad_col = NULL;
			
			unset($PMT5577DN_col, $PMT6300DN_col, $PMT4278DN_col, $PD1DN_col, $PD2DN_col, $PMT5577rad_col, $PMT6300rad_col, $PMT4278rad_col, $PD1rad_col, $PD2rad_col);
		}
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