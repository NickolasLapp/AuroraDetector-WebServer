<?php
	include('dataPaths.inc');
	$UTC_timezone = new DateTimeZone('UTC');
	$currentDate = date("mdY");

/*
Opens a CSV file and outputs an entire column
*/
function processCSV($date, $ID, $columns) {
	global $DATA_DIR;
	$dir_handle = @opendir($DATA_DIR.$ID) or die("Error: Cannot open the data folder for detector id: ".$ID); 
	$handle = fopen($DATA_DIR.$ID.'/'.$date.'.csv',"r");
	
	$skipFirstHeader = TRUE;
	$useSecondHeaderInput = TRUE;
	while($data = fgetcsv($handle, 0)) {
		if ($skipFirstHeader)
			$skipFirstHeader = FALSE;
		else {
			foreach($columns as $value) {
				$outputData[] = $data[$value];
			}
			$output = implode(',', $outputData);
			$meta["data"][] = $output;
			if ($useSecondHeaderInput) {
				$useSecondHeaderInput = FALSE;
				$meta["time"][] = "Time in UTC";
			}
			else
				$meta["time"][] = date("Y-m-d H:i", strtotime($data[0]));
			unset($outputData);
		}
	}
	fclose($handle);
	return $meta;
	
}	


/*
Checks to see if there is a data file on this date
in the detector directories
*/
function doesDateFileExist($ID, $date) {
	global $DATA_DIR;
	$handle = fopen($DATA_DIR.$ID.'/'.$date.'.csv',"r");
	if ($handle === FALSE) {
		fclose($handle);
		return FALSE;
	}
	else {
		fclose($handle);	
		return TRUE;
	}
}

/*
Reads in the detector directories and outputs a 
an array of ID's.
*/
function getDetectorIDs() {
	global $DATA_DIR;
	if ($handle = opendir($DATA_DIR)) {
		while (FALSE !== ($file = readdir($handle))) {
			if (strlen($file) == 4 && is_numeric(substr($file, -1))) {
				$meta[] = $file;
			}
		}
	}
	closedir($handle);
	return $meta;
}

function getData($date) {
	$columns = func_get_args();
	array_shift($columns);
	$meta = buildDataArray($date, $columns);
	
}

/*
Main function to retrieve an aurora array based on date.

@param 		the date to be used.
@param... 	the columns to output.
*/
private function buildDataArray($date, $columns) {
	$directoriesForDetectors = getDetectorIDs();
	foreach ($directoriesForDetectors as $ID) {
		if (doesDateFileExist($ID, $date)) {
			$activeDetector[] = $ID;
		}
	}
	foreach ($activeDetector as $ID) {
		$outputColumns[$ID] = processCSV($date, $ID, $columns);
	}
	return $outputColumns;
}
?>