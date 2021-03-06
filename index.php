<?php
require_once("./include/membersite_config.php");
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Montana Aurora Detector Network</title>
		<link type="text/css" href="css/new-theme/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
		<link type="text/css" href="css/aurora.css" rel="stylesheet" />
		<script src="http://www.google.com/jsapi?key=ABQIAAAAToxhgf_rzjN7LOyhH1jyvBTe9t9bKN0lBJnMkQH51vw737LPuBS8oWw2AvSyuUzgADEOcOSz5iAxjw"></script>
		<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.5.custom.min.js"></script>
		<script type="text/javascript" src="js/cookieFunctions.js"></script>
		<script type="text/javascript" src="js/liveDataFunctions.js"></script>
		<script type="text/javascript">
			var ge;
			var currentKmlObjects = {'full': null, 'abbrev': null, 'oval': null};
			var number_of_objects;
			google.load("earth", "1");
			google.setOnLoadCallback(init);
			
			$(function(){
				// Current Aurora Status Functions
				$('#tabs').tabs();
				$("#tabs li").removeClass('ui-corner-top').addClass('ui-corner-all');
				
				// Toolbar buttons
				$( "#map_settings" ).buttonset();
					$( "#name_length" ).click(function() { $(this).blur(); name_checked(); });
					$( "#show_oval" ).click(function() { $(this).blur(); oval_checked(); });
				$( "#map_shortcuts" ).buttonset();
					$( "#view_network" ).click(function() { $(this).blur(); lookMt(); });
					$( "#view_oval" ).click(function() { $(this).blur(); lookOval(); });
				$( "#help_button" ).button({ icons: { primary: "ui-icon-help"}, text: false}).click(function() {
					$(this).blur();
					$('.demo').button('refresh');
					$('#map3d').addClass('ui-helper-hidden-accessible');
					$( "#help" ).dialog({width: 500, modal: true, close: function(event, ui) {
						$('#map3d').removeClass('ui-helper-hidden-accessible');
					} });
				});
				
				$( ".demo" ).button().click(function() { $(this).blur(); toggle_demo_buttons();	});
								
				initialize_toolbar();
				resize();
			});
			
			var resizeTimer = null;
			$(window).bind('resize', function() {
				if (resizeTimer) { clearTimeout(resizeTimer); }
    			resizeTimer = setTimeout(resize, 50);
			});
			
		</script>
	</head>
	<body id="body_content">
		<!--[if lte IE 8]>
			<div class="ui-widget" style="padding: 1em 1em;">
				<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
						<strong>Alert:</strong> You are using Microsoft Internet Explorer version 8 or below. 
						<p>Please upgrade to IE version 9 or switch to Google Chrome, Mozilla Firefox or Apple Safari for the best possible viewing experience. 
						Links to these browswers are available below.
						<br>Google Chrome: <a href="http://www.google.com/chrome">download</a>.
						Mozilla Firefox: <a href="www.mozilla.com/firefox/">download</a>.
						Apple Safari: <a href="http://www.apple.com/safari/download/">download</a>.
						Microsoft Internet Explorer 9 Beta: <a href="http://www.beautyoftheweb.com/">download</a>.</p>
					</p>
				</div>
			</div>
		<![endif]-->

		<!-- start masthead -->
	    <div id="masthead">
			<h3 class="title">Montana Aurora Detector Network</h3>
			<img class="msu" src="images/msulogo.jpg" border="0" height="110" width="109" alt="msulogo">
			<h4 class="subheading">
	            Operated by the Optical Remote Sensor Laboratory, Dr. Joseph Shaw,<br>
	            Montana State University, Electrical and Computer Engineering Department
			</h4>
			<?php	
				if(!$fgmembersite->CheckLogin())
				{
					echo "<p style='text-align:right'>";
						echo "<a href='login.php'>Login</a> or <br /><a href='forgot-password.php'>forgot password?</a>.";
					echo "</p>";
				}
				else {
					echo "<p style='text-align:right'>";
						echo "<a href='logout.php'>Sign out</a>";
					echo "</p>";	
					echo "<p style='text-align:right'>";
						echo "<a href='unsubscribe.php?signature=" . $fgmembersite->GetHashedInfo().
							"&email=".$fgmembersite->UserEmail().
							"&phone_number=".$fgmembersite->UserPhoneNumber().
							"&expiration=".date('Y-m-d', time())."'>Unsubscribe</a>";
					echo "</p>";	
				}
			?>
		</div>

		<!-- Tabs -->
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Live Aurora Status</a></li>
				<?php
					if(!$fgmembersite->CheckLogin()) {
						echo "<li><a href='register.php'>Sign Up For Aurora Alerts</a></li>";
					}
					else {
						echo "<li><a href='controlPanel.php'>User Control Panel</a></li>";
					}
				?>
				<!-- <li><a href="archived.php">Archived Data</a></li> -->
				<li><a href="#tabs-4">About Our Detectors</a></li>
				<li><a href="#tabs-5">About The Auroral Oval</a></li>
				<li><a href="contact.html">Our Research</a></li>
				<li><a href="#tabs-8">Contact Us</a></li>
			</ul>
			<div id="tabs-1">
				<div id="tab1_heading">
					<h4>Live Aurora Status</h4>
					<div id="tab1_toolbar" class="ui-widget-header ui-corner-top toolbar">
						<span id="map_settings">
							<input type="checkbox" id="name_length"/><label for="name_length" title="Toggles between full and abbreviated detector names">Full Names</label>
							<input type="checkbox" id="show_oval"/><label for="show_oval" title="Toggles the display of the auroral oval">Auroral Oval</label>
						</span>
						<span id="map_shortcuts">
							<button id="view_network" title="Moves the map to Montana">View Montana</button>
							<button id="view_oval" title="Moves the map to the northern aurora oval">View Auroral Oval</button>
						</span>
						<button id="help_button">Help</button>
					</div>
				</div>
				<div id="map3d"></div>
				<img class="legend">
				<small id="version_info">Google Earth plugin version: <span id="installed-plugin-version">Loading...</span> Window Size: <span id="sizeReporter"></span></small>
				
				<div id="help" title="Help" class="ui-helper-hidden">
					<h4>Current Aurora Status Help</h4>
					<br> This page displays the current status of the network represented by the icons on the map. 
						Each of these icons can be clicked to display more information about the current status.<br>
					<br><h5>College Icons</h5><br/>
						The numbers in the icons for each college represent the current brightness level of the aurora at that location.
						For more information about these brightness levels, use the "About Our Detectors" link in the navigation bar on the left side of the page.<br>
					<br><h5>Map Display Settings</h5><br/>
						<input class="demo" type="checkbox" id="demo_name_length" checked="checked"/><label for="demo_name_length">Full Names</label>
						<b><span id="demo_name_state">Shows full college names</span></b><br/>
						This button toggles the length of the colleges' names between full and abbreviated.
						By default the full names are displayed.<br/>
						<input class="demo" type="checkbox" id="demo_show_oval" checked="checked"/><label for="demo_show_oval">Auroral Oval</label>
						<b><span id="demo_oval_state">Shows the auroral oval</span></b><br/>
						This button toggles the display of the auroral oval. This is enabled by default.
						Please note that even when this is displayed, it may not be visible from your current map view.
						For more information about the auroral oval, use the link in the navigation bar.<br>
					<br><h5>Map View Shortcuts</h5><br/>
						<button class="demo" id="demo_view_network">View Montana</button><br/>
						This button adjusts the map so that the entire Montana Aurora Detector Network is in view.<br/>
						<button class="demo" id="demo_view_oval">View Auroral Oval</button><br/>
						When the auroral oval is displayed this button adjusts the map so that the northern auroral oval is in view.<br><br>
					<small><center>The buttons in this dialog are for demonstration only. The states of these buttons will not effect the content on the map.</center></small>
				</div>
			</div>
			<div id="tabs-4">
				<h1>Information About Our Detectors</h1>
				<br>
				<h3><b>What causes the Aurora?</b></h3>
				<p style="width: 45em"><b>Excited Particles in our Atmosphere</b> cause the Aurora to become visible. Energy from the sun strikes our atmosphere, causing causes abundant Nitrogen and Oxygen to become excited, meaning that
				   their electrons jump up to a higher energy state. These electrons eventually fall back to their ground state and they release the stored energy as light. This is the light that we see and recognize as the visible Aurora.</p>
				<h1>How Do We Detect the Aurora?</h1>
				<p style="width:45em"><b>The Light is Released</b> from the particles in our atmosphere at a specific wavelength. In the case of Oxygen emissions this light is approximately 557.7nm. This light is fed through a lens into our detector,
					and we measure its intensity. We then also measure the total light from the sky, and compare the relative strengths. If the Aurora Green is more intense than the general light from the sky, we know that an Aurora is likely occuring!</p>
				<br>
				<br><img height="182" width="224" style="display: block; margin-left:auto; margin-right:auto" src='images/detector.jpg'>
				<p style="width: 45em"> Pictured is the aurora detector located at MSU Bozeman on the roof of Cobleigh Hall. Skylight passes through a 10-nm interference filter and is focused by a lens onto a photomultiplier tube (PMT) detector 
					whose output is sampled with a microcontroller and transmitted via Ethernet to a server at MSU. Once the data arrives, a data processing algorithm determines whether an alarm signal should be sent.</p>
				<br>
			</div>
			<div id="tabs-5">
				<h4>Information About The Auroral Oval</h4>
				<br>
				<p style="width: 60em">The Auroral Oval is generated with <a href='http://www.swpc.noaa.gov/products/30-minute-aurora-forecast'>information</a> gathered by the National Oceanic and Atmospheric Administration (NOAA).
					Data is generated through measurements of solar winds, which indicate the likelyhood of an Auroral storm occuring at the hemispheres. Though the Aurora is primarily in high-latitude
					environments, high intensity solar storms can cause the Auroral Oval to extend down into Montana. The image below was taken by Dr. Joseph Shaw in Bozeman on May 3rd, 2010.
					<br>
					<br>
					<img height="340" width="516" style="display: block; margin-left:auto; margin-right:auto"  src="images/auroraexample.jpg">
					<br>
					The Auroral Oval indicates where the Aurora is likely visible. Darker regions indicate higher higher probability of seeing the Aurora, while lighter and clear regions indicate little to no chance of seeing the Aurora.
					Data used to generate the Auroral Oval is updated every 5 minutes, so be sure to refresh your browser to see the most recent Auroral Oval.</p>
			</div>
			<div id="tabs-8">
				<h3><a href="mailto:jshaw at montana dot edu">The Optical Remote Sensor Laboratory</a></h3>
				<br>
				<h3><a href="mailto:aurora.montana+webmaster@gmail.com">Webmaster</a></h3>
			</div>
		</div>
	</body>
</html>