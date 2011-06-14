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


localize_days();

// Ensure that the most current day is the first one displayed
var current_day = total_days;

function localize_days(tz) {
	// Now create local date objects from the UTC data from the server
	local_dates = new Array();
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
	
	local_dates = unique(local_dates);
	
	total_days = local_dates.length-1;	
}

function unique(arrayName) {
    // this function was copied from: http://www.roseindia.net/java/javascript-array/javascript-array-unique.shtml
	var newArray = new Array();
    label:for(var i=0; i<arrayName.length;i++ )
    {  
        for(var j=0; j<newArray.length;j++ )
        {
            if(newArray[j]==arrayName[i]) 
                continue label;
        }
        newArray[newArray.length] = arrayName[i];
    }
    return newArray;
}

function oldest_date() {
	return (new Date(local_dates[0].substring(0,4), local_dates[0].substring(4,6), local_dates[0].substring(6,8), 0, 0, 0));
}

function newest_date() {
	return (new Date(local_dates[total_days].substring(0,4), local_dates[total_days].substring(4,6), local_dates[total_days].substring(6,8), 0, 0, 0));
}

function date_readable(java_date) {
	var month = parseInt(java_date.substring(4,6), 10)+1;
	if (month < 10) {
		month = '0'+month;
	}
	return ' '+month+'/'+java_date.substring(6,8)+'/'+java_date.substring(0,4);
}

function ts_to_string(date) {
	var date_object = new Date(date);
	var month = date_object.getMonth();
	if (month < 10) {
		month = '0'+month;
	}
	var date_string = date_object.getFullYear()+''+month+''+date_object.getDate();
	return date_string;
}

function date_display(action) {
	// Check to see if the action argument is either numeric or null
	if ((action == null) || (!isNaN(action))) {
		if (action == -1) {
			// This is the command to go back one day from the currently selected date
			if (range) {
				// If the currently selected date is a range, go back one from the earliest date in the range
				current_day = date_range[0];
				range = false;
			}
			if (current_day > 0) {
				// If the currently selected date is not the last date there is data for, go back one date.
				current_day--;
			}
		} else if (action == 1) {
			// This is the command to go forward one day from the currently selected date
			if (range) {
				// If the currently selected date is a range, go forward one from the latest date in the range
				current_day = date_range[1];
				range = false;
			}
			if (current_day < total_days) {
				// If the currently selected date is not the most current date there is data for, go forward one date.
				current_day++;
			} 
		} else if (action == 0) {
			// If the action is 0, refresh the current date(s) and therefore, do very little here.  This is only used after the timezone has been changed.
			if (range) {
				alert((date_range[0].toString())+'~'+(date_range[1].toString()));
				date_display((date_range[0].toString())+'~'+(date_range[1].toString()));
			}
		} else  {
			// Else just display the most current day
			range = false;
			current_day = total_days;
		}
		
		if (current_day == total_days) {
			$('#forward_one_day').hide();
		} else if ($('#forward_one_day').is(':hidden')) {
			$('#forward_one_day').show();
		} else {
			$('#forward_one_day').blur();
		}
		
		if (current_day == 0) {
			$('#back_one_day').hide();
		} else if ($('#back_one_day').is(':hidden')) {
			$('#back_one_day').show();
		} else {
			$('#back_one_day').blur();
		}
		
		var display_day = local_dates[current_day];
		$('.separator').width(18.67);
		$('#displayed_date').text('Data for '+date_readable(display_day));
		
		var from_date = (new Date(display_day.substring(0,4), display_day.substring(4,6), display_day.substring(6,8), 0, 0, 0)).getTime();
		var to_date = (new Date(display_day.substring(0,4), display_day.substring(4,6), display_day.substring(6,8), 23, 59, 59)).getTime();
		
		$('#chart').load('data.php?id=BCC1&from='+from_date+'&to='+to_date);
	} 
	// If the action was not numeric or null, it is a string of timestamps in the format 'from~to'	
	else {
		if (action.indexOf('~') != -1) {
			var dates = action.split("~");
			var from_date = (new Date(parseInt(dates[0]))).getTime();
			var to_date = (new Date(parseInt(dates[1]))).getTime();
			if (from_date == to_date) {
				range = false;
				current_day = local_dates.indexOf(ts_to_string(from_date));
				
				if (current_day == total_days) {
					$('#forward_one_day').hide();
				} else if ($('#forward_one_day').is(':hidden')) {
					$('#forward_one_day').show();
				} else {
					$('#forward_one_day').blur();
				}
				
				if (current_day == 0) {
					$('#back_one_day').hide();
				} else if ($('#back_one_day').is(':hidden')) {
					$('#back_one_day').show();
				} else {
					$('#back_one_day').blur();
				}
				
				var display_day = local_dates[current_day];
				$('.separator').width(18.67);
				$('#displayed_date').text('Data for '+date_readable(display_day));
			} else {
				date_range = [local_dates.indexOf(ts_to_string(from_date)), local_dates.indexOf(ts_to_string(to_date))];
				range = true;
				$('.separator').width(4);
				$('#displayed_date').text('Data for '+date_readable(ts_to_string(from_date))+' to '+date_readable(ts_to_string(to_date)));	
			}			
		} else {
			// If there is a specific date selected, go to that date
			alert('This is working');
			range = false;
			//var specific_date = ts_to_string(action);
			
			var specific_date = action.substring(0,4)+action.substring(4,6)+action.substring(6,8);
			alert(specific_date);	
			current_day = local_dates.indexOf(specific_date);
			
			if (current_day == total_days) {
				$('#forward_one_day').hide();
			} else if ($('#forward_one_day').is(':hidden')) {
				$('#forward_one_day').show();
			} else {
				$('#forward_one_day').blur();
			}
			
			if (current_day == 0) {
				$('#back_one_day').hide();
			} else if ($('#back_one_day').is(':hidden')) {
				$('#back_one_day').show();
			} else {
				$('#back_one_day').blur();
			}
			
			var display_day = local_dates[current_day];
			$('.separator').width(18.67);
			$('#displayed_date').text('Data for '+date_readable(display_day));
			
			var from_date = (new Date(display_day.substring(0,4), display_day.substring(4,6), display_day.substring(6,8), 0, 0, 0)).getTime();
			var to_date = (new Date(display_day.substring(0,4), display_day.substring(4,6), display_day.substring(6,8), 23, 59, 59)).getTime();
				
				
		}

		$('#chart').load('data.php?id=BCC1&from='+from_date+'&to='+to_date);			
	}
	$('#forward_one_day, #back_one_day').blur();
}

