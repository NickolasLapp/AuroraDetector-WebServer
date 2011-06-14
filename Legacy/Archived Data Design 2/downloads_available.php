<?php
	$from_date = ($_REQUEST['from'])/1000;
	$to_date = ($_REQUEST['to'])/1000;
	
	// Determine which date UTC the passed in local timestamps correspond to...
	$from_date_UTC = gmmktime(0,0,0, gmdate("n, j, Y", $from_date));
	$to_date_UTC = gmmktime(0,0,0, gmdate("n, j, Y", $to_date));
	
	echo '<script type="text/javascript">';
	echo 'console.log(Passed in: from: '.$from_date.' to: '.$to_date.');';
	echo 'console.log(UTC: from: '.$from_date_UTC.' to: '.$to_date_UTC.');';
	echo '</script>';
	
	// Set up the arrays of detector's ID's and names
	$detectors = array('BCC1','CDK1','FBC1','FPC1','LBH1','BZN2','SKC1','SCC1');
	$names = array('Blackfeet Community College','Chief Dull Knife College','Fort Belknap College','Fort Peck Community College','Little Big Horn College','Montana State University','Salish Kootenai College','Stone Child College');
	
	// Set the default timezone used in all mktime calls (not sure if this is needed after swiching to using gmmktime instead, but I'm keeping it there anyway for the time being)
	date_default_timezone_set('UTC');	
	
	$jj = 0; // Counter for the number of detectors that have data in the requested time range
	for ($ii = 0; $ii < count($detectors); $ii++) { // Loop through all of the detectors
		$ID = $detectors[$ii];
		$name = $names[$ii];
		// Set up the path to the data files the current detector
		$path = 'data/'.$ID; 
		$dir_handle = @opendir($path) or die("Cannot open the data folder for detector ID: ".$ID);
		// Read in all of the file names in that folder
		while ($file = readdir($dir_handle)) { 
			// Look for files in the YYYY-MM-DD.txt format
			if (preg_match('/^(\d{4}-\d{2}-\d{2}.txt)/', $file) ) { 
				// disregard the 0000-00-00.txt file
				if (substr($file,0,4)!=0) {
					// Check if the file is in the requested range
					if ( ( gmmktime(0, 0, 0, substr($file,5,2), substr($file,8,2), substr($file,0,4)) >= $from_date_UTC) 
						 && ( gmmktime(0, 0, 0, substr($file,5,2), substr($file,8,2), substr($file,0,4)) <= $to_date_UTC) ) {
						// Add it to the array of files available for that detector if it is.
						$files_list[$ID][] = $file;		
					}
				}	
			}	
		}
		
		// If files were found in the requested date range for the current detector, show it's download link in the modal dialog and add 1 to the counter			
		if (count($files_list[$ID]) != 0) {
			echo '<script type="text/javascript">';
			echo '$("#'.$ID.'").show();';
			echo '</script>';
			$jj++;
		}
	}

	// If no data files were found for the detector (This should only happen in testing and not durring deployment)
	if ($jj == 0) {
		echo "<p><b>No data are available for the date";
		if ($from_date != $to_date) {
			echo "s";
		}
		echo " you specified.</b></p>";
		echo '<script type="text/javascript">';
			echo '$("#download_status").text("Please try again.");';
		echo '</script>';
	} else {
		echo '<br/>';
	}
?>