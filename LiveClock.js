/*
 * params -
 * timezone(use php offset/let mod calculate/timezone selected by user, depends on admin panel settings)
 * show time in am/pm if selected by admin
 * time string (decided by admin/mod default/type used by forum)
*/

/*
 * Work left
 * use jquery
 * show dropdown for timezone (ummm bounceee effectee)?
*/

function refrClock(params) {
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
	if(params.timezone === '' || params.timezone === undefined) {
		var offset = Math.abs(new Date().getTimezoneOffset()/60);	
	} else {
		var offset = params.timezone;
	}

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
	setTimeout("refrClock(params)",1000);
}