<?php
include_once('dataPaths.inc');
$month = date('m');
$day = date('d');
$year = date('Y');

function getRecentAuroraData($auroraData, $detectorID)
{
	$file = $DATA_DIR."/$detectorID/".date('Y-m-d', mktime(0,0,0,$month,$day,$year)).".txt";
	if( file_exists($file) ){
    	$handle = fopen($file,"r");
    	while($data = fgetcsv($handle, 1024)){}
		if ($auroraData == "PMT")
			return $data[6];
		else if ($auroraData == "PD")
			return $data[7];
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