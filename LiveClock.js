/*
 * params -
 * timezone(use php offset/let mod calculate/timezone selected by user, depends on admin panel settings)
 * show time in am/pm if selected by admin
 * time string (decided by admin/mod default/type used by forum)
*/

var liveClock = {
	userTimeZone : null,
	allTimeZones : null,
	timeoutCounter: null,
	paramsObj : null,
};


liveClock.initialize = function (params) {
	var docId = document.getElementById('live_clock');
	if(!docId) {
		setTimeout(function() {
			refrClock();
		},1000);
		return;
	}

	if(params == undefined) {
		return;
	}

	liveClock.allTimeZones = (params.timezoneoptions) ? params.timezoneoptions : {};
	if(params.timezone !== '' && params.timezone !== undefined && params.timezone !== null) {
		var offset = params.timezone;
	} else if (liveClock.userTimeZone !== '') {
		var offset = liveClock.userTimeZone;
	} else {
		var offset = Math.abs(new Date().getTimezoneOffset()/60);
	}

	liveClock.paramsObj = params;
	var user24hrFormat = (params.use24hrFormat == 'true') ? true : false;
	d = new Date();
	utc = d.getTime() + (d.getTimezoneOffset() * 60000);
	nd = new Date(utc + (3600000 *+ offset));

	var s = nd.getSeconds(),
		m = nd.getMinutes(),
		h = nd.getHours();
	if(user24hrFormat) var am_pm;

	if (s < 10) {
		s = '0' + s;
	}
	if (m < 10) {
		m ='0' + m;
	}
	if(user24hrFormat) {
		if (h > 12) {
			h = h - 12;
			am_pm = 'pm'
		} else {
			am_pm = 'am';
		}	
	}
	if (h < 10) {
		h= '0' + h;
	}
	if(user24hrFormat) var time = h + ':' + m + ':' + s + am_pm;
	else var time = h + ':' + m + ':' + s;
	docId.innerHTML= time;

	var sel = document.getElementById('live_clock_timezone_options');
	var items = sel.getElementsByTagName('option');

	if(items.length !== Object.keys(liveClock.allTimeZones).length) {
		var opt = null;
		if(Object.keys(liveClock.allTimeZones).length > 0) {
			for(i in liveClock.allTimeZones) {
				var zone_diff = parseFloat(liveClock.allTimeZones[i].zone_diff);
				opt = document.createElement('option');
				opt.value = liveClock.allTimeZones[i].id_zone;
				opt.innerHTML = liveClock.allTimeZones[i].zone_name;
				if(zone_diff == offset) {
					opt.selected = 'selected';
				}
				sel.appendChild(opt);
			}
		}	
	}	
	liveClock.timeoutCounter = setTimeout("liveClock.initialize(liveClock.paramsObj)",1000);
}

liveClock.onTimezoneChange = function(zone) {
	$.post('index.php', {
		action: 'liveclock',
		sa: 'updateusertimezone',
		timezone: zone
	}, function(data, textStatus, jqXHR) {
		// use data here
		for(i in liveClock.allTimeZones) {
			var id_zone = liveClock.allTimeZones[i].id_zone;
			var zone_diff = parseFloat(liveClock.allTimeZones[i].zone_diff);
			if(id_zone == zone) {
				console.log('match')
				liveClock.paramsObj.timezone = zone_diff;
			}
		}
		clearTimeout(liveClock.timeoutCounter);
		liveClock.timeoutCounter = setTimeout("liveClock.initialize(liveClock.paramsObj)",1000);
	});
}