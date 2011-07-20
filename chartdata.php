<?php

	$date = $_REQUEST['date'];
	
	include('constants.php');
	
	$aurora_data = get_data($date, FALSE);
	
echo $aurora_data['data'];

?>