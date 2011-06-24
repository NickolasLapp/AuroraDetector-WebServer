<?php
	include('dataPaths.inc');
	// Create an array of the detectors
	$detectors = array();
	$detectors['abbreviations'] = array('MSU', 'SKC', 'LBHC', 'FPCC', 'FBC', 'CDKC', 'SCC', 'BCC');
	$detectors['names'] = array('Montana State University', 'Salish Kootenai College', 'Little Big Horn College', 'Fort Peck Community College', 'Fort Belknap College', 'Chief Dull Knife College', 'Stone Child College', 'Blackfeet Community College');
	$detectors['coordinates'] = array('-111.045955555556,45.6666027777778,0', '-114.107188, 47.596867,0', '-107.456703, 45.603839,0', '-105.193191, 48.113830,0', '-108.759900, 48.482213,0', '-106.666902, 45.622809,0', '-109.783128, 48.257473,0', '-113.010239, 48.548064,0');
	$detectors['IDs'] = array('BZN2', 'SKC1', 'LBH1', 'FPC1', 'FBC1', 'CDK1', 'SCC1', 'BCC1');

	$column_headings = array('date','local','BZN2_PMT5577DN','BZN2_PMT6300DN','BZN2_PMT4278DN','BZN2_PD1DN','BZN2_PD2DN','SKC1_PMT5577DN','SKC1_PMT6300DN','SKC1_PMT4278DN','SKC1_PD1DN','SKC1_PD2DN','LBH1_PMT5577DN','LBH1_PMT6300DN','LBH1_PMT4278DN','LBH1_PD1DN','LBH1_PD2DN','FPC1_PMT5577DN','FPC1_PMT6300DN','FPC1_PMT4278DN','FPC1_PD1DN','FPC1_PD2DN','FBC1_PMT5577DN','FBC1_PMT6300DN','FBC1_PMT4278DN','FBC1_PD1DN','FBC1_PD2DN','CDK1_PMT5577DN','CDK1_PMT6300DN','CDK1_PMT4278DN','CDK1_PD1DN','CDK1_PD2DN','SCC1_PMT5577DN','SCC1_PMT6300DN','SCC1_PMT4278DN','SCC1_PD1DN','SCC1_PD2DN','BCC1_PMT5577DN','BCC1_PMT6300DN','BCC1_PMT4278DN','BCC1_PD1DN','BCC1_PD2DN');

	$school_colors = array("00008C"=>'BZN2', "008000"=>'SKC1', "FFD700"=>'LBH1', "FF0000"=>'FPC1', "800000"=>'FBC1', "3030FF"=>'CDK1', "640080"=> 'SCC1', "000000"=>'BCC1');
	
	$charts_possible = array("Green (557.7nm)"=>'5577', "Red (630.0nm)"=>'6300', "Blue (427.8nm)"=>'4278', "White"=>'PD1', "White 2"=>'PD2');
	
	$mountain_timezone = new DateTimeZone('America/Denver');
	$UTC_timezone = new DateTimeZone('UTC');

	
	function get_data($date) {
		// This file accepts the date in the format YYYY-MM-DD and returns all of the data from all of the detectors from that date
		
		global $column_headings, $mountain_timezone, $UTC_timezone, $detectors, $charts_possible, $school_colors, $DATA_DIR;
		
		// Initialize the temporary data array
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
		
			$handle = fopen("$DATA_DIR".$ID.'/'.$date.'.txt',"r");
			
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
		
		// Sort the resulting data array so that it is organized from oldest data to newest
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
		
		// This is only temporary until I figure out how to have multiple x axis values
		unset($output_data['local'], $output_columns[1]);
		
		unset($data_array, $max_PMT5577DN, $max_PMT6300DN, $max_PMT4278DN, $max_PD1DN, $max_PD2DN, $UTC_time, $local_time, $current_column, $offset_ID, $offset, $ID, $ii, $jj);
		
		$output['columns'] = $output_columns;
		$unformatted_output = array();
		foreach ($output_data as $column) {
			$temp_data = array_combine($output_data['date'], $column);
			$unformatted_output = array_merge_recursive($unformatted_output, $temp_data);
			unset($temp_data);
		}
		
		foreach ($unformatted_output as $row) {
			$output_rows[] = implode(',', $row);
		}
		
		$output['data'] = implode("\n", $output_rows);
		
		unset($unformatted_output, $output_rows, $row, $column);
		
		foreach ($charts_possible as $chart_title => $needle) {
			foreach ($output['columns'] as $haystack) {
				if (strlen(strstr($haystack, $needle))>0) {
					$output['charts'][$needle]['title'] = $chart_title;
					$output['charts'][$needle]['columns'][] = $haystack;
					
					$ii = 0;
					foreach ($school_colors as $color => $school) {
						if (strlen(strstr($haystack, $school))>0) {
							$output['charts'][$needle]['colors'][] = $color;
							$output['charts'][$needle]['schools'][] = $detectors['abbreviations'][$ii];
						}
						$ii++;
					}
				}
			}
		}
		
		unset($needle, $haystack, $chart_title, $color, $school);
		
		return $output;
	}
	
?>