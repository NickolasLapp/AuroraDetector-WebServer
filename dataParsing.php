<?php
$DATA_DIR = '//ORSL-MAXWELL/Data--Auxiliary/Aurora_Network/';
$month = date('m');
$day = date('d');
$year = date('Y');

function getRecentAuroraData($auroraData, $detectorID)
{
	$file = '//ORSL-MAXWELL/Data--Auxiliary/Aurora_Network/'.$detectorID.'/'."delete".'.txt';
	if( file_exists($file) ){
    	$handle = fopen($file,"r");
    	while($data = fgetcsv($handle, 1024, ',')){}
		if ($auroraData == "PMT")
			return $data[6];
		else if ($auroraData == "PD")
			return $data[7];
	}
	else
		return $file;
}

function getEntireAuroraData($detectorID) {
	$file = '//ORSL-MAXWELL/Data--Auxiliary/Aurora_Network/'.$detectorID.'/'."delete".'.txt';
	if( file_exists($file) ){
		$handle = fopen($file,"r");
		while($data = fgetcsv($handle, 1024, ',')){
			$array[] = $data[6];
		}
		return $array;
	}
}

function getRecentPMTAuroraData($detectorID)
{
	return getRecentAuroraData("PMT", $detectorID);
}

function getRecentPDAuroraData($detectorID)
{
	return getRecentAuroraData("PD", $detectorID);
}

?>