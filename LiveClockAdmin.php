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
		'savetimezones' => 'LC_saveTimezones'
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

?>