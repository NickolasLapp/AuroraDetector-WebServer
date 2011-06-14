<?php
	$kml = array('<?xml version="1.0" encoding="UTF-8"?>');
	
	$kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2">';
	
	$kml[] = ' <NetworkLinkControl>';
	
	$now = gmdate("Y-m-d H:i:s");
	
	$expiration_time = strtotime($now) + 10;
	$expires = date("Y-m-d H:i:s", $expiration_time);
	
	$kml[] = ' <expires>'.$expires.'</expires>';

	$kml[] = ' <Folder>';
	
	$kml[] = ' <open>1</open>';
	
	$kml[] = ' <Document>';
	$kml[] = ' <name>Montana Aurora Detector Network</name>';
			
	$kml[] = ' <atom:author>';
	$kml[] = ' <atom:name>Optical Remote Sensor Laboratory, Montana State University</atom:name>';
	$kml[] = ' </atom:author>';
			
	$kml[] = ' <LookAt id="Montana">';
	$kml[] = ' <latitude>46.879682</latitude>';
	$kml[] = ' <longitude>-110.0</longitude>';
	$kml[] = ' <range>1500000</range>';
	$kml[] = ' <tilt>0</tilt>';
	$kml[] = ' <heading>0</heading>';
	$kml[] = ' </LookAt>';
			
	$kml[] = ' <open>1</open>';
			
	$kml[] = ' <Placemark>';
	$kml[] = ' <name>Montana State University</name>';
	$kml[] = ' <Style id="detector">';
	$kml[] = ' <BalloonStyle><text>$[description]</text></BalloonStyle>';
		
	$kml[] = ' <Icon>';
	$kml[] = ' <href>http://www.coe.montana.edu/ee/weather/aurora/images/BZN1-status.png</href>';
	$kml[] = ' </Icon>';

	$kml[] = ' </Style>';
	$kml[] = ' <description>Aurora Detector on top of Cobleigh Hall on the campus of Montana State University, Bozeman.</description>';
				
	$kml[] = ' <Point><coordinates>-111.045955555556,45.6666027777778,0</coordinates></Point>';
	$kml[] = ' </Placemark>';
			
	$kml[] = ' <Placemark>';
	$kml[] = ' <name>Salish Kootenai College</name>';
				
	$kml[] = ' <description>Aurora Detector on the campus of Salish Kootenai College.</description>';
				
	$kml[] = ' <Point><coordinates>-114.107188, 47.596867,0</coordinates></Point>';
				
	$kml[] = ' <BalloonStyle><text>$[description]</text></BalloonStyle>';
	$kml[] = ' </Placemark>';
			
	$kml[] = ' <Placemark>';
	$kml[] = ' <name>Little Big Horn College</name>';
				
	$kml[] = ' <description>Aurora Detector on the campus of Little Big Horn College.</description>';
				
	$kml[] = ' <Point><coordinates>-107.456703, 45.603839,0</coordinates></Point>'
				
	$kml[] = ' <BalloonStyle><text>$[description]</text></BalloonStyle>';
	$kml[] = ' </Placemark>';
			
	$kml[] = ' <Placemark>';
	$kml[] = ' <name>Fort Peck Community College</name>';
				
	$kml[] = ' <description>Aurora Detector on the campus of Fort Peck Community College.</description>';
				
	$kml[] = ' <Point><coordinates>-105.193191, 48.113830,0</coordinates></Point>';
				
	$kml[] = ' <BalloonStyle><text>$[description]</text></BalloonStyle>';
	$kml[] = ' </Placemark>';
			
	$kml[] = ' <Placemark>';
	$kml[] = ' <name>Fort Belknap College</name>';
				
	$kml[] = ' <description>Aurora Detector on the campus of Fort Belknap College.</description>';
				
	$kml[] = ' <Point><coordinates>-108.759900, 48.482213,0</coordinates></Point>';
				
	$kml[] = ' <BalloonStyle><text>$[description]</text></BalloonStyle>';
	$kml[] = ' </Placemark>';
			
	$kml[] = ' <Placemark>';
	$kml[] = ' <name>Chief Dull Knife College</name>';
				
	$kml[] = ' <description>Aurora Detector on the campus of Chief Dull Knife College.</description>';
				
	$kml[] = ' <Point><coordinates>-106.666902, 45.622809,0</coordinates></Point>';
				
	$kml[] = ' <BalloonStyle><text>$[description]</text></BalloonStyle>';
	$kml[] = ' </Placemark>';
			
	$kml[] = ' <Placemark>';
	$kml[] = ' <name>Stone Child College</name>';
				
	$kml[] = ' <description>Aurora Detector on the campus of Stone Child College.</description>';
				
	$kml[] = ' <Point><coordinates>-109.783128, 48.257473,0</coordinates></Point>';
				
	$kml[] = ' <BalloonStyle><text>$[description]</text></BalloonStyle>';
	$kml[] = ' </Placemark>';
			
	$kml[] = ' <Placemark>';
	$kml[] = ' <name>Blackfeet Community College</name>';
				
	$kml[] = ' <description>Aurora Detector on the campus of Blackfeet Community College.</description>';
				
	$kml[] = ' <Point><coordinates>-113.010239, 48.548064,0</coordinates></Point>';
				
	$kml[] = ' <BalloonStyle><text>$[description]</text></BalloonStyle>';
	$kml[] = ' </Placemark>';
		
	$kml[] = ' </Document>';
	$kml[] = ' </Folder>';
	$kml[] = ' </kml>';
	
	// End XML file
	$kml[] = ' </Document>';
	$kml[] = '</kml>';
	$kmlOutput = join("\n", $kml);
	header('Content-type: application/vnd.google-earth.kml+xml');
	echo $kmlOutput;
?>