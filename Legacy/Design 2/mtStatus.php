<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1">
	<meta http-equiv="refresh" content="300" > 
	<link rel="stylesheet" href="2col_leftNav.css" type="text/css">
	<script src="http://www.google.com/jsapi?key=ABQIAAAAHP16blkrd4Koo7WXGxBOABSsWUSjEjRtBHXIwYcznw7vTxpJKBRsZs-3HHEbobdbcGxExsqBeA67yQ"></script>
	
	<script type="text/javascript">
		// Set global variable
		var ge;
		
		// Functions for handling the cookie for storing the users settings
		// c_name: Name of the cookie
		function setCookie(c_name,value,expiredays) {
			var exdate=new Date();
			exdate.setDate(exdate.getDate()+expiredays);
			document.cookie=c_name+ "=" +escape(value)+
			((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
		}
		
		function getCookie(c_name) {
			if (document.cookie.length>0) {
				c_start=document.cookie.indexOf(c_name + "=");
				if (c_start!=-1) {
					c_start=c_start + c_name.length+1;
					c_end=document.cookie.indexOf(";",c_start);
					if (c_end==-1) c_end=document.cookie.length; 
					return unescape(document.cookie.substring(c_start,c_end));
				}
			}
			return "";
		}
		
		function checkSettings() {
			var settings=getCookie('AuroraDetectorNetworkSettings');
			if (settings!=null && settings!="") {
				var nameLengthSetting = parseInt(settings.substring(0, 1));
				var showAuroralOvalSetting = parseInt(settings.substring(1, 2));
				return [nameLengthSetting,showAuroralOvalSetting];
			} else {
				var settings='11';
				setCookie('AuroraDetectorNetworkSettings',settings,365);
				var nameLengthSetting = parseInt(settings.substring(0, 1));
				var showAuroralOvalSetting = parseInt(settings.substring(1, 2));
				return [nameLengthSetting, showAuroralOvalSetting];
			}
		}
		
		function toggleSettings(setting) {
			var displaySettings = checkSettings();
			var nameLengthSetting = displaySettings[0];
			var showAuroralOvalSetting = displaySettings[1];
			
			if (setting == 0) {
				if (nameLengthSetting == 0) {
					nameLengthSetting = 1;
				} else {
					nameLengthSetting = 0;
				}
			} else {
				if (showAuroralOvalSetting == 0) {
					showAuroralOvalSetting = 1;
				} else {
					showAuroralOvalSetting = 0;
				}
			}
			
			var settings = ''+nameLengthSetting+showAuroralOvalSetting;
			setCookie('AuroraDetectorNetworkSettings',settings,365);
			window.location.reload();
		}
		
		function setDisplay() {
			var displaySettings = checkSettings();
			var linkString = 'http://www.coe.montana.edu/ee/weather/aurora/AuroraNetwork_Web.php?longNames='+displaySettings[0]+'&showOval='+displaySettings[1];
			return linkString;
		}
		
		function windowSize() {
			var myWidth = 0, myHeight = 0;
			if( typeof( window.innerWidth ) == 'number' ) {
				//Non-IE
				myWidth = window.innerWidth;
				myHeight = window.innerHeight;
			} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
				//IE 6+ in 'standards compliant mode'
				myWidth = document.documentElement.clientWidth;
				myHeight = document.documentElement.clientHeight;
			} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
				//IE 4 compatible
				myWidth = document.body.clientWidth;
				myHeight = document.body.clientHeight;
			}
			return [myWidth, myHeight];
		}
		
		function resize() {
			var size = windowSize();
			var mapWidth = size[0]-20;
			var mapHeight = size[1]-67;
			var legendHeight = mapHeight-50;
			var toggleWidth = mapWidth;
			
			var displaySettings = checkSettings();
			var showAuroralOvalSetting = displaySettings[1];
			if (showAuroralOvalSetting == 1) {
				mapWidth = mapWidth-100;
			}
			
			document.getElementById('map3d').style.width = mapWidth+'px';
			document.getElementById('map3d').style.height = mapHeight+'px';
			document.getElementById('toggle').style.width = toggleWidth+'px'; 
			document.getElementById('legendBar').style.height = legendHeight+'px';
			document["legendBar"].src = 'images/test.bmp';
			document["legendBar"].height = legendHeight;
		}
		
		// Everything needed for initiating the Google Earth API
		function init() {
			google.earth.createInstance('map3d', initCB, failureCB);
		}
		
		function initCB(instance) {
			ge = instance;
			ge.getWindow().setVisibility(true);
			
			var linkString = setDisplay();
			
			var link = ge.createLink('');
			var href = ''+linkString;
			link.setHref(href);
			
			var networkLink = ge.createNetworkLink('');
			networkLink.set(link, true, true); // Sets the link, refreshVisibility, and flyToView
			
			ge.getFeatures().appendChild(networkLink);
			
			ge.getNavigationControl().setVisibility(ge.VISIBILITY_SHOW);
			ge.getNavigationControl().getScreenXY().setXUnits(ge.UNITS_PIXELS);
  			ge.getNavigationControl().getScreenXY().setYUnits(ge.UNITS_INSET_PIXELS);
			
			ge.getLayerRoot().enableLayerById(ge.LAYER_BORDERS, true);
			
			ge.getOptions().setFlyToSpeed(ge.SPEED_TELEPORT);  
		}
		
		function failureCB(errorCode) {		}
		
		google.load("earth", "1");
		google.setOnLoadCallback(init);
		
	</script>

	</head>
	
	<body onload="resize();" onresize="resize();">
		<h2>Current Aurora Status</h2>
		<table id="toggle" height="16px" border="0" cellpadding="5" cellspacing="2">
			<tr>
				<td width="50%" align="center">
					<script type="text/javascript">
						var displaySettings = checkSettings();
						var nameLengthSetting = displaySettings[0];
						if (nameLengthSetting == 1) {
							document.write('<a href="#" onclick="toggleSettings(0)">Show Abbreviated Names</a>');
						} else {
							document.write('<a href="#" onclick="toggleSettings(0)">Show Full Names</a>');
						}
					</script>
				</td>
				
				<td width="50%" align="center">
					<script type="text/javascript">
						var displaySettings = checkSettings();
						var showAuroralOvalSetting = displaySettings[1];
						if (showAuroralOvalSetting == 1) {
							document.write('<a href="#" onclick="toggleSettings(1)">Hide Auroral Oval</a>');
						} else {
							document.write('<a href="#" onclick="toggleSettings(1)">Show Auroral Oval</a>');
						}
					</script>
				</td>
			</tr>
		</table>
		<div id="map3d"></div>
		<small><center>This page automatically refreshes every five minutes to provide the most current data.</center></small>
		<div id="legend" style="visibility: hidden; width: 90px; height: 50px">
			<h2 style="font-size: 14px;"><center>Power Flux<br>mW/m<sup>2</sup></center></h2>
		</div>
		<div id="legendBar" style="visibility: hidden; width: 90px; height: 50px">
			<img src="images/test2.bmp" alt='Loading...' name="legendBar" width="90">
		</div> 
		
		<script type="text/javascript">
			var displaySettings = checkSettings();
			var showAuroralOvalSetting = displaySettings[1];
			if (showAuroralOvalSetting == 1) {
				document.getElementById('legend').style.visibility = 'visible';
				document.getElementById('legendBar').style.visibility = 'visible';
			}
		</script>
	</body>
</html>