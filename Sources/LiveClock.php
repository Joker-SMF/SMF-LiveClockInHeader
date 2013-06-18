<?php

/**
* @package manifest file for Live clock in header
* @version 1.1.1
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

	$context['insert_after_template'] .= '
	<script type="text/javascript">
		var head= document.getElementsByTagName("head")[0];
		var script= document.createElement("script");
		script.type= "text/javascript";
		if (!window.jQuery) {
			document.write("<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js\"><\/script>");
		}
	</script>';

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
		function isJqueryLoaded() {
			if (window.jQuery) {
				console.log("jquery loaded");
				var head= document.getElementsByTagName("head")[0];
				var script= document.createElement("script");
				script.type= "text/javascript";

				document.write("<script type=\"text/javascript\" src=\"'. $settings['default_theme_url']. '/scripts/LiveClock.js\"><\/script>");
				liveClockInit();
			} else {
				console.log("jquery not loaded");
				setTimeout(isJqueryLoaded, 50);
			}
		}

		function liveClockInit() {
			if(window.liveClock !== undefined) {
				var params = {
					timezone : "'. $timezone .'",
					use24hrFormat : "'. $hour_format .'",
					timezoneoptions: '. json_encode($context['live_clock_timezones']) .',
					showTimezoneDropdown : "'. $showTimezoneDropdown .'",
					showDate : "'. $showDate .'"
				}
				liveClock.initialize(params);
			} else {
				setTimeout(liveClockInit, 50);
			}
		}

		isJqueryLoaded();
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