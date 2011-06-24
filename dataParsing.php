<?php
include('dataPaths.inc');

function getEntireAuroraData($dataType, $detectorID) {
	global $DATA_DIR;
	$file = $DATA_DIR.$detectorID.'/'.date('Y-m-d', mktime(0,0,0,date('m'),date('d'),date('Y'))).'.txt';
	if( file_exists($file) ){
		$handle = fopen($file,"r");
		
		if ($dataType == "PMT")
			$ii = 6;
		else if ($dataType == "PD")
			$ii = 7;
		
		while($data = fgetcsv($handle, 1024, ',')){
			$array[] = $data[$ii];
		}
		return $array;
	}
}

function getRecentPMTAuroraData($detectorID)
{
	$array = getEntireAuroraData("PMT", $detectorID);
	return $array[sizeof($array) - 1];
}

function getRecentPDAuroraData($detectorID)
{
	$array = getEntireAuroraData("PD", $detectorID);
	return $array[sizeof($array) - 1];
}

?>