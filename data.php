<?php
	// This file accepts the date in the format YYYY-MM-DD and returns all of the data from all of the detectors from that date
	$date = $_REQUEST['date'];
	
	include_once 'constants.php';
	
	$mountain_timezone = new DateTimeZone('America/Denver');
	$UTC_timezone = new DateTimeZone('UTC');
	
	//header("Content-Type: application/download");
	//header("Content-Disposition: attachment; filename=\"".$date.".csv\"");
	
	// Initialize the data array
	$data_array = array();
	
	// Set up the column headers
	$data_array[0][0] = "date";
	$data_array[0][1] = "local";
	foreach($detectors['IDs'] as $ID) {
		$offset_ID = $ID.'_offset';
		$$offset_ID = count($data_array[0]);
		$data_array[0][$$offset_ID] = "{$ID}_PMT5577DN";
		$data_array[0][$$offset_ID+1] = "{$ID}_PMT6300DN";
		$data_array[0][$$offset_ID+2] = "{$ID}_PMT4278DN";
		$data_array[0][$$offset_ID+3] = "{$ID}_PD1DN";
		$data_array[0][$$offset_ID+4] = "{$ID}_PD2DN";
	}
	
	foreach($detectors['IDs'] as $ID) {
		$offset_ID = $ID.'_offset';
		$offset = $$offset_ID;
	
		$dir_handle = @opendir('data/'.$ID) or die("Error: Cannot open the data folder for detector id: ".$ID); 
	
		$handle = fopen('data/'.$ID.'/'.$date.'.txt',"r");
		
		
		if ($handle === FALSE) {	
			// If the file cannot be opened, add a set of nulls for the requested date
			$UTC_time = DateTime::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00', $UTC_timezone);
			$local_time = DateTime::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00', $UTC_timezone);
			$local_time->setTimezone($mountain_timezone);
			
			if ($data_array[$UTC_time->format('Y-m-d H:i')][0] == NULL) {
				for ($jj = 0; $jj < 42; $jj++) {
					$data_array[$UTC_time->format('Y-m-d H:i')][$jj] = NULL;
				}
				$data_array[$UTC_time->format('Y-m-d H:i')][0] = $UTC_time->format('Y-m-d H:i');
				$data_array[$UTC_time->format('Y-m-d H:i')][1] = $local_time->format('Y-m-d H:i');	
			}
			
			$data_array[$UTC_time->format('Y-m-d H:i')][$offset] = NULL;
			$data_array[$UTC_time->format('Y-m-d H:i')][$offset+1] = NULL;
			$data_array[$UTC_time->format('Y-m-d H:i')][$offset+2] = NULL;
			$data_array[$UTC_time->format('Y-m-d H:i')][$offset+3] = NULL;
			$data_array[$UTC_time->format('Y-m-d H:i')][$offset+4] = NULL;
		
		} else {
			// Else add all of the data in the time range
			while($data = fgetcsv($handle, 1024)){
				if (strtotime($data[0]) !== FALSE ) {
					if ($data[0] == 'UTC') {
						$UTC_col = 0;
						for ($ii = 1; $ii < count($data); $ii++) {
							//UTC, PMT5577DN, PMT6300DN, PMT4278DN, PD1DN, PD2DN, PMT5577rad, PMT6300rad, PMT4278rad, PD1rad, PD2rad
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
						$UTC_time = DateTime::createFromFormat('Y-m-d H:i:s', $data[0], $UTC_timezone);
						$local_time = DateTime::createFromFormat('Y-m-d H:i:s', $data[0], $UTC_timezone);
						$local_time->setTimezone($mountain_timezone);
						
						if ($data_array[$UTC_time->format('Y-m-d H:i')][0] == NULL) {
							for ($jj = 0; $jj < 42; $jj++) {
								$data_array[$UTC_time->format('Y-m-d H:i')][$jj] = NULL;
							}
							$data_array[$UTC_time->format('Y-m-d H:i')][0] = $UTC_time->format('Y-m-d H:i');
							$data_array[$UTC_time->format('Y-m-d H:i')][1] = $local_time->format('Y-m-d H:i');	
						}
						

						if ($data_array[$UTC_time->format('Y-m-d H:i')][$offset] == NULL) {
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset] = $data[$PMT5577DN_col];
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset+1] = $data[$PMT6300DN_col];
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset+2] = $data[$PMT4278DN_col];
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset+3] = $data[$PD1DN_col];
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset+4] = $data[$PD2DN_col];
						} else {
							// Take the max of the values in the minute.  This will hopefully give a better representation of a time-varying aurora.
							$max_PMT5577DN = max($data_array[$UTC_time->format('Y-m-d H:i')][2], $data[$PMT5577DN_col]);
							$max_PMT6300DN = max($data_array[$UTC_time->format('Y-m-d H:i')][3], $data[$PMT6300DN_col]);
							$max_PMT4278DN = max($data_array[$UTC_time->format('Y-m-d H:i')][4], $data[$PMT4278DN_col]);
							$max_PD1DN = max($data_array[$UTC_time->format('Y-m-d H:i')][5], $data[$PD1DN_col]);
							$max_PD2DN = max($data_array[$UTC_time->format('Y-m-d H:i')][6], $data[$PD2DN_col]);
							//$data_array[$UTC_time->format('Y-m-d H:i')] = array($UTC_time->format('Y-m-d H:i'), $local_time->format('Y-m-d H:i'), $max_PMT5577DN, $max_PMT6300DN, $max_PMT4278DN, $max_PD1DN, $max_PD2DN);
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset] = $max_PMT5577DN;
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset+1] = $max_PMT6300DN;
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset+2] = $max_PMT4278DN;
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset+3] = $max_PD1DN;
							$data_array[$UTC_time->format('Y-m-d H:i')][$offset+4] = $max_PD2DN ;
						}
					}
				}
			}
			unset($PMT5577DN_col, $PMT6300DN_col, $PMT4278DN_col, $PD1DN_col, $PD2DN_col, $PMT5577rad_col, $PMT6300rad_col, $PMT4278rad_col, $PD1rad_col, $PD2rad_col);
		}
	}	
	
	// Sort the resulting data array so that it is organized from 
	ksort($data_array);
	
	// Convert the large data array into separate array for each channel.
	// This breaks the association of the timestamp keys needed for the time averaging but allows for the easy removal of un-needed columns.
	$output_data = array();
	$ii = 0;
	foreach ($data_array as $row) {
		for ($jj=0; $jj<count($row); $jj++) {
			$current_column = $column_headings[$jj];
			if ($ii > 0) {
				$output_data[$current_column][] = $row[$jj];
			}
		}
		$ii++;
	}
	
	$output_columns = array();
	
	// Now get rid of the un-needed columns
	foreach ($column_headings as $current_column) {
		if (max($output_data[$current_column]) == NULL) {
			unset($output_data[$current_column]);
		} else {
			$output_columns[] = $current_column;
		}
	}
	
	//print_r($output_data);
	
	$output['columns'] = $output_columns;
	$output['data'] = $output_data;
	
	print_r($output);
?>