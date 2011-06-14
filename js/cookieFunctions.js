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
		var timezoneSetting = settings.substring(2);
	} else {
		var nameLengthSetting = 1;
		var showAuroralOvalSetting = 1;
		var timezoneSetting = 'UTC';
		setCookie('AuroraDetectorNetworkSettings','11UTC',365);
	}
	return [nameLengthSetting, showAuroralOvalSetting, timezoneSetting];
}	

function toggleSettings(setting) {
	var displaySettings = checkSettings();
	var nameLengthSetting = displaySettings[0];
	var showAuroralOvalSetting = displaySettings[1];
	var timezoneSetting = displaySettings[2];
	
	if (setting == 'nameLength') {
		if (nameLengthSetting == 0) {
			nameLengthSetting = 1;
		} else {
			nameLengthSetting = 0;
		}
	} else if (setting == 'oval') {
		if (showAuroralOvalSetting == 0) {
			showAuroralOvalSetting = 1;
		} else {
			showAuroralOvalSetting = 0;
		}	
	} else {
		timezoneSetting = setting;
	}
	
	var settings = ''+nameLengthSetting+showAuroralOvalSetting+timezoneSetting;
	setCookie('AuroraDetectorNetworkSettings',settings,365);
}