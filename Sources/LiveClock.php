<?php

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

if (!defined('SMF'))
	die('Hacking attempt...');


function LC_includeAssets() {
	global $settings, $context;

	loadlanguage('LikePosts');
	$context['insert_after_template'] .= '
	<script type="text/javascript"><!-- // --><![CDATA[
		var lpLoaded = false,
		inConflict = false;

		function compareScriptVersion(v1, v2, callback) {
			var v1parts = v1.split('.');
			var v2parts = v2.split('.');

			for (var i = 0; i < v1parts.length; ++i) {
				if (v2parts.length == i) {
					//v1 + " is larger"
					callback(1);
					return;
				}

				if (v1parts[i] == v2parts[i]) {
					continue;
				} else if (v1parts[i] > v2parts[i]) {
					//v1 + " is larger";
					callback(1);
					return;
				} else {
					//v2 + " is larger";
					callback(2);
					return;
				}
			}

			if (v1parts.length != v2parts.length) {
				//v2 + " is larger";
				callback(2);
				return;
			}
			callback(false);
			return;
		}

		function loadScript(url, callback) {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = url;

			var head = document.getElementsByTagName("head")[0],
				done = false;

			script.onload = script.onreadystatechange = function() {
				if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
					done = true;
					callback();
					script.onload = script.onreadystatechange = null;
					head.removeChild(script);
				};
			};
			head.appendChild(script);
		}

		// Only do anything if jQuery isn"t defined
		if (typeof(jQuery) == "undefined") {
			console.log("jquery not found");
			if (typeof($) == "function") {
				console.log("jquery but in conflict");
				inConflict = true;
			}

			loadScript("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
				if (typeof(jQuery) !=="undefined") {
					console.log("directly loaded with version: " + jQuery.fn.jquery);
					lc_jquery2_0_3 = jQuery.noConflict(true);
					loadLCScript();
				}
			});
		} else {
			// jQuery is already loaded
			console.log("jquery is already loaded with version: " + jQuery.fn.jquery);
			compareScriptVersion(jQuery.fn.jquery, "2.0.3", function(result) {
				console.log("result of version check: " + result)
				switch(result) {
					case false:
					case 1:
						lc_jquery2_0_3 = jQuery.noConflict(true);
						loadLCScript();
						break;

					case 2:
						loadScript("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
							if (typeof(jQuery) !=="undefined") {
								console.log("after version check loaded with version: " + jQuery.fn.jquery);
								lc_jquery2_0_3 = jQuery.noConflict(true);
								loadLCScript();
							}
						});
						break;

					default:
						loadScript("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
							if (typeof(jQuery) !=="undefined") {
								console.log("default version check loaded with version: " + jQuery.fn.jquery);
								lc_jquery2_0_3 = jQuery.noConflict(true);
								loadLCScript();
							}
						});
						break;
				}
			})
		};

		function loadLCScript() {
			var js = document.createElement("script");
			js.type = "text/javascript";
			js.src = "' . $settings['default_theme_url'] . '/scripts/LiveClock.js";
			document.body.appendChild(js);
		}
	// ]]></script>';

	LC_checkJsonEncode();
}

function LC_checkJsonEncode() {
	if (!function_exists('json_encode')) {
		function json_encode($a = false) {

			switch(gettype($a)) {
				case 'integer':
				case 'double':
					return floatval(str_replace(",", ".", strval($a)));
				break;

				case 'NULL':
				case 'resource':
				case 'unknown':
					return 'null';
				break;

				case 'boolean':
					return $a ? 'true' : 'false' ;
				break;

				case 'array':
				case 'object':
					$output = array();
					$isAssoc = false;

					foreach(array_keys($a) as $key) {
						if (!is_int($key)) {
							$isAssoc = true;
							break;
						}
					}

					if($isAssoc) {
						foreach($a as $k => $val) {
							$output []= json_encode($k) . ':' . json_encode($val);
						}
						$output = '{' . implode(',', $output) . '}';
					} else {
						foreach($a as $val){
							$output []= json_encode($val);
						}
						$output = '[' . implode(',', $output) . ']';
					}
					return $output;
				break;

				default:
				return '"' . addslashes($a) . '"';
			}
		}
	}
}

function LC_mainIndex() {
	global $context;

	loadLanguage('LiveClock');
	$default_action_func = 'LC_showClock';
	$subActions = array(
		'showclock' => 'LC_showClock',
		'updateusertimezone' => 'LC_updateUserTimezone'
	);

	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']]))
		return $subActions[$_REQUEST['sa']]();
	$default_action_func();
}

function LC_showClock() {
	global $context, $modSettings, $settings, $user_info, $sourcedir;

	// Check to avoid uninstall error
	$file_path = $sourcedir . '/Subs-LiveClock.php';
	if (!file_exists($file_path)) {
		return false;
	}
	require_once($sourcedir . '/Subs-LiveClock.php');
	$context['live_clock_timezones'] = LC_getALlTimeZones();

	if (!$user_info['is_guest']) {
		$user_info['custom_timezone'] = LC_getUserTimezone();
	}

	$timezone = '';
	if (isset($user_info['custom_timezone'])) {
		$timezone = $user_info['custom_timezone'];
	}
	$hour_format = !empty($modSettings['lc_24_hr_format']) ? 'true' : 'false';
	$showTimezoneDropdown = !empty($modSettings['lc_show_timezone_dropdown']) ? 'true' : 'false';
	$showDate = !empty($modSettings['lc_show_date']) ? 'true' : 'false';

	$context['insert_after_template'] .= '
	<script type="text/javascript"><!-- // --><![CDATA[
		window.onload = function() {
			var params = {
				timezone : "'. $timezone .'",
				req24hrFormat : "'. $hour_format .'",
				timezoneoptions: '. json_encode($context['live_clock_timezones']) .',
				showTimezoneDropdown : "'. $showTimezoneDropdown .'",
				displayDate : "'. $showDate .'"
			}
			liveClock.init(params);
		}
	// ]]></script>';
}

function LC_updateUserTimezone() {
	global $sourcedir, $user_info, $txt;

	if ($user_info['is_guest']) {
		$resp = array('response' => false, 'error' => $txt['error_guests_not_allowed']);
		echo json_encode($resp);
		die();	
	}

	if (!isset($_REQUEST['timezone']) || empty($_REQUEST['timezone'])) {
		$resp = array('response' => false, 'error' => $txt['error_blank_value']);
		echo json_encode($resp);
		die();
	}

	$timezoneVal = (int) $_REQUEST['timezone'];

	require_once($sourcedir . '/Subs-LiveClock.php');
	$result = LC_updateUserDBZone($timezoneVal);
	if ($result) {
		$resp_pass = array('response' => true);
		echo json_encode($resp_pass);
		die();
	} else {
		$resp = array('response' => false, 'error' => $txt['error_updating_timezone']);
		echo json_encode($resp);
		die();
	}
	return;
}

?>
