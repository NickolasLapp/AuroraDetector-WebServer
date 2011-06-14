<?php
	include_once 'constants.php';
?>


<script type="text/javascript">
	// Archived Data Functions	
	$.getScript('js/archivedDataFunctions.php', function() {
		var timezone = (checkSettings())[2];
		$( "#timezone_select" ).val(timezone);
		$( "#timezone_select" ).change(function() {
			timezone = $( "#timezone_select" ).val();
			toggleSettings(timezone);
			load_chart(timezone);
		});	
		
		$( "#range_select" ).buttonset().click( function() { day_range_change(); });
		$( "#from_date, #to_date" ).datepicker().datepicker("setDate" , newest_date() ).datepicker('option', {
			beforeShow: restrict_range,
			beforeShowDay: restrict_dates,
			onSelect: function(date) { load_chart(timezone); } 
		});
				
		$( "#download_button" ).button({ icons: { secondary: "ui-icon-arrowthickstop-1-s"}, text: false, disabled: false}).click(function() {
			$(this).blur();
			$( "#download_menu" ).dialog({width: 350, modal: true, close: function(event, ui) {
				$( "downloads_available" ).text('Checking for available data...');
			} });
			check_for_data();
		});
		
		$( ".download_data_button").button({ icons: { primary: "ui-icon-arrowthickstop-1-s" }, text: false }).click(function() {
			$(this).blur();
			var id_of_button = ($(this).attr("id")).substring(9);
			//console.log(id_of_button);
			$('#download_status').text('Compiling data for '+id_of_button+'. Download will begin in a few moments.');
			var dates = check_dates_input();
			//console.log(dates);
			$( ".download_data_button").button({disabled: true});
			//console.log('#'+id_of_button+'_download_box');
			var data_file_url = ["data.php?id=",id_of_button,"&from=",dates[0],"&to=",dates[1]].join("");
			//console.log(data_file_url);
			$('#download_box').attr('src', data_file_url);
			$( ".download_data_button").button({disabled: false});
		});
		
		$( "#close" ).button({ icons: { primary: "ui-icon-closethick" } }).click(function() {
			$('#download_status').text('');
			$("#downloads_available").text('');
			$("#download_menu").dialog( "close" );
		});
		
		$('#loading_progress').hide();
		$('#tab2_content').show();
		day_range_change();
		load_chart(timezone);
	});
</script>

<div id="tab2_heading"><h4>Archived Data</h4></div>

<div id="loading_progress">
	<p>Checking for available data. This may take a few moments...</p>
</div>

<div id="tab2_content" class="ui-helper-hidden">
	<div id="tab2_toolbar" class="ui-widget-header ui-corner-top">
		<form style="display:inline">
			<span id="range_select">
				<input type="radio" id="one_day_radio" name="range_select" checked="checked"/><label for="one_day_radio">One Day</label>
				<input type="radio" id="date_range_radio" name="range_select"/><label for="date_range_radio" title="Select a range of dates">Range</label>
			</span>
		</form>
		<span id="range_input">
			<span id="start_text"></span><input size="10" type="text" id="from_date">
			<span id="end_text">End: <input size="10" type="text" id="to_date"></span>
		</span>
		<select id="timezone_select" title="Select your standard timezone, Daylight Savings will be applied automatically if applicable.">
			<option value="UTC">Select your timezone...</option>
			<?php
				foreach ($timezones as $tz => $info) {
					echo '<option value="'.$tz.'">'.$info['tz'].'</option>';
				}
			?>
		</select>	
		<button id="download_button" title="Download data (currently not operational)">Download</button>
	</div>
	
	<div id="chart"></div>
	
	<div id="download_menu" title="Download Data" class="ui-helper-hidden">
		<p>Select which detector's data you wish to download from the choices below.</p>
		<div id="downloads_available">Checking for available data...</div>
		<div id="BCC1" class="ui-helper-hidden">
			<button id="download_BCC1" class="download_data_button">Download</button>
			Blackfeet Community College
		</div>
		<div id="CDK1" class="ui-helper-hidden">
			<button id="download_CDK1" class="download_data_button">Download</button>
			Chief Dull Knife College
		</div>
		<div id="FBC1" class="ui-helper-hidden">
			<button id="download_FBC1" class="download_data_button">Download</button>
			Fort Belknap College
		</div>
		<div id="FPC1" class="ui-helper-hidden">
			<button id="download_FPC1" class="download_data_button">Download</button>
			Fort Peck Community College
		</div>
		<div id="LBH1" class="ui-helper-hidden">
			<button id="download_LBH1" class="download_data_button">Download</button>
			Little Big Horn College
		</div>
		<div id="BZN2" class="ui-helper-hidden">
			<button id="download_BZN2" class="download_data_button">Download</button>
			Montana State University
		</div>
		<div id="SKC1" class="ui-helper-hidden">
			<button id="download_SKC1" class="download_data_button">Download</button>
			Salish Kootenai College
		</div>
		<div id="SCC1" class="ui-helper-hidden">
			<button id="download_SCC1" class="download_data_button">Download</button>
			Stone Child College
		</div>
		<p>Status: <span id="download_status">Waiting...</span></p>
		<span><button id="close">Close</button></span>
	</div>
</div>

<!-- Create the offscreen iFrame used to start the download without the random opening of a new tab while the data is being compiled. -->
<!-- I REALLY want to avoid using this if possible, will change if I find a better method -->
<iframe width="1" height="1" frameborder="0" src="" id="download_box"></iframe>		