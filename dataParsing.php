<?php
include_once('constants.php');

function getRecentAuroraData()
{
	$month = date('m');
	$day = date('d');
	$year = date('Y');
	$file = $DATA_DIR.date('Y-m-d', mktime(0,0,0,$month,$day,$year)).".txt";
	if( file_exists($file) ){
    	$handle = fopen($file,"r");
    	while($data = fgetcsv($handle, 1024)){
			$time[] = $data[0];
			
		}
	}
}
?>