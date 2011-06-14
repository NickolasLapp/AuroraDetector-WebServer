<?php
	// This file currently works for small date ranges but fails for large ones.
	//Also, there is a problem with the ZipArchive class in PHP that causes the resulting archives to have problems when opened with window's default zip program.
	
	$ids = $_REQUEST['ids'];
	$from_date = ($_REQUEST['from'])/1000;
	$to_date = ($_REQUEST['to'])/1000;
	
	$detectors = explode('-',$ids); 
	//echo count($detectors).' detectors selected<br>';
	$archive =  tempnam('/tmp', 'temp_zip_file');
	$zip = new ZipArchive;
	$zip_open_test = $zip->open($archive, ZipArchive::OVERWRITE);
	if ($zip_open_test !== true) {
		echo 'Error: Zip archive cannot be created.<br>';
	}
	
	for ($ii = 0; $ii < count($detectors); $ii++) {	
		$ID = $detectors[$ii];
		//echo 'Checking for files for ID: '.$ID.'<br>';
		$path = 'data/'.$ID;
		$dir_handle = @opendir($path) or die("Error: Cannot open the data folder for detector id: ".$ID."<br>");
		while ($file = readdir($dir_handle)) {
			if (preg_match('/^(\d{4}-\d{2}-\d{2}.txt)/', $file) ) {
				if (substr($file,0,4)!=0) {
					if ( ( mktime(0, 0, 0, substr($file,5,2), substr($file,8,2), substr($file,0,4)) >= $from_date)
						 && ( mktime(0, 0, 0, substr($file,5,2), substr($file,8,2), substr($file,0,4)) <= $to_date) ) {
						$files_list[$ID][] = $file;					
					}
				}	
			}	
		}
		if (count($files_list[$ID]) == 0) {
			echo 'Error: No data exists for detector id: '.$ID.'\r\n';
		} else {
			$temp = tmpfile();
			$temp_files_created[$ID] = $temp;
			fwrite($temp, "UTC Timestamp,PMT Digital Number,PD Digital Number\r\n");
			for ($jj = 0; $jj < count($files_list[$ID]); $jj++) {
				$file_name = $files_list[$ID][$jj];
				$handle = fopen($path.'/'.$file_name,"r");
				while($data = fgetcsv($handle, 1024)){
					if (((strtotime($data[0])) !== false) && ($data[0] != 'UTC')) {
						fwrite($temp, $data[0].",");
						fwrite($temp, $data[1].",");
						fwrite($temp, $data[2]."\r\n");
					}
				}
			}
			fseek($temp, 0);
		}
	}
	
	for ($ii = 0; $ii < count($detectors); $ii++) {
		$ID = $detectors[$ii];
		$current_file_name = $ID.'_data.csv';
		$file_contents = fread($temp_files_created[$ID], 1024);
		$test_add = $zip->addFromString($current_file_name, $file_contents);
		echo $file_contents;
		unlink($file_contents);
		//$test_add = $zip->addFile($temp_files_created[$ID], $current_file_name);
		if ($test_add !== true ) {
			echo 'Error: File could not be added to zip archive.<br>';
		} 
	}
	
	$test_close = $zip->close();
	
	if ($test_close === true) {
		header("Content-type: application/zip");
		header('Content-Disposition: attachment; filename="Aurora Data.zip"');
		header("Pragma: no-cache");
		header("Expires: 0");
		readfile($archive);
	} else {
		echo 'Error: Zip archive file close error. Cannont download file.';
	}
	
	// Close all of the temp files
	for ($ii = 0; $ii < count($detectors); $ii++) {
		fclose($temp_files_created[$ID]);
	} 
	fclose($archive); 
?>