function date_range_change() {
	if ($('#day_radio').is(':checked')) {
		$('#one_date_input').datepicker( "enable" );
		$('#from_date_input, #to_date_input').datepicker( "disable" );		
	} else {
		$('#one_date_input').datepicker( "disable" );
		$('#from_date_input, #to_date_input').datepicker( "enable" );
	}
}

function specific_date_selected() {
	if ($('#day_radio').is(':checked')) {
		selected_date = $('#one_date_input').datepicker("getDate");
		if (selected_date != null) {
			var temp_month = selected_date.getMonth();
			var temp_day = selected_date.getDate();
			if (temp_month < 10) {
				temp_month = '0'+(temp_month+'');
			}
			if (temp_day < 10) {
				temp_day = '0'+(temp_day+'');
			}
			temp_date_string_selected = selected_date.getFullYear()+''+temp_month+''+temp_day;
			date_display(selected_date.getFullYear()+''+temp_month+''+temp_day);
			$("#custom_date_dialog").dialog('close');
		} else {
			alert('Please make sure your specified date is valid and then try again.');
		}
	} else {
		selected_from_date = $('#from_date_input').datepicker("getDate");
		selected_to_date = $('#to_date_input').datepicker("getDate");
		if ((selected_from_date != null) && (selected_to_date != null)) {			
			date_display(selected_from_date.getFullYear()+''+selected_from_date.getMonth()+''+selected_from_date.getDate()+'~'+selected_to_date.getFullYear()+''+selected_to_date.getMonth()+''+selected_to_date.getDate());				
			$("#custom_date_dialog").dialog('close');
		} else {
			alert('Please make sure your from and to dates are valid and then try again.');
		}
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
	if (local_dates.indexOf(test_string) != -1) {
		return [true, ''];	
	} else {
		return [false, ''];
	}
}

function restrict_range(date_input) {
	if ( $('#day_radio').is(':checked') ) {
		return {
			minDate: oldest_date(),
			maxDate: newest_date(),
		}
	} else {
		if (date_input.id == 'to_date_input') {
			return {
				minDate: $('#from_date_input').datepicker("getDate"),
				maxDate: newest_date()
			};
		} else if (date_input.id == 'from_date_input') {
			return {
				maxDate: $('#to_date_input').datepicker("getDate"),
				minDate: oldest_date()
			};
		}	
	}
}