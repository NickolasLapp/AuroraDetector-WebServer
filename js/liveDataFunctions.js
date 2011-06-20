function init() {
	google.earth.createInstance('map3d', initCB, failureCB);
	}
	
function initCB(instance) {
	ge = instance;
	ge.getWindow().setVisibility(true);
	ge.getNavigationControl().setVisibility(ge.VISIBILITY_AUTO);
	ge.getLayerRoot().enableLayerById(ge.LAYER_BORDERS, true);
	document.getElementById('installed-plugin-version').innerHTML =	ge.getPluginVersion().toString();
	
	// Load the correct layer based on cookie and set button initial state
	var settings = checkSettings();
	if (settings[0] == 0) {
		loadKml('abbrev');
	} else {
		loadKml('full');
	}
	if (settings[1] == 1) {
		loadKml('oval');
	}
	lookMt();
}

function loadKml(file) {
	var ts = new Date().getTime();
	if (file == 'full') {
		var kmlUrl = 'http://orsl.eps.montana.edu/betaWebsite/aurora/AuroraNetwork_Web.php?time='+ts;
	} else if (file == 'abbrev') {
		var kmlUrl = 'http://orsl.eps.montana.edu/betaWebsite/aurora/AuroraNetwork_Web-Abbreviated.php?time='+ts;
	} else if (file == 'oval') {
		var kmlUrl = 'http://orsl.eps.montana.edu/betaWebsite/aurora/AuroraNetwork_Web-NOAAAuroralOval.kml';
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

function toggleKml(file) {
	// remove the old KML object if it exists
	if (currentKmlObjects[file]) {
		ge.getFeatures().removeChild(currentKmlObjects[file]);
		currentKmlObjects[file] = null;
	} else {
		loadKml(file);	
	}
}

function checkMap() {
	// This function is used to eliminate additional google earth objects that may be created if the user clicks a button too quickly
	number_of_objects = ge.getFeatures().getChildNodes().getLength();
}

function failureCB(errorCode) {	}

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

function resize() {
	var map_height = ($(window).height()-$('#masthead').height()-$('#tab1_heading').height()-$('#installed-plugin-version').height())-40;
	var map_width = ($(window).width()-$('.ui-tabs-nav').width())-180;
	
	if ($(window).height() > 600) {
		$('#map3d').height(map_height);
	} else {
		$('#map3d').height('426');
	}
	if ($(window).width() > 856) {
		$('#map3d').width(map_width);
	} else {
		$('#map3d').width('464');
		$(document).width('856');
	}
	$('#sizeReporter').text($(window).width()+' by '+$(window).height()+' map3d: '+$('#map3d').width()+' by '+$('#map3d').height());
	$('img.msu').css('left', (($('#map3d').offset()).left+($('#map3d').width())-109)+'px' ).css('top', (($('#masthead').offset()).top-10)+'px');
	$('#masthead').width(($('img.msu').offset()).left+235);
	$('#tab1_toolbar').width($('#map3d').width()-5);
	$('img.legend').attr('src', 'images/legend.php?width=90&height='+($('#map3d').height()));
	$('img.legend').css('top', (($('#map3d').position()).top)+'px');
	var settings = checkSettings();
	if (settings[1] == 1) {
		$('img.legend').css('left', (($('#map3d').position()).left+$('#map3d').width()+5)+'px').show();
	} else {
		$('img.legend').css('left', (($('#map3d').position()).left+$('#map3d').width()-100)+'px');
	}
}

function initialize_toolbar() {
	var settings = checkSettings();
	if (settings[0] == 0) {
		$('#name_length').removeAttr('checked').button("refresh");
	} else {
		$('#name_length').attr('checked', 'checked').button("refresh");
	}
	if (settings[1] == 1) {
		$('#show_oval').attr('checked', 'checked').button("refresh");
	} else {
		$('#show_oval').removeAttr('checked').button("refresh");
		$( "#view_oval" ).attr('disabled', true).button("refresh");
	}
}

function name_checked() {
	toggleSettings('nameLength');
	var settings = checkSettings();
	if (settings[0] == 1) {
		toggleKml('abbrev');
		toggleKml('full');
	} else {
		toggleKml('full');
		toggleKml('abbrev');
	}
}

function oval_checked() {
	toggleSettings('oval');
	toggleKml('oval');
	var settings = checkSettings();
	if (settings[1] == 1) {
		$( "#view_oval" ).attr('disabled', false).button("refresh");
		$('img.legend').show().animate({left: '+=105'},300);
	} else {
		$( "#view_oval" ).attr('disabled', true).button("refresh");
		$('img.legend').animate({left: '-=105'},300, function() {
	    	$(this).hide();	
		});
	}
}

function toggle_demo_buttons() {
	if ($('#demo_name_length').is(":checked")) { 
		$('#demo_name_state').text('Shows full college names');
	} else {
		$('#demo_name_state').text('Shows abbreviated college names');
	}
	if ($('#demo_show_oval').is(":checked")) { 
		$('#demo_oval_state').text('Shows the auroral oval');
	} else {
		$('#demo_oval_state').text('Hides the auroral oval');
	}
}