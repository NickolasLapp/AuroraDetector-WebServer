function toggleKml(file) {
	// remove the old KML object if it exists
	if (currentKmlObjects[file]) {
		ge.getFeatures().removeChild(currentKmlObjects[file]);
		currentKmlObjects[file] = null;
	} else {
		loadKml(file);	
	}
}

function loadKml(file) {
	if (file == 'full') {
		var kmlUrl = 'http://www.coe.montana.edu/ee/weather/aurora/AuroraNetwork_Web.php';
	} else if (file == 'abbrev') {
		var kmlUrl = 'http://www.coe.montana.edu/ee/weather/aurora/AuroraNetwork_Web-Abbreviated.php';
	} else if (file == 'oval') {
		var kmlUrl = 'http://www.coe.montana.edu/ee/weather/aurora/AuroraNetwork_Web-NOAAAuroralOval.kml';
	}
	
	// fetch the KML
	google.earth.fetchKml(ge, kmlUrl, function(kmlObject) {
	// NOTE: we still have access to the 'file' variable (via JS closures)
		if (kmlObject) {
			// show it on Earth
			currentKmlObjects[file] = kmlObject;
			ge.getFeatures().appendChild(kmlObject);
		} else {
			// bad KML
			currentKmlObjects[file] = null;
			
			// wrap alerts in API callbacks and event handlers
			// in a setTimeout to prevent deadlock in some browsers
			setTimeout(function() {
				alert('Bad or null KML.');
			}, 0);
		}
	});
}

function lookMt() {
	// Look at the entire network at the start
	var mt = ge.createLookAt('');
	
	// Set the position values
	mt.setLatitude(47);
	mt.setLongitude(-109.7);
	mt.setRange(1000000); //default is 0.0
	
	// Update the view in Google Earth
	ge.getView().setAbstractView(mt);
}

function lookOval() {
	// Look at the entire network at the start
	var oval = ge.createLookAt('');
	
	// Set the position values
	oval.setLatitude(63);
	oval.setLongitude(-109.7);
	oval.setRange(17500000); //default is 0.0
	
	// Update the view in Google Earth
	ge.getView().setAbstractView(oval);
}