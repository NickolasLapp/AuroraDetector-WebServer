<?php
	$garbage = $_REQUEST['time'];
	
	include_once 'constants.php';
	include_once 'dataParsing.php';
	
	//Set up the G.E. header and create the folders	
	$dom=new DOMDocument('1.0','iso-8859-1');
	
	$google = $dom->createElementNS('http://earth.google.com/kml/2.2', 'kml');
	$googleNode = $dom->appendChild($google);
	
	$folder = $dom->createElement('Folder');
	$folderNode = $googleNode->appendChild($folder);
	
	$open = $dom->createElement('open','1');
	$openNode = $folderNode->appendChild($open);
	
	//Set up the document node for the Aurora Network
	$document = $dom->createElement('Document');
  	$documentNode = $folderNode->appendChild($document);
	
	$name = $dom->createElement('name','Montana Aurora Detector Network');
	$nameNode = $documentNode->appendChild($name);
	
	$descriptionText = '<p><h3 style = "margin-right: 130px; font-size: 12px; font-weight: normal;">Overlays the current status of each of the eight detectors in the Montana Aurora Detector Network.<br><br>This network is operated by the Optical Remote Sensor Laboratory on the campus of Montana State University, Bozeman.</p><p></h3><h6 style = "font-size: 10px; font-weight: normal; text-align: center;">The data refreshes every minute to provide you with the most current information possible.</h6></p>';
	//TODO: Make this lower link dynamic
	$descriptionText = $descriptionText.'<img style = "position:absolute;left:275px;top:15;"; src = http://orsl.eps.montana.edu/betaWebsite/aurora/images/msulogo.jpg>';
	
	$description = $dom->createElement('description',$descriptionText);
	$descriptionNode = $documentNode->appendChild($description);
	
	$snippet = $dom->createElement('Snippet','');
	$snippetNode = $documentNode->appendChild($snippet);
	$snippetNode->setAttribute('maxLines', '2');
	
	$style = $dom->createElement('Style');
	$styleNode = $documentNode->appendChild($style);
	$styleNode->setAttribute('id', 'aurora_network');
	
	$balloonStyle = $dom->createElement('BalloonStyle');
	$balloonStyleNode = $styleNode->appendChild($balloonStyle);
	
	$balloonText = $dom->createElement('text', '$[description]');
	$balloonTextNode = $balloonStyleNode->appendChild($balloonText);
	
	//Tell google Earth to look at MT when the Aurora Network is clicked
	$lookAt = $dom->createElement('LookAt');
	$lookAtNode = $documentNode->appendChild($lookAt);
	$lookAtNode->setAttribute('id','Montana');
	
	$latitude = $dom->createElement('latitude','45.5');
	$latitudeNode = $lookAtNode->appendChild($latitude);
	
	$longitude = $dom->createElement('longitude','-105.5');
	$longitudeNode = $lookAtNode->appendChild($longitude);
	
	$range = $dom->createElement('range','1500000');
	$rangeNode = $lookAtNode->appendChild($range);
	
	$tilt = $dom->createElement('tilt','0');
	$tiltNode = $lookAtNode->appendChild($tilt);
	
	$heading = $dom->createElement('heading','0');
	$headingNode = $lookAtNode->appendChild($heading);
	
	
	//Set up the detectors placemarks and status icons
	for ($ii = 0; $ii < count($detectors['IDs']); $ii++) {
		$placemark = $dom->createElement('Placemark');
		$placemarkNode = $documentNode->appendChild($placemark);
		
		$name = $dom->createElement('name',$detectors['names'][$ii]);
		$nameNode = $placemarkNode->appendChild($name);
		
		$snippet = $dom->createElement('Snippet','Aurora detector on the campus of '.$detectors['names'][$ii]);
		$snippetNode = $placemarkNode->appendChild($snippet);
		$snippetNode->setAttribute('maxLines', '2');
		
		$style = $dom->createElement('Style');
		$styleNode = $placemarkNode->appendChild($style);
		$styleNode->setAttribute('id', $detectors['IDs'][$ii].' balloon');
		
		$balloonStyle = $dom->createElement('BalloonStyle');
		$balloonStyleNode = $styleNode->appendChild($balloonStyle);
		
		$balloonText = $dom->createElement('text', '$[description]');
		$balloonTextNode = $balloonStyleNode->appendChild($balloonText);
		
		$iconStyle = $dom->createElement('Icon');
		$iconStyleNode = $styleNode->appendChild($iconStyle);
		
		//TODO: Make this lower link dynamic
		$iconImageLink = 'http://orsl.eps.montana.edu/betaWebsite/aurora/stationImage.php?ID='.($detectors['IDs'][$ii]).'&date='.$expiration_time;
		$iconLink = $dom->createElement('href', htmlspecialchars($iconImageLink));
		$iconLinkNode = $iconStyleNode->appendChild($iconLink);
		
		$descriptionText = '<h3 style = "text-align: left; font-family: Arial,sans-serif;">'.$detectors['names'][$ii].'</h3>';
		$descriptionText = $descriptionText.'<p><h3 style = "font-size: 12px; font-weight: normal; font-family: Arial,sans-serif;">Aurora detector on the campus of '.$detectors['names'][$ii].'.</h3>';
		$descriptionText = $descriptionText.'</p><text style = "font-size: 12px; font-weight: normal; font-family: Arial,sans-serif;">';

		$PMTData = getRecentPMTAuroraData($detectors['IDs'][$ii]);
		$PDData	 = getRecentPDAuroraData($detectors['IDs'][$ii]);	
		if ($PMTData == 'NaN' || $PDData == 'NaN') {
			$descriptionText = $descriptionText.'Current Status: Standby';
			$descriptionText = $descriptionText.'<br>PMT:<rad style = "text-align: left; margin-left: 10px;"> '.'#####'.' &#956;<i>W</i>/<i>m</i><sup>2</sup><i>Sr</i></rad>';
			$descriptionText = $descriptionText.'<br>PD:<rad style = "text-align: left; margin-left: 15px;"> '.'#####'.' &#956;<i>W</i>/<i>m</i><sup>2</sup><i>Sr</i></rad>';
		}
		else if ($PMTData == '' || $PDData == '') {
			$descriptionText = $descriptionText.'Current Status: Inactive';
			$descriptionText = $descriptionText.'<br>PMT:<rad style = "text-align: left; margin-left: 10px;"> '.'#####'.' &#956;<i>W</i>/<i>m</i><sup>2</sup><i>Sr</i></rad>';
			$descriptionText = $descriptionText.'<br>PD:<rad style = "text-align: left; margin-left: 15px;"> '.'#####'.' &#956;<i>W</i>/<i>m</i><sup>2</sup><i>Sr</i></rad>';
		}
		else {
			$descriptionText = $descriptionText.'Current Status: On';
			$descriptionText = $descriptionText.'<br>PMT:<rad style = "text-align: left; margin-left: 10px;"> '.$PMTData.' &#956;<i>W</i>/<i>m</i><sup>2</sup><i>Sr</i></rad>';
			$descriptionText = $descriptionText.'<br>PD:<rad style = "text-align: left; margin-left: 15px;"> '.$PDData.' &#956;<i>W</i>/<i>m</i><sup>2</sup><i>Sr</i></rad>';
		}
		/*$descriptionText = $descriptionText.'<p>Current Status: No Aurora';
		$descriptionText = $descriptionText.'<br>PMT:<rad style = "text-align: left; margin-left: 10px;">'.$PMTData.' &#956;W/m<sup>2</sup>Sr</rad>';
		$descriptionText = $descriptionText.'<br>PD:<rad style = "text-align: left; margin-left: 15px;">'.$PDData.' &#956;W/m<sup>2</sup>Sr</rad></p></text>';*/
		$description = $dom->createElement('description', $descriptionText);
		$descriptionNode = $placemarkNode->appendChild($description);
		
		$point = $dom->createElement('Point');
		$pointNode = $placemarkNode->appendChild($point);
		
		$coordinates = $dom->createElement('coordinates', $detectors['coordinates'][$ii]);
		$coordinatesNode = $pointNode->appendChild($coordinates);
	}
	
	//Tell the browser the output is XML via the 'Content-Type' HTTP header
	header('Content-Type: text/xml');
	
	//Display DOM document
	echo $dom->saveXML();
?>