<?php
	$from_date = ($_REQUEST['from']);
	$to_date = ($_REQUEST['to']);
	
	echo "From: ".$from_date." To: ".$to_date."<br>";
	
	$from_date_string = date('Y-m-d', $from_date);
	$to_date_string = date('Y-m-d', $to_date+86399);
	
	if ($from_date == $to_date) {
		echo 'Data for '.$from_date_string.'<br/>';
	} else {
		echo 'Data from '.$from_date_string.' to '.$to_date_string.'<br/>';	
	}
	
	$settings_link = htmlspecialchars('chart-settings.php?from='.$from_date.'&to='.$to_date);
	echo $settings_link;
?>