<?php	
	include_once 'constants.php';
	
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
	
	//TODO: Make this link dynamic
	$descriptionText = '<p><h3 style = "margin-right: 130px; font-size: 12px; font-weight: normal;">Overlays the current status of each of the eight detectors in the Montana Aurora Detector Network.<br><br>This network is operated by the Optical Remote Sensor Laboratory on the campus of Montana State University, Bozeman.</p><p><a href="http://orsl.eps.montana.edu/betaWebsite/aurora/signup.php?ID=state"><i>Sign up for text message aurora alerts</i></a></h3><h6 style = "font-size: 10px; font-weight: normal; text-align: center;">The data refreshes every minute to provide you with the most current information possible.</h6></p>';
	//TODO: Make this link dynamic
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
	
	$latitude = $dom->createElement('latitude','46.879682');
	$latitudeNode = $lookAtNode->appendChild($latitude);
	
	$longitude = $dom->createElement('longitude','-109.75');
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
		
		$descriptionText = '<h3 style = "text-align: left";>'.$detectors['names'][$ii].'</h3>';
		$descriptionText = $descriptionText.'<p><h3 style = "font-size: 12px; font-weight: normal;">Aurora detector on the campus of '.$detectors['names'][$ii].'.</h3>';
		$descriptionText = $descriptionText.'</p>';
		$descriptionText = $descriptionText.'Current Status: No Aurora';
		$descriptionText = $descriptionText.'<br>PMT:<rad style = "text-align: left; margin-left: 10px;">'.'#####'.' &#956;<i>W</i>/<i>m</i><sup>2</sup><i>Sr</i></rad>';
		$descriptionText = $descriptionText.'<br>PD:<rad style = "text-align: left; margin-left: 15px;">'.'#####'.' &#956;<i>W</i>/<i>m</i><sup>2</sup><i>Sr</i></rad>';
		
		//TODO: Make these lower links dynamic
		$descriptionText = $descriptionText.'<br><br><a href="http://orsl.eps.montana.edu/betaWebsite/aurora/units.php"><i>About our units</i></a>';
		$descriptionText = $descriptionText.'<br><a href="http://orsl.eps.montana.edu/betaWebsite/aurora/signup.php?ID='.$detectors['IDs'][$ii].'"><i>Sign up for text message aurora alerts</i></a>';
		
		$description = $dom->createElement('description', $descriptionText);
		$descriptionNode = $placemarkNode->appendChild($description);
		
		$point = $dom->createElement('Point');
		$pointNode = $placemarkNode->appendChild($point);
		
		$coordinates = $dom->createElement('coordinates', $detectors['coordinates'][$ii]);
		$coordinatesNode = $pointNode->appendChild($coordinates);
	}
	
	//Set up network link to NOAA's Auroral Activity overal
	$networkLinkToNOAA = $dom->createElement('NetworkLink');
	$networkLinkToNOAANode = $folderNode->appendChild($networkLinkToNOAA);
	$networkLinkToNOAANode->setAttribute('id', 'Auroral_Oval');
	
	$networkLinkToNOAAName = $dom->createElement('name', 'Auroral Oval Activity<br>(extrapolated from NOAA POES)');
	$networkLinkToNOAANameNode = $networkLinkToNOAANode->appendChild($networkLinkToNOAAName);
	
	$networkLinkToNOAADescription = $dom->createElement('description','Overlays show the current extent and position of the auroral oval at each pole, extrapolated from measurements taken during the most recent polar pass of the NOAA POES satellite.<br><br>This data is from the NOAA Space Weather Prediction Center\'s Google Earth Layer and is the sole property of the NOAA Space Weather Prediction Center.<br><br><a href="http://www.swpc.noaa.gov">NOAA Space Weather Predition Center</a>');
	$networkLinkToNOAADescriptionNode = $networkLinkToNOAANode->appendChild($networkLinkToNOAADescription);
	
	$networkLinkToNOAAAddress = $dom->createElement('address', '325 South Broadway, Boulder, CO');
	$networkLinkToNOAAAddressNode = $networkLinkToNOAANode->appendChild($networkLinkToNOAAAddress);
	
	$NOAALink = $dom->createElement('Link');
	$NOAALinkNode = $networkLinkToNOAANode->appendChild($NOAALink);	
	
	$NOAALinkHref = $dom->createElement('href','http://www.swpc.noaa.gov/pmap/GEpmap/AuroralOval.kml');
	$NOAALinkHrefNode = $NOAALinkNode->appendChild($NOAALinkHref);
	
	$NOAALinkRefreshMode = $dom->createElement('refreshMode','onInterval');
	$NOAALinkRefreshModeNode = $NOAALinkNode->appendChild($NOAALinkRefreshMode);
	
	$NOAALinkRefreshInterval = $dom->createElement('refreshInterval','60');
	$NOAALinkRefreshIntervalNode = $NOAALinkNode->appendChild($NOAALinkRefreshInterval);
	
	$NOAALinkVisibility = $dom->createElement('visibility','1');
	$NOAALinkVisibilityNode = $networkLinkToNOAANode->appendChild($NOAALinkVisibility);
	
	$NOAALinkRefreshVisibility = $dom->createElement('refreshVisibility', '0');
	$NOAALinkRefreshVisibilityNode = $networkLinkToNOAANode->appendChild($NOAALinkRefreshVisibility);
	
	$NOAALinkFlyToView = $dom->createElement('flyToView','0');
	$NOAALinkFlyToViewNode = $networkLinkToNOAANode->appendChild($NOAALinkFlyToView);
	
	
	//Tell the browser the output is XML via the 'Content-Type' HTTP header
	header('Content-Type: text/xml');
	
	//Display DOM document
	echo $dom->saveXML();
?>