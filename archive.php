<?php
	//$ID = $_REQUEST['ID'];
	include("dataPaths.inc");
	date_default_timezone_set('America/Denver');

	$detectors = array('BCC1','CDK1','FBC1','FPC1','LBH1','BZN2','SKC1','SCC1');
	
	for ($ii = 0; $ii < count($detectors); $ii++) {
		$ID = $detectors[$ii];
		$path = $DATA_DIR.$ID;
		$dir_handle = @opendir($path) or die("Cannot open the data folder for detector id: ".$ID);
		// Loop through the data files
		while ($file = readdir($dir_handle)) {
			if (preg_match('/^(\d{4}-\d{2}-\d{2}.csv)/', $file) ) {
				if (substr($file,0,4)!=0) {
					$date = substr($file,0,4).'-'.substr($file,5,2).'-'.substr($file,8,2);
					$dates_list[] = $date;
					$detectors_with_data[$date][] = $ID;
				}	
			}	
		}
		$dates_list = array_unique($dates_list);
		rsort($dates_list);
	}
?>

<div>
	<?php		
		/* Display Dates and Links */
		echo '<table border="0" width="100%">';
		
		foreach ($dates_list as $date) {
			echo '<tr> <td width="20%"><a class="chart_link" href="chart_page.php?date='.$date.'">'.$date.'</a></td> <td width="10%">download: </td>';
			foreach($detectors_with_data[$date] as $detector) {
				echo '<td width="10%"><a href="data/'.$detector.'/'.str_replace('/', '-', $date).'.csv">'.$detector.'</a></td>';
			}	
		}
		echo '</table>';
	?>
</div>


