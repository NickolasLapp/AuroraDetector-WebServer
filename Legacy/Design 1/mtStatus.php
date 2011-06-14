<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta name="" content="">
	<meta name="vs_targetSchema" content="http://schemas.microsoft.com/intellisense/ie5">
	<link rel="stylesheet" href="images.css" type="text/css">
	<link rel="stylesheet" href="2col_leftNav.css" type="text/css">
</head>
<body>
	<h2>Current Aurora Status</h2>
	<br>
	<h6 style = "margin-left: 10px;">
	Click on an area for more information.
	</h6>
	<script type="text/javascript">
		var popupIDsArray = new Array();
			popupIDsArray[0] = "SKC1-popup";
			popupIDsArray[1] = "BCC1-popup";
			popupIDsArray[2] = "SCC1-popup";
			popupIDsArray[3] = "FBC1-popup";
			popupIDsArray[4] = "FPC1-popup";
			popupIDsArray[5] = "BZN2-popup";
			popupIDsArray[6] = "LBC1-popup";
			popupIDsArray[7] = "CDK1-popup";
		
		function hide(ID, status) {
			if (status == "hide") {
				var currentOpacity = 1;
				var linkID = ID+"-link";
				document.getElementById(linkID).style.visibility="hidden";
				fadeOut(ID,currentOpacity);
			} else {
				for(i = 0; i < 8; i++) {
					if (popupIDsArray[i] != ID) {
						if (document.getElementById(popupIDsArray[i]).style.visibility=="visible") {
							var linkID = popupIDsArray[i]+"-link";
							document.getElementById(linkID).style.visibility="hidden";
							var currentOpacity = 1;
							fadeOut(popupIDsArray[i],currentOpacity);
						}
					}
				}
				var currentOpacity = 0;
				document.getElementById(ID).style.opacity = 0;
				document.getElementById(ID).style.visibility="visible";
				fadeIn(ID, currentOpacity);
				var linkID = ID+"-link";
				document.getElementById(linkID).style.visibility="visible";
			}
		}
		
		function fadeIn(ID, currentOpacity) {
			if (currentOpacity < 1) {
				currentOpacity += 0.1;
				document.getElementById(ID).style.opacity = currentOpacity;
				window.setTimeout("fadeIn('"+ID+"',"+currentOpacity+")", 15);
			} else {
				document.getElementById(ID).style.opacity = 1;
				return;
			}	
		}
		
		function fadeOut(ID, currentOpacity) {
			if (currentOpacity > 0) {
				currentOpacity -= 0.1;
				document.getElementById(ID).style.opacity = currentOpacity;
				window.setTimeout("fadeOut('"+ID+"',"+currentOpacity+")", 15);
			} else {
				document.getElementById(ID).style.opacity = 0;
				document.getElementById(ID).style.visibility="hidden";
			}	
		}
	</script>
	
	<!-- Map images -->
	<div id="SKC1">
		<a href="#" onmouseover="document.SKC1.src='images/Pablo-large.png'" onmouseout="document.SKC1.src='images/Pablo-small.png'" onclick="hide('SKC1-popup', '')">
		<img src="images/Pablo-small.png" name="SKC1" width="146" height="201" border="0" alt="Salish Kootenai College">
		</a>
	</div>
	<div id="BCC1">
		<a href="#" onmouseover="document.BCC1.src='images/Browning-large.png'" onmouseout="document.BCC1.src='images/Browning-small.png'" onclick="hide('BCC1-popup', '')">
		<img src="images/Browning-small.png" name="BCC1" width="79" height="201" border="0" alt="Blackfeet Community College">
		</a>
	</div>
	<div id="SCC1">
		<a href="#" onmouseover="document.SCC1.src='images/BoxElder-large.png'" onmouseout="document.SCC1.src='images/BoxElder-small.png'" onclick="hide('SCC1-popup','')">
		<img src="images/BoxElder-small.png" name="SCC1" width="115" height="201" border="0" alt="Stone Child College">
		</a>
	</div>
	<div id="FBC1">
		<a href="#" onmouseover="document.FBC1.src='images/Harlem-large.png'" onmouseout="document.FBC1.src='images/Harlem-small.png'" onclick="hide('FBC1-popup','')">
		<img src="images/Harlem-small.png" name="FBC1" width="122" height="201" border="0" alt="Fort Belknap College">
		</a>
	</div>
	<div id="FPC1">
		<a href="#" onmouseover="document.FPC1.src='images/Poplar-large.png'" onmouseout="document.FPC1.src='images/Poplar-small.png'" onclick="hide('FPC1-popup','')">
		<img src="images/Poplar-small.png" name="FPC1" width="178" height="201" border="0" alt="Fort Peck Community College">
		</a>
	</div>
	<div id="BZN2">
		<a href="#" onmouseover="document.BZN2.src='images/Bozeman-large.png'" onmouseout="document.BZN2.src='images/Bozeman-small.png'" onclick="hide('BZN2-popup','')">
		<img src="images/Bozeman-small.png" name="BZN2" width="300" height="199" border="0" alt="Montana State University">
		</a>
	</div>
	<div id="LBC1">
		<a href="#" onmouseover="document.LBC1.src='images/CrowAgency-large.png'" onmouseout="document.LBC1.src='images/CrowAgency-small.png'" onclick="hide('LBC1-popup','')">
		<img src="images/CrowAgency-small.png" name="LBC1" width="137" height="199" border="0" alt="Little Big Horn College">
		</a>
	</div>
	<div id="CDK1">
		<a href="#" onmouseover="document.CDK1.src='images/LameDeer-large.png'" onmouseout="document.CDK1.src='images/LameDeer-small.png'" onclick="hide('CDK1-popup','')">
		<img src="images/LameDeer-small.png" name="CDK1" width="203" height="199" border="0" alt="Chief Dull Knife College">
		</a>
	</div>
	
	<!-- Information popups -->
	<div id="SKC1-info">
		<a href="#" onclick="hide('SKC1-popup','hide')">
		<img src="SKC1-popup.php" name="SKC1InfoPopup" id="SKC1-popup" width="172" height="164" border="0">
		</a>
	</div>
	<div id="SKC1-detailed_info">
		<a href="data.php?station=SKC1" id="SKC1-popup-link" target="data" style="font-size: 90%;">More Information</a>
	</div>
	
	<div id="BCC1-info">
		<a href="#" onclick="hide('BCC1-popup','hide')">
		<img src="BCC1-popup.php" name="BCC1InfoPopup" id="BCC1-popup" width="172" height="164" border="0">
		</a>
	</div>
	<div id="BCC1-detailed_info">
		<a href="data.php?station=BCC1" id="BCC1-popup-link" target="data" style="font-size: 90%;">More Information</a>
	</div>
	
	<div id="SCC1-info">
		<a href="#" onclick="hide('SCC1-popup','hide')">
		<img src="SCC1-popup.php" name="SCC1InfoPopup" id="SCC1-popup" width="172" height="164" border="0">
		</a>
	</div>
	<div id="SCC1-detailed_info">
		<a href="data.php?station=SCC1" id="SCC1-popup-link" target="data" style="font-size: 90%;">More Information</a>
	</div>
	
	<div id="FBC1-info">
		<a href="#" onclick="hide('FBC1-popup','hide')">
		<img src="FBC1-popup.php" name="FBC1InfoPopup" id="FBC1-popup" width="172" height="164" border="0">
		</a>
	</div>
	<div id="FBC1-detailed_info">
		<a href="data.php?station=FBC1" id="FBC1-popup-link" target="data" style="font-size: 90%;">More Information</a>
	</div>
	
	<div id="FPC1-info">
		<a href="#" onclick="hide('FPC1-popup','hide')">
		<img src="FPC1-popup.php" name="FPC1InfoPopup" id="FPC1-popup" width="172" height="164" border="0">
		</a>
	</div>
	<div id="FPC1-detailed_info">
		<a href="data.php?station=FPC1" id="FPC1-popup-link" target="data" style="font-size: 90%;">More Information</a>
	</div>
	
	<div id="BZN2-info">
		<a href="#" onclick="hide('BZN2-popup','hide')">
		<img src="BZN2-popup.php" name="BZN2InfoPopup" id="BZN2-popup" width="172" height="164" border="0">
		</a>
	</div>
	<div id="BZN2-detailed_info">
		<a href="data.php?station=BZN2" id="BZN2-popup-link" target="data" style="font-size: 90%;">More Information</a>
	</div>
	
	<div id="LBC1-info">
		<a href="#" onclick="hide('LBC1-popup','hide')">
		<img src="LBC1-popup.php" name="LBC1InfoPopup" id="LBC1-popup" width="172" height="164" border="0">
		</a>
	</div>
	<div id="LBC1-detailed_info">
		<a href="data.php?station=LBC1" id="LBC1-popup-link" target="data" style="font-size: 90%;">More Information</a>
	</div>
	
	<div id="CDK1-info">
		<a href="#" onclick="hide('CDK1-popup','hide')">
		<img src="CDK1-popup.php" name="CDK1InfoPopup" id="CDK1-popup" width="172" height="164" border="0">
		</a>
	</div>
	<div id="CDK1-detailed_info">
		<a href="data.php?station=CDK1" id="CDK1-popup-link" target="data" style="font-size: 90%;">More Information</a>
	</div>
	
	
	<!-- Current aurora status overlays -->
	<?php
		$aurora_status = file("currentStatus.txt");
		for ($i = 0; $i < count($aurora_status); $i++)
      		$aurora_status[$i] = rtrim($aurora_status[$i]);
			
		if ($aurora_status[0] != "0") {
			echo "<div id=\"SKC1-status\"> <img src=\"images/IBC_".$aurora_status[0].".png\" width=\"102\" height=\"52\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
		}
		if ($aurora_status[1] != "0") {
			echo "<div id=\"BCC1-status\"> <img src=\"images/IBC_".$aurora_status[1].".png\" width=\"102\" height=\"52\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
		}
		if (($aurora_status[2] != "0") && ($aurora_status[3] != "0") && ($aurora_status[2] == $aurora_status[3])) {
			echo "<div id=\"SCC1-FBC1-status\"> <img src=\"images/SCC1_FBC1-IBC_".$aurora_status[2].".png\" width=\"165\" height=\"69\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
		} else {
			if ($aurora_status[2] != "0") {
				echo "<div id=\"SCC1-status\"> <img src=\"images/IBC_".$aurora_status[2].".png\" width=\"102\" height=\"52\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
			} if ($aurora_status[3] != "0") {
				echo "<div id=\"FBC1-status\"> <img src=\"images/IBC_".$aurora_status[3].".png\" width=\"102\" height=\"52\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
			}
		}
		if ($aurora_status[4] != "0") {
			echo "<div id=\"FPC1-status\"> <img src=\"images/IBC_".$aurora_status[4].".png\" width=\"102\" height=\"52\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
		}
		if ($aurora_status[5] != "0") {
			echo "<div id=\"BZN2-status\"> <img src=\"images/IBC_".$aurora_status[5].".png\" width=\"102\" height=\"52\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
		}
		
		if (($aurora_status[6] != "0") && ($aurora_status[7] != "0") && ($aurora_status[6] == $aurora_status[7])) {
			echo "<div id=\"LBC1-CDK1-status\"> <img src=\"images/LBC1_CDK1-IBC_".$aurora_status[6].".png\" width=\"152\" height=\"54\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
		} else {
			if ($aurora_status[6] != "0") {
				echo "<div id=\"LBC1-status\"> <img src=\"images/IBC_".$aurora_status[6].".png\" width=\"102\" height=\"52\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
			}
			if ($aurora_status[7] != "0") {
				echo "<div id=\"CDK1-status\"> <img src=\"images/IBC_".$aurora_status[7].".png\" width=\"102\" height=\"52\" border=\"0\" alt=\"Aurora detected\"> </div>\n";
			}
		}
	?>
	
	<div id="Dead-Zone">
		<img src="images/dead_zone-test.png" name="dead_zone" width="640" height="30" style="opacity:0; filter:alpha(opacity=0)">
	</div>
</body>
</html>

