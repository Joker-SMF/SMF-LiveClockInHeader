/**
 * @package manifest file for Live clock in header
 * @version 1.2
 * @author Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
 * @copyright Copyright (c) 2012, Siddhartha Gupta
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

/*
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Initial Developer of the Original Code is
 *  Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
 * Portions created by the Initial Developer are Copyright (C) 2012
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 */

/*
 * params -
 * timezone(use php offset/let mod calculate/timezone selected by user, depends on admin panel settings)
 * show time in am/pm if selected by admin
 * time string (decided by admin/mod default/type used by forum)
 */

var liveClock = (function(jQRef, win) {
	var paramsObj = null,
		timer = null,
		monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],

		init = function(params) {
			var docId = document.getElementById('live_clock'),
				offset = null;

			clearTimeout(timer);
			timer = null;

			if (!docId) {
				timer = setTimeout(function() {
					initialize(params);
				}, 1000);
				return;
			}

			if (typeof(params) === 'undefined') {
				return;
			}

			paramsObj = paramsObj || params;
			paramsObj.timezoneoptions = (paramsObj.timezoneoptions) ? paramsObj.timezoneoptions : {};

			if (paramsObj.timezone !== '' && paramsObj.timezone !== undefined && paramsObj.timezone !== null) {
				offset = paramsObj.timezone;
			} else {
				offset = Math.abs(new Date().getTimezoneOffset() / 60);
			}

			var user24hrFormat = (paramsObj.use24hrFormat == 'true') ? true : false;
			d = new Date();
			utc = d.getTime() + (d.getTimezoneOffset() * 60000);
			nd = new Date(utc + (3600000 * +offset));

			var s = nd.getSeconds(),
				m = nd.getMinutes(),
				h = nd.getHours(),
				date = nd.getDate(),
				month = monthsList[nd.getMonth()],
				year = nd.getFullYear(),
				am_pm = null;

			if (s < 10) {
				s = '0' + s;
			}
			if (m < 10) {
				m = '0' + m;
			}
			if (!user24hrFormat) {
				if (h > 12) {
					h = h - 12;
					am_pm = ' pm';
				} else {
					am_pm = ' am';
				}
			}
			if (h < 10) {
				h = '0' + h;
			}
			var time = '';
			if (paramsObj.showDate === 'true') time += month + ' ' + date + ', ' + year + ', ';

			if (!user24hrFormat) time += h + ':' + m + ':' + s + am_pm;
			else time += h + ':' + m + ':' + s;

			docId.innerHTML = time;

			if (paramsObj.showTimezoneDropdown === "true") {
				if (jQRef('#live_clock_timezone_options').is(':hidden')) jQRef('#live_clock_timezone_options').show();
				var sel = document.getElementById('live_clock_timezone_options'),
					items = sel.getElementsByTagName('option');

				if (items.length !== Object.keys(paramsObj.timezoneoptions).length) {
					var opt = null;

					if (Object.keys(paramsObj.timezoneoptions).length > 0) {
						for (var i in paramsObj.timezoneoptions) {
							var current = paramsObj.timezoneoptions[i],
								zone_diff = parseFloat(current.zone_diff);

							opt = document.createElement('option');
							opt.value = current.id_zone;
							opt.innerHTML = current.zone_name;

							if (zone_diff == offset) {
								opt.selected = 'selected';
							}
							sel.appendChild(opt);
						}
					}
				}
			}
			timer = setTimeout(function() {
				liveClock.init(paramsObj);
			}, 1000);
		},

		onTimezoneChange = function(zone) {
			jQRef.post('index.php', {
				action: 'liveclock',
				sa: 'updateusertimezone',
				timezone: zone
			}, function(data, textStatus, jqXHR) {
				if (typeof(data) === 'undefined') {
					alert('Something went wrong. Please try again');
				} else if (typeof(data.response) !== 'undefined' && data.response === true) {
					for (var i in paramsObj.timezoneoptions) {
						var current = paramsObj.timezoneoptions[i],
							id_zone = current.id_zone,
							zone_diff = parseFloat(current.zone_diff);

						if (id_zone == zone) {
							paramsObj.timezone = zone_diff;
						}
					}
				} else if (typeof(data.response) !== 'undefined' && data.response === false) {
					if (typeof(data.error) !== 'undefined' && data.error !== '') {
						alert(data.error);
					}
				}
			}, 'json');
			return;
		};

	return {
		init: init,
		onTimezoneChange: onTimezoneChange
	};
}(lc_jquery2_0_3, window));
