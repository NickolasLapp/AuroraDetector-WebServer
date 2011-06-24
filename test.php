<?php
	include_once 'dataParsing.php';
	include_once 'constants.php';
/*  $detectorID = $detectors['IDs'][0];
	$month = date('m');
$day = date('d');
$year = date('Y');
	echo $DATA_DIR.$detectorID.'/'.date('Y-m-d', mktime(0,0,0,$month,$day,$year)).'.txt';*/


//echo getRecentPMTAuroraData("BZN2");
$array = getEntireAuroraData("BZN2");
for ($i = 0; $i < sizeof($array); $i++) {
	echo $array[$i]."\n";
}
?>