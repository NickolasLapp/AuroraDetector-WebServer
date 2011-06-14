<?php	
	// Set up this array so that future detectors can be added just by adding their callsign into the detectors array
	$detectors = array('BCC1','CDK1','FBC1','FPC1','LBH1','BZN2','SKC1','SCC1');
	
	// Look through all of the file folders of data and add each valid file's date to the days_list array
	for ($ii = 0; $ii < count($detectors); $ii++) {
		$ID = $detectors[$ii];
		$path = '../data/'.$ID;
		$dir_handle = @opendir($path) or die("Cannot open the data folder for ".$ID.'.');
		while ($file = readdir($dir_handle)) {
			// Use this regular expression to look for valid data files in the form 'YYYY-MM-DD.text'
			if (preg_match('/^(\d{4}-\d{2}-\d{2}.txt)/', $file) ) {
				// Don't grab the 0000-00-00.txt file
				if (substr($file,0,4)!=0) {	
					// Add the date from the file
					$java_month = intval(substr(date('Ymd', strtotime(substr($file,0,10))),4,2)) - 1;
					if ($java_month < 10) {
						$java_month = '0'.$java_month;
					}
					$days_list[] = substr(date('Ymd', strtotime(substr($file,0,10))),0,4).$java_month.substr(date('Ymd', strtotime(substr($file,0,10))),6,2);
				}
			}
		}
	}
	
	// Now get rid of repeated days and sort them
	$unique_dates = array_unique($days_list);
	sort($unique_dates);
	
	// Create the javascript array dates_available
	echo 'var dates_available = new Array();';
	
	// Add the unique UTC dates to the javascript dates_available array
	foreach ($unique_dates as $day){
		echo 'dates_available.push("'.$day.'");';
	}
?>

// Now create local date objects from the UTC data from the server
var local_dates = new Array();
for (var ii = 0; ii < dates_available.length; ii++) {
	// Look at the local time equivalent of 00:00 and 23:59 UTC 
	for (var jj = 0; jj<2; jj++){
		switch(jj) {
			case 0:	var temp_local_date = (new Date(Date.UTC(dates_available[ii].substring(0,4), dates_available[ii].substring(4,6), dates_available[ii].substring(6), 0, 0, 0))); break;
			case 1: var temp_local_date = (new Date(Date.UTC(dates_available[ii].substring(0,4), dates_available[ii].substring(4,6), dates_available[ii].substring(6), 23, 59, 0))); break;
		}
		
		var temp_year = temp_local_date.getFullYear();
		var temp_month = temp_local_date.getMonth();
		var temp_day = temp_local_date.getDate();
		
		if (temp_month < 10) {
			temp_month = '0'+(temp_month+'');
		}
		
		if (temp_day < 10) {
			temp_day = '0'+(temp_day+'');
		}
		//console.log(temp_year+''+temp_month+''+temp_day);
		local_dates.push(temp_year+''+temp_month+''+temp_day);
	}
}

function restrict_dates(date) {
	var test_year = date.getFullYear();
	var test_month = date.getMonth();
	var test_day = date.getDate();
	
	if (test_month < 10) {
		test_month = '0'+test_month;
	}
	
	if (test_day < 10) {
		test_day = '0'+test_day;
	}
	
	var test_string = test_year+''+test_month+''+test_day;
	
	from_date_input = $("#from_date").datepicker("getDate");
	to_date_input = $("#to_date").datepicker("getDate");
	
	if (local_dates.indexOf(test_string) != -1) {
		return [true, ''];	
	} else {
		return [false, ''];
	}
}

function restrict_range(date_input) {
	if ( $('#one_day_radio').is(':checked') ) {
		return {
			minDate: oldest_date(),
			maxDate: newest_date()
		}
	} else {
		if (date_input.id == 'to_date') {
			return {
				minDate: $('#from_date').datepicker("getDate"),
				maxDate: newest_date()
			};
		} else if (date_input.id == 'from_date') {
			return {
				maxDate: $('#to_date').datepicker("getDate"),
				minDate: oldest_date()
			};
		}	
	}
}

function oldest_date() {
	var beginning_date = local_dates[0];
	return (new Date(beginning_date.substring(0,4), beginning_date.substring(4,6), beginning_date.substring(6,8), 0, 0, 0));
}

function newest_date() {
	var most_current_date = local_dates[local_dates.length-1];
	return (new Date(most_current_date.substring(0,4), most_current_date.substring(4,6), most_current_date.substring(6,8), 0, 0, 0));
}

function check_dates_input() {
	if ( $('#one_day_radio').is(':checked') ) {
		// Get time object from datepicker
		from_date = $("#from_date").datepicker( "getDate" ); 
		var from_offset = (from_date.getTimezoneOffset())*60*1000; // Timezone offset in milliseconds
		
		// Covert this to timestamp form (ms since unix epock) and then normalize to UTC time
		var data_from_date = (from_date.getTime())-from_offset;
		var data_to_date = data_from_date;
	} else {
		// First check the dates for the correct progression (from < to) while still in local time
		from_date = $("#from_date").datepicker( "getDate" );
		to_date = $("#to_date").datepicker( "getDate" );
		
		// Calculate offsets for both entered times to handle DST
		var from_offset = (from_date.getTimezoneOffset())*60*1000;
		var to_offset = (to_date.getTimezoneOffset())*60*1000;
		
		var data_from_date = (from_date.getTime())-from_offset;
		var data_to_date = (to_date.getTime())-to_offset;
		/*
		if (data_from_date > data_to_date) {
			// Flip the dates around so that from < to
			temp_date = from_date;
			from_date = to_date;
			to_date = temp_date;
			$("#from_date").datepicker("setDate", from_date);
			$("#to_date").datepicker("setDate", to_date);
			
			// Redefine data_from_date and data_to_date to reflect the change as well 
			var from_offset = (from_date.getTimezoneOffset())*60*1000;
			var to_offset = (to_date.getTimezoneOffset())*60*1000;
			
			var data_from_date = (from_date.getTime())-from_offset;
			var data_to_date = (to_date.getTime())-to_offset;
		}
		*/			
	}
	return [data_from_date, data_to_date];
}

function check_for_data() {
	var dates = check_dates_input();
	$("#downloads_available").load('downloads_available.php?from='+dates[0]+'&to='+dates[1]);
}

function day_range_change() {
	if ( $('#one_day_radio').is(':checked') ) {
		$('#start_text').text('Date: ');
		$('#end_text').hide();
		$('#to_date').datepicker( "disable" );
	} else {
		$('#start_text').text('Start: ');
		$('#end_text').show();
		$('#to_date').datepicker( "enable" ).datepicker("setDate", $("#from_date").datepicker("getDate") );
	}
}

function load_chart(tz) {
	var dates = check_dates_input();
	if ( (dates[0] != null) && (dates[1] != null) ) {
		//$("#chart").load('chart.php?from='+data_from_date+'&to='+data_to_date);
		var submit_time = new Date().getTime();
		$("#chart").load('data-new.php?id=BZN2&from='+dates[0]+'&to='+dates[1]+'&tz='+tz+'&submitted='+submit_time);
	}
}