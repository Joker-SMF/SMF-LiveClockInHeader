/**
 * @package manifest file for Live clock in header
 * @version 1.3
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

(function(jQRef, win) {
	function liveClock() {
		var paramsObj = null,
			timer = null,
			monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],

			init = function(params) {
				if (isNullUndefined(params)) {
					return false;
				}
				paramsObj = paramsObj || params;

				// Check if elem exist
				var docId = document.getElementById('live_clock');
				if (!docId) {
					timer = setTimeout(function() {
						init(paramsObj);
					}, 1000);
					return false;
				}

				clearTimeout(timer);
				timer = null;

				// add custom values to paramsObj
				paramsObj.docId = docId;
				paramsObj.timezoneoptions = (paramsObj.timezoneoptions) ? paramsObj.timezoneoptions : {};
				paramsObj.user24hrFormat = (paramsObj.req24hrFormat == 'true') ? true : false;
				paramsObj.showDate = (paramsObj.displayDate == 'true') ? true : false;

				if (isNullUndefined(paramsObj.timezone)) {
					paramsObj.timezone = Math.abs(new Date().getTimezoneOffset() / 60);
				}
				executeClock();
			},

			onTimezoneChange = function(zone) {
				jQRef.post('index.php', {
					action: 'liveclock',
					sa: 'updateusertimezone',
					timezone: zone
				}, function(data, textStatus, jqXHR) {
					if (isNullUndefined(data)) {
						alert('Something went wrong. Please try again');
					} else if (!isNullUndefined(data.response) && data.response === true) {
						for (var i in paramsObj.timezoneoptions) {
							var current = paramsObj.timezoneoptions[i],
								id_zone = current.id_zone,
								zone_diff = parseFloat(current.zone_diff);

							if (id_zone == zone) {
								paramsObj.timezone = zone_diff;
							}
						}
					} else if (!isNullUndefined(data.response) && data.response === false) {
						if (!isNullUndefined(data.error)) {
							alert(data.error);
						}
					}
				}, 'json');
				return;
			},

			executeClock = function() {
				var d = new Date(),
					utc = d.getTime() + (d.getTimezoneOffset() * 60000),
					nd = new Date(utc + (3600000 * +paramsObj.timezone)),
					s = nd.getSeconds(),
					m = nd.getMinutes(),
					h = nd.getHours(),
					date = nd.getDate(),
					month = monthsList[nd.getMonth()],
					year = nd.getFullYear(),
					am_pm = null,
					time = '';

				if (s < 10) {
					s = '0' + s;
				}

				if (m < 10) {
					m = '0' + m;
				}

				if (!paramsObj.user24hrFormat) {
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

				if (paramsObj.showDate) {
					time += month + ' ' + date + ', ' + year + ', ';
				}

				if (!paramsObj.user24hrFormat) {
					time += h + ':' + m + ':' + s + am_pm;
				} else {
					time += h + ':' + m + ':' + s;
				}
				paramsObj.docId.innerHTML = time;

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

								if (zone_diff == paramsObj.timezone) {
									opt.selected = 'selected';
								}
								sel.appendChild(opt);
							}
						}
					}
				}
				timer = setTimeout(function() {
					executeClock();
				}, 1000);
			},

			getType = function(obj) {
				return ({}).toString.call(obj).toLowerCase();
			},

			isNullUndefined = function(val) {
				var isNull = false,
					type = getType(val);

				switch (type) {
					case '[object array]':
						if (val.length === 0) {
							isNull = true;
						}
						break;

					case '[object object]':
						if (Object.keys(val).length === 0) {
							isNull = true;
						}
						break;

					default:
						if (typeof(val) === "undefined" || val === null || val === "" || val === "null" || val === "undefined") {
							isNull = true;
						}
				}
				return isNull;
			};

		return {
			init: init,
			onTimezoneChange: onTimezoneChange,
		};
	}

	win.liveClock = liveClock();
}(lc_jquery2_0_3, window));
