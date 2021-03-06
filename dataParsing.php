<?php
include('dataPaths.inc');

function getEntireAuroraData($dataType, $detectorID) {
	global $DATA_DIR;
	$file = $DATA_DIR.$detectorID.'/'.date('Y-m-d').'.csv';
	if( file_exists($file) ){
		$handle = fopen($file,"r");
		
		if ($dataType == "PMT")
			$ii = 6;
		else if ($dataType == "PD")
			$ii = 7;
		
		while($data = fgetcsv($handle, 1024, ',')){
			$array[] = $data[$ii];
		}
		fclose($handle);
		return $array;
	}
	else
		return "";
}
function getRecentPMTAuroraData($detectorID) {
	$array = getEntireAuroraData("PMT", $detectorID);
	if ($array != "")
		return $array[sizeof($array) - 1];
	else
		return "";
}

function getRecentPDAuroraData($detectorID) {
	$array = getEntireAuroraData("PD", $detectorID);
	if ($array != "")
		return $array[sizeof($array) - 1];
	else
		return "";
}

?>