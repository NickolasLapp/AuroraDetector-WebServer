<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<body>
<?php
	include ("auroraFunctions.php");
	
	$detectors = array();
	
	$detectors['names'] = array('Montana State University', 'Salish Kootenai College', 'Little Big Horn College', 'Fort Peck Community College', 'Fort Belknap College', 'Chief Dull Knife College', 'Stone Child College', 'Blackfeet Community College');
	
	$detectors['coordinates'] = array('-111.045955555556,45.6666027777778,0', '-114.107188, 47.596867,0', '-107.456703, 45.603839,0', '-105.193191, 48.113830,0', '-108.759900, 48.482213,0', '-106.666902, 45.622809,0', '-109.783128, 48.257473,0', '-113.010239, 48.548064,0');
	
	$detectors['IDs'] = array('BZN2','SKC1','LBC1','FPC1','FBC1','CDK1','SCC1','BCC1');

	print_r($detectors);
	
	$data = call_user_func('load_data_summary', 'BZN2');
	$last_time_stamp = $data["date"]." ".$data["time"];
	echo "".$last_time_stamp."<br>";
	
	echo "".$data["PMT_rad"]." uW/m^2*Sr<br>";
?>
</body>
</html>
