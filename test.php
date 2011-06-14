<?php
	include_once 'constants.php';
	
	echo 'This is a test of the get_data function for 2010-09-17<br>';
	
	$data = get_data('2010-09-17');
	echo 'The columns are numbered as follows<br>';
	print_r($data['columns']);
	
	/*
	$final_data = array();
	foreach ($data['data'] as $column) {
		$temp_data = array_combine($data['data']['date'], $column);
		$final_data = array_merge_recursive($final_data, $temp_data);
	}
	
	//$temp_data[] = implode(',', $temp_data_line);
	
	//$csv_formatted_data = implode("\r\n", $temp_data);
	*/
	echo 'data: <br>';
	print_r($data['data']);
	
?>