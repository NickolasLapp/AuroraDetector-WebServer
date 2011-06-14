<?php
	$ID = $_REQUEST['ID'];
	$path = 'data/'.$ID;
	$dir_handle = @opendir($path) or die("Cannot open the data folder for detector id: ".$ID);
	// Loop through the data files
	while ($file = readdir($dir_handle)) {
		if (preg_match('/^(\d{4}-\d{2}-\d{2}.txt)/', $file) ) {
			if (substr($file,0,4)!=0) {
				$dates_list[] = substr($file,0,4).substr($file,5,2).substr($file,8,2);
				$months_list[] = substr($file,0,4).substr($file,5,2);
			}	
		}	
	}
	$months_list = array_unique($months_list);
	rsort($dates_list);
	rsort($months_list);
?>

<div class="ui-widget-header ui-corner-top">
	<div class="data_header"><h5>Daily</h5></div>
	<div class="data_header"><h5>Monthly</h5></div>
</div>
<div class="bordered_all ui-corner-bottom">
	<div class="data_list bordered_right">
		<?php
			/* Display Dates and Links */
			foreach ($dates_list as $date) {
				echo substr($date,0,4).'/'.substr($date,4,2).'/'.substr($date,6,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;download<br>';	
			}
		?>
	</div>
	
	<div class="data_list">
		<?php
			/* Display Months and Links */
			foreach ($months_list as $date) {
				echo substr($date,0,4).'/'.substr($date,4,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;download<br>';	
			}
		?>
	</div>
</div>