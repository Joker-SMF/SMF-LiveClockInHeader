<?php

/**
* @package manifest file for Live clock in header
* @version 1.0 Alpha
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

loadLanguage('LiveClock');
loadtemplate('LiveClock');

function LiveClockAdminPanel($return_config = false) {
	global $txt, $scripturl, $context, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	$context['page_title'] = $txt['lc_admin_panel'];
	$default_action_func = 'LC_basicLiveClockSettings';

	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['lc_admin_panel'],
		'tabs' => array(
			'basicsettings' => array(
				'label' => $txt['lc_basic_settings'],
				'url' => 'basicsettings',
			),
			'timezones' => array(
				'label' => $txt['lc_timezone_settings'],
				'url' => 'displaytimezones',
			),
		),
	);
	$context[$context['admin_menu_name']]['tab_data']['active_button'] = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : 'basicsettings';

	$subActions = array(
		'basicsettings' => 'LC_basicLiveClockSettings',
		'savebasicsettings' => 'LC_saveBasicSettings',
		'displaytimezones' => 'LC_displayTimezones',
		'savetimezones' => 'LC_saveTimezones',
		'resetalltimezones' => 'LC_resetAllTimeZones',
	);

	//wakey wakey, call the func you lazy
	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']]))
		return $subActions[$_REQUEST['sa']]();
	$default_action_func();
}

function LC_basicLiveClockSettings() {
	global $txt, $scripturl, $context, $sourcedir, $user_info;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	require_once($sourcedir . '/ManageServer.php');
	$general_settings = array(
		array('check', 'lc_mod_enable', 'subtext' => $txt['lc_mod_enable_desc']),
		array('check', 'lc_forum_timezone_offset', 'subtext' => $txt['lc_forum_timezone_offset_desc']),
		array('check', 'lc_24_hr_format', 'subtext' => $txt['lc_24_hr_format_desc']),
	);

	$context['page_title'] = $txt['lc_admin_panel'];
	$context['sub_template'] = 'lc_admin_basic_setting_panel';
	$context['live_clock']['tab_name'] = $txt['lc_basic_settings'];
	$context['live_clock']['tab_desc'] = $txt['lc_basic_settings_desc'];
	prepareDBSettingContext($general_settings);
}

function LC_saveBasicSettings() {
	global $context, $sourcedir;

	isAllowedTo('admin_forum');

	if (isset($_POST['submit'])) {
		checkSession();

		$general_settings = array(
			array('check', 'lc_mod_enable'),
			array('check', 'lc_forum_timezone_offset'),
			array('check', 'lc_24_hr_format'),
		);

		require_once($sourcedir . '/ManageServer.php');
		saveDBSettings($general_settings);
		redirectexit('action=admin;area=liveclock;sa=basicsettings');
	}
}

function LC_displayTimezones() {
	global $context, $sourcedir, $txt;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	require_once('Subs-LiveClock.php');

	$context['live_clock_timezones'] = LC_getALlTimeZones();
	$context['page_title'] = $txt['lc_admin_panel'];
	$context['sub_template'] = 'lc_admin_timezone_setting_panel';
	$context['live_clock']['tab_name'] = $txt['lc_timezone_settings'];
	$context['live_clock']['tab_desc'] = $txt['lc_timezone_settings_desc'];
}

function LC_saveTimezones() {
	global $context, $sourcedir, $txt;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	require_once('Subs-LiveClock.php');

	$data = array();
	unset($_POST['submit']);
	foreach ($_POST as $key => $value) {
		$temp_data = explode('_', $key);

		//if i found something fishy, you are going back
		if (empty($temp_data[0]) || !is_numeric($temp_data[1])) {
			return false;
		}

		$key_name = $temp_data[0];
		$id_zone = (int) $temp_data[1];

		if ($key_name === 'timezonename') {
			if (isset($data[$id_zone])) {
				$data[$key_value]['zone_name'] = $value;
			} else {
				$data[$id_zone] = array(
					'zone_name' => $value,
				);
			}
		} else if ($key_name === 'timezonediff') {
			if (isset($data[$id_zone])) {
				$data[$id_zone]['zone_diff'] =  $value;
			} else {
				$data[$key_value] = array(
					'zone_diff' => $value,
				);
			}
		}
	}
	
	$sanitizedData = LC_sanitizeTimezoneDBData($data);
	if (count($sanitizedData) == 0) {
		redirectexit('action=admin;area=liveclock;sa=displaytimezones');
	} else {
		LC_updateTimeZones($sanitizedData);
		redirectexit('action=admin;area=liveclock;sa=displaytimezones');
	}
}

function LC_sanitizeTimezoneDBData($data = array()) {
	global $context;

	isAllowedTo('admin_forum');

	if (!is_array($data))
		return false;

	foreach ($data as $key => $val) {
		if (empty($val['zone_diff']) || empty($val['zone_name']) || $val['zone_diff'] < -12 || $val['zone_diff'] > 12) {
			unset($data[$key]);
		}
	}
	return $data;
}

function LC_resetAllTimeZones() {
	global $context;

	isAllowedTo('admin_forum');
	require_once('Subs-LiveClock.php');

	$data = LC_defaultTimezones();
	LC_resetDBTimezones($data);
	redirectexit('action=admin;area=liveclock;sa=displaytimezones');
}

function LC_defaultTimezones() {
	global $context;

	isAllowedTo('admin_forum');

	$live_clock_timezones = array(
		'Eniwetok, Kwajalein' => '-12',
		'Midway Island, Samoa' => '-11',
		'Hawaii' => '-10',
		'Alaska' => '-9',
		'Pacific Time (US &amp; Canada)' => '-8',
		'Mountain Time (US &amp; Canada)' => '-7',
		'Central Time (US &amp; Canada), Mexico City' => '-6',
		'Eastern Time (US &amp; Canada), Bogota, Lima' => '-5',
		'Atlantic Time (Canada), Caracas, La Paz' => '-4',
		'Newfoundland' => '-3.5',
		'Brazil, Buenos Aires, Georgetown' => '-3',
		'Mid-Atlantic' => '-2',
		'Azores, Cape Verde Islands' => '-1',
		'Western Europe Time, London, Lisbon, Casablanca' => '0',
		'Brussels, Copenhagen, Madrid, Paris' => '+1',
		'Kaliningrad, South Africa' => '+2',
		'Baghdad, Riyadh, Moscow, St. Petersburg' => '+3',
		'Tehran' => '+3.5',
		'Abu Dhabi, Muscat, Baku, Tbilisi' => '+4',
		'Kabul' => '+4.5',
		'Ekaterinburg, Islamabad, Karachi, Tashkent' => '+5',
		'Bombay, Calcutta, Madras, New Delhi' => '+5.5',
		'Kathmandu' => '+5.75',
		'Almaty, Dhaka, Colombo' => '+6',
		'Bangkok, Hanoi, Jakarta' => '+7',
		'Beijing, Perth, Singapore, Hong Kong' => '+8',
		'Tokyo, Seoul, Osaka, Sapporo, Yakutsk' => '+9',
		'Adelaide, Darwin' => '+9.5',
		'Eastern Australia, Guam, Vladivostok' => '+10',
		'Magadan, Solomon Islands, New Caledonia' => '+11',
		'Auckland, Wellington, Fiji, Kamchatka' => '+12',
	);
	return $live_clock_timezones;
}

?